<?php

namespace App\Services;

use App\Models\Item;
use App\Models\Member;
use App\Models\Message;
use App\Models\MessageItem;
use App\Models\Shop;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

/**
 * メール返信をDBに保存するサービス。
 *
 * セキュリティ設計:
 *  - To / Delivered-To / X-Original-To 等いずれかに reply@{shop_domain} が含まれない場合は即終了（転送環境対応）
 *  - UUID は In-Reply-To → References → X-Thread-ID の優先順で取得
 *  - UUID一致 AND Fromメール一致 の両方が成立しないと保存しない
 *  - HTML は strip_tags で除去（text/plain 優先）
 *  - 本文は最大 10KB に制限
 */
class MailReplyService
{
    private const LOG_CHANNEL  = 'mail_receive';
    private const MAX_BODY_LEN = 10 * 1024; // 10KB

    /**
     * 標準入力から取得した生メールを解析してDB保存する。
     */
    public function process(string $rawEmail): void
    {
        // ① メール解析
        try {
            $parsed = $this->parseMail($rawEmail);
        } catch (\Throwable $e) {
            $this->log('error', 'parse error: ' . $e->getMessage());
            return;
        }

        $fromEmail = $parsed['from'];
        $toAddress = $parsed['to'];
        $subject   = $parsed['subject'];
        $body      = $parsed['body'];
        $headers   = $parsed['headers'];

        // ② スパム防御: reply@{shop_domain} が宛先系ヘッダに含むこと（.forward / 転送で To が変わる場合あり）
        $shop = Shop::first();
        $shopDomain = ($shop && $shop->shop_domain) ? strtolower(trim((string) $shop->shop_domain)) : '';

        if ($shopDomain === '' || ! $this->inboundIndicatesReplyMailbox($headers, $toAddress, $shopDomain)) {
            $this->log(
                'info',
                'ignored: reply@ not in recipient headers. to=' . $toAddress
                . ' delivered-to=' . ($headers['delivered-to'] ?? '')
            );
            return;
        }

        if (empty($fromEmail)) {
            $this->log('error', 'From header is empty or unparseable');
            return;
        }

        // ③ UUID抽出: In-Reply-To → References → X-Thread-ID
        $uuid = $this->extractUuid($headers, $subject);
        if ($uuid === null) {
            $this->log('error', 'uuid not found in In-Reply-To / References / X-Thread-ID');
            return;
        }

        // ④ UUID でスレッドを特定
        $thread = $this->findThread($uuid);
        if ($thread === null) {
            $this->log('error', 'uuid not found in DB: ' . $uuid);
            return;
        }

        // ⑤ 本文サニタイズ・サイズ制限
        $body = $this->cleanBody($body);
        if (trim($body) === '') {
            $this->log('error', 'body is empty after sanitize');
            return;
        }

        [$table, $original] = $thread;

        // ⑥ 送信者特定 → 保存
        if ($table === 'messages') {
            $this->saveMemberReply($uuid, $original, $fromEmail, $body);
        } else {
            $this->saveCastReply($uuid, $original, $fromEmail, $body);
        }
    }

    // ──────────────────────────────────────────────
    // To 検証（reply@{shop_domain} 固定）
    // ──────────────────────────────────────────────

    /**
     * MTA が To を書き換えた場合でも、Delivered-To 等に元の reply@ が残ることがある。
     */
    private function inboundIndicatesReplyMailbox(array $headers, string $toAddress, string $shopDomainLower): bool
    {
        $chunks = [$toAddress];
        foreach (['delivered-to', 'x-original-to', 'envelope-to'] as $key) {
            if (! empty($headers[$key])) {
                $chunks[] = $headers[$key];
            }
        }
        foreach ($chunks as $chunk) {
            if ($this->recipientListContainsReplyMailbox($chunk, $shopDomainLower)) {
                return true;
            }
        }

        return false;
    }

    private function recipientListContainsReplyMailbox(string $toHeader, string $shopDomainLower): bool
    {
        $target = 'reply@' . $shopDomainLower;
        foreach ($this->addressesFromRecipientHeader($toHeader) as $addr) {
            if ($addr === $target) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return list<string>
     */
    private function addressesFromRecipientHeader(string $header): array
    {
        $out = [];
        foreach ($this->splitAddressList($header) as $chunk) {
            $addr = $this->extractEmailAddress($chunk);
            if ($addr !== '') {
                $out[] = $addr;
            }
        }

        return array_values(array_unique($out));
    }

    /**
     * @return list<string>
     */
    private function splitAddressList(string $list): array
    {
        $list = trim($list);
        if ($list === '') {
            return [];
        }

        $parts   = [];
        $buf     = '';
        $len     = strlen($list);
        $inQuote = false;

        for ($i = 0; $i < $len; $i++) {
            $c = $list[$i];
            if ($c === '"') {
                $inQuote = ! $inQuote;
            }
            if ($c === ',' && ! $inQuote) {
                $t = trim($buf);
                if ($t !== '') {
                    $parts[] = $t;
                }
                $buf = '';

                continue;
            }
            $buf .= $c;
        }

        $t = trim($buf);
        if ($t !== '') {
            $parts[] = $t;
        }

        return $parts;
    }

    // ──────────────────────────────────────────────
    // UUID抽出
    // ──────────────────────────────────────────────

    /**
     * UUID を以下の優先順で取得する。
     *   1. In-Reply-To ヘッダ  <UUID@domain> 形式
     *   2. References ヘッダ   同上
     *   3. X-Thread-ID ヘッダ  カスタムヘッダ（一部クライアントで削除される場合あり）
     *
     * UUID形式: 8-4-4-4-12 の小文字16進数
     */
    private function extractUuid(array $headers, string $subject): ?string
    {
        $uuidPattern = '[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}';

        // Priority 1: In-Reply-To ヘッダ（<UUID@domain> 形式）
        $inReplyTo = $headers['in-reply-to'] ?? null;
        if ($inReplyTo) {
            if (preg_match('/(' . $uuidPattern . ')@/i', $inReplyTo, $m)) {
                return strtolower($m[1]);
            }
        }

        // Priority 2: References ヘッダ（同形式）
        $references = $headers['references'] ?? null;
        if ($references) {
            if (preg_match('/(' . $uuidPattern . ')@/i', $references, $m)) {
                return strtolower($m[1]);
            }
        }

        // Priority 3: X-Thread-ID ヘッダ
        $xThreadId = $headers['x-thread-id'] ?? null;
        if ($xThreadId) {
            $candidate = strtolower(trim($xThreadId));
            if (preg_match('/^' . $uuidPattern . '$/', $candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    // ──────────────────────────────────────────────
    // スレッド検索
    // ──────────────────────────────────────────────

    /**
     * UUID に対応する元メッセージを messages → message_items の順に検索。
     * 管理者が送った（sender_type='admin'）メッセージのみ対象。
     *
     * @return array{0: string, 1: Message|MessageItem}|null
     */
    private function findThread(string $uuid): ?array
    {
        $msg = Message::where('thread_uuid', $uuid)
            ->where('sender_type', 'admin')
            ->first();
        if ($msg) {
            return ['messages', $msg];
        }

        $item = MessageItem::where('thread_uuid', $uuid)
            ->where('sender_type', 'admin')
            ->first();
        if ($item) {
            return ['message_items', $item];
        }

        return null;
    }

    // ──────────────────────────────────────────────
    // 返信保存
    // ──────────────────────────────────────────────

    private function saveMemberReply(
        string $uuid,
        Message $original,
        string $fromEmail,
        string $body
    ): void {
        // なりすまし防止: From が元メッセージの宛先（会員）と一致するか確認
        $member = $this->findMemberByEmail($fromEmail);
        if ($member === null) {
            $this->log('warning', "unknown sender (member): {$fromEmail}");
            return;
        }

        // UUID一致 + 受信者ID一致 の両方を満たすこと
        if ($original->receiver_id !== $member->id) {
            $this->log('warning', "member ID mismatch: uuid={$uuid} member_id={$member->id}");
            return;
        }

        Message::create([
            'thread_uuid'   => $uuid,
            'sender_id'     => $member->id,
            'sender_type'   => 'member',
            'receiver_id'   => $original->sender_id,
            'receiver_type' => 'admin',
            'message'       => $body,
            'title'         => null,
        ]);

        $this->log('info', "success: member reply saved member_id={$member->id} uuid={$uuid}");
    }

    private function saveCastReply(
        string $uuid,
        MessageItem $original,
        string $fromEmail,
        string $body
    ): void {
        $cast = $this->findCastByEmail($fromEmail);
        if ($cast === null) {
            $this->log('warning', "unknown sender (cast): {$fromEmail}");
            return;
        }

        if ($original->receiver_id !== $cast->id) {
            $this->log('warning', "cast ID mismatch: uuid={$uuid} cast_id={$cast->id}");
            return;
        }

        MessageItem::create([
            'thread_uuid'   => $uuid,
            'sender_id'     => $cast->id,
            'sender_type'   => 'item',
            'receiver_id'   => $original->sender_id,
            'receiver_type' => 'admin',
            'message'       => $body,
            'title'         => null,
        ]);

        $this->log('info', "success: cast reply saved item_id={$cast->id} uuid={$uuid}");
    }

    // ──────────────────────────────────────────────
    // ユーザー照合（暗号化メール対応）
    // ──────────────────────────────────────────────

    /**
     * 会員テーブルを全件走査し、復号メールアドレスで照合。
     * DB の WHERE email = ? は暗号化のため使用禁止。
     */
    private function findMemberByEmail(string $email): ?Member
    {
        $email = strtolower($email);

        foreach (Member::all() as $member) {
            try {
                $decrypted = strtolower(Crypt::decryptString($member->getRawOriginal('email')));
            } catch (\Exception $e) {
                $decrypted = strtolower((string) $member->getRawOriginal('email'));
            }

            if ($decrypted === $email) {
                return $member;
            }
        }

        return null;
    }

    /**
     * キャスト（items）テーブルを全件走査し、復号メールアドレスで照合。
     * Item モデルは 'email' => 'encrypted' キャストを持つが、
     * getRawOriginal で暗号値を取得してから Crypt::decryptString で復号する。
     */
    private function findCastByEmail(string $email): ?Item
    {
        $email = strtolower($email);

        foreach (Item::all() as $item) {
            try {
                $decrypted = strtolower(Crypt::decryptString($item->getRawOriginal('email')));
            } catch (\Exception $e) {
                $decrypted = strtolower((string) $item->getRawOriginal('email'));
            }

            if ($decrypted === $email) {
                return $item;
            }
        }

        return null;
    }

    // ──────────────────────────────────────────────
    // メール解析（外部ライブラリ不使用）
    // ──────────────────────────────────────────────

    /**
     * 生メール文字列を解析して from / to / subject / body / headers を返す。
     *
     * @return array{from: string, to: string, subject: string, body: string, headers: array<string, string>}
     * @throws \RuntimeException
     */
    private function parseMail(string $raw): array
    {
        $raw = str_replace(["\r\n", "\r"], "\n", $raw);

        $sep = strpos($raw, "\n\n");
        if ($sep === false) {
            throw new \RuntimeException('Cannot split headers from body');
        }

        $headerSection = substr($raw, 0, $sep);
        $bodySection   = substr($raw, $sep + 2);

        $headers = $this->parseHeaders($headerSection);

        $contentType      = $headers['content-type'] ?? 'text/plain';
        $transferEncoding = $headers['content-transfer-encoding'] ?? '';

        $textBody = $this->extractPlainText($bodySection, $contentType, $transferEncoding);

        return [
            'from'    => $this->extractEmailAddress($headers['from'] ?? ''),
            'to'      => $headers['to'] ?? '',
            'subject' => $this->decodeMimeHeader($headers['subject'] ?? ''),
            'body'    => $textBody,
            'headers' => $headers, // X-Thread-ID 取得に使用
        ];
    }

    /**
     * ヘッダーセクション文字列を小文字キーの連想配列にパース。
     * RFC 2822 の folding（継続行）に対応。
     */
    private function parseHeaders(string $section): array
    {
        $unfolded = preg_replace('/\n[ \t]+/', ' ', $section);
        $headers  = [];

        foreach (explode("\n", $unfolded) as $line) {
            $pos = strpos($line, ':');
            if ($pos === false) {
                continue;
            }
            $name           = strtolower(trim(substr($line, 0, $pos)));
            $headers[$name] = trim(substr($line, $pos + 1));
        }

        return $headers;
    }

    /**
     * ボディから text/plain を優先して抽出。
     * multipart の場合は再帰的に解析する。
     */
    private function extractPlainText(string $body, string $contentType, string $transferEncoding): string
    {
        $type = strtolower(strtok($contentType, ';'));

        if ($type === 'text/plain') {
            $decoded = $this->decodeTransfer(trim($body), $transferEncoding);
            return $this->convertToUtf8($decoded, $contentType);
        }

        if ($type === 'text/html') {
            $decoded = $this->decodeTransfer(trim($body), $transferEncoding);
            return strip_tags($this->convertToUtf8($decoded, $contentType));
        }

        if (str_starts_with($type, 'multipart/')) {
            if (preg_match('/boundary="?([^";]+)"?/i', $contentType, $m)) {
                return $this->parseMultipart($body, trim($m[1]));
            }
        }

        return trim($body);
    }

    /**
     * multipart ボディを境界線で分割し、text/plain パートを返す。
     */
    private function parseMultipart(string $body, string $boundary): string
    {
        $delimiter  = '--' . $boundary;
        $parts      = explode($delimiter, $body);
        $textResult = '';

        foreach ($parts as $part) {
            $part = ltrim($part, "\n");
            if (str_starts_with(trim($part), '--')) {
                continue;
            }

            $pos = strpos($part, "\n\n");
            if ($pos === false) {
                continue;
            }

            $partHeaders  = $this->parseHeaders(substr($part, 0, $pos));
            $partBody     = substr($part, $pos + 2);
            $partType     = strtolower(strtok($partHeaders['content-type'] ?? 'text/plain', ';'));
            $partEncoding = $partHeaders['content-transfer-encoding'] ?? '';

            if ($partType === 'text/plain') {
                $decoded = $this->decodeTransfer(trim($partBody), $partEncoding);
                return $this->convertToUtf8($decoded, $partHeaders['content-type'] ?? '');
            }

            if ($partType === 'text/html' && $textResult === '') {
                $decoded = $this->decodeTransfer(trim($partBody), $partEncoding);
                $textResult = strip_tags($this->convertToUtf8($decoded, $partHeaders['content-type'] ?? ''));
            }

            if (str_starts_with($partType, 'multipart/')) {
                if (preg_match('/boundary="?([^";]+)"?/i', $partHeaders['content-type'] ?? '', $m)) {
                    $nested = $this->parseMultipart($partBody, trim($m[1]));
                    if ($nested !== '') {
                        return $nested;
                    }
                }
            }
        }

        return $textResult;
    }

    /**
     * Content-Transfer-Encoding に応じてボディをデコード。
     */
    private function decodeTransfer(string $body, string $encoding): string
    {
        return match (strtolower(trim($encoding))) {
            'base64'           => (string) base64_decode(preg_replace('/\s+/', '', $body)),
            'quoted-printable' => quoted_printable_decode($body),
            default            => $body,
        };
    }

    /**
     * Content-Type ヘッダから charset を抽出し、UTF-8 に変換する。
     * iso-2022-jp / shift_jis / euc-jp 等の日本語文字コードに対応。
     * charset が未指定または既に UTF-8 の場合はそのまま返す。
     */
    private function convertToUtf8(string $body, string $contentType): string
    {
        if (preg_match('/charset=["\']?([^"\';,\s]+)["\']?/i', $contentType, $m)) {
            $charset = strtolower(trim($m[1]));
            if (!in_array($charset, ['utf-8', 'utf8'], true)) {
                $converted = mb_convert_encoding($body, 'UTF-8', strtoupper($m[1]));
                if ($converted !== false && $converted !== '') {
                    return $converted;
                }
            }
        }
        return $body;
    }

    /**
     * RFC 2047 エンコードされたヘッダー値をデコード。
     */
    private function decodeMimeHeader(string $value): string
    {
        return (string) mb_decode_mimeheader($value);
    }

    /**
     * "Display Name <user@example.com>" または "user@example.com" からアドレスを抽出。
     */
    private function extractEmailAddress(string $from): string
    {
        if (preg_match('/<([^>@\s]+@[^>]+)>/', $from, $m)) {
            return strtolower(trim($m[1]));
        }

        $candidate = trim($from);
        if (filter_var($candidate, FILTER_VALIDATE_EMAIL)) {
            return strtolower($candidate);
        }

        return strtolower($candidate);
    }

    // ──────────────────────────────────────────────
    // 本文クリーニング
    // ──────────────────────────────────────────────

    /**
     * 本文を安全にサニタイズする。
     *  - HTML タグを除去
     *  - 引用返信部分（> 記法 / "wrote:" 以降）を除去
     *  - 最大 10KB に切り詰め
     */
    private function cleanBody(string $body): string
    {
        $body  = strip_tags($body);
        $lines = explode("\n", $body);
        $clean = [];

        foreach ($lines as $line) {
            if (preg_match('/^\s*>/', $line)) {
                continue;
            }
            if (preg_match('/^On .+wrote:/i', $line)) {
                break;
            }
            if (preg_match('/^-{5,}.*-{5,}$/i', trim($line))) {
                break;
            }
            $clean[] = $line;
        }

        $body = trim(implode("\n", $clean));

        if (strlen($body) > self::MAX_BODY_LEN) {
            $body = mb_strcut($body, 0, self::MAX_BODY_LEN);
        }

        return $body;
    }

    // ──────────────────────────────────────────────
    // ロギング
    // ──────────────────────────────────────────────

    private function log(string $level, string $message): void
    {
        Log::channel(self::LOG_CHANNEL)->{$level}('[MailReply] ' . $message);
    }
}
