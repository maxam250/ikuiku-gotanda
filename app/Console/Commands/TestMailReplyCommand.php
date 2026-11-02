<?php

namespace App\Console\Commands;

use App\Models\Item;
use App\Models\Member;
use App\Models\Message;
use App\Models\MessageItem;
use App\Models\Shop;
use App\Services\MailReplyService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;

/**
 * ローカル環境でメール返信受信をシミュレートするテスト用コマンド。
 *
 * 使い方:
 *   php artisan mail:test-reply                          # 最新の会員スレッドを使用
 *   php artisan mail:test-reply --type=cast              # 最新のキャストスレッドを使用
 *   php artisan mail:test-reply --uuid=<UUID>            # UUID指定
 *   php artisan mail:test-reply --body="返信テキスト"    # 本文指定
 *   php artisan mail:test-reply --dry-run               # メール本文を表示するだけ（DBに保存しない）
 */
class TestMailReplyCommand extends Command
{
    protected $signature = 'mail:test-reply
        {--type=member : スレッドの種類 (member / cast)}
        {--uuid=       : スレッドUUID（省略時はDBから最新取得）}
        {--body=       : メール本文（省略時はデフォルトテキスト）}
        {--dry-run     : DBに保存せず生成されるメール本文のみ表示}';

    protected $description = 'ローカルでメール返信受信をシミュレート（DBに保存）';

    public function __construct(private readonly MailReplyService $mailReplyService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $type = $this->option('type');
        $uuid = $this->option('uuid');
        $body = $this->option('body') ?: "これはテスト返信です。\n\nよろしくお願いいたします。";
        $isDryRun = $this->option('dry-run');

        // ① スレッド取得
        [$original, $tableName] = $this->findThread($type, $uuid);
        if ($original === null) {
            $this->error('対象スレッドが見つかりませんでした。');
            $this->line('  → messages/message_items テーブルに thread_uuid が設定された admin 送信メッセージが必要です。');
            return self::FAILURE;
        }

        $this->info("スレッド取得: table={$tableName}, uuid={$original->thread_uuid}");

        // ② 返信者のメールアドレス取得（receiver = 会員 or キャスト）
        $fromEmail = $this->getReceiverEmail($original, $tableName);
        if ($fromEmail === null) {
            $this->error('返信者（受信者）のメールアドレスが取得できませんでした。');
            return self::FAILURE;
        }

        $this->info("返信者メール: {$fromEmail}");

        // ③ Shop設定取得
        $shop = Shop::first();
        if (!$shop || !$shop->shop_domain) {
            $this->error('shops テーブルに shop_domain が設定されていません。');
            return self::FAILURE;
        }

        $toAddress    = "reply@{$shop->shop_domain}";
        $threadUuid   = $original->thread_uuid;
        $subject      = "Re: テストメッセージ [#{$threadUuid}]";

        // ④ 生メール文字列を構築
        $rawEmail = $this->buildRawEmail($fromEmail, $toAddress, $subject, $threadUuid, $body, $shop->shop_domain);

        // --dry-run の場合はメール本文を表示して終了
        if ($isDryRun) {
            $this->line('');
            $this->info('=== 生成されたテストメール（--dry-run）===');
            $this->line($rawEmail);
            $this->info('=== ここまで ===');
            $this->line('DBには保存しません。--dry-run を外すと実際に保存されます。');
            return self::SUCCESS;
        }

        // ⑤ MailReplyService でDB保存
        $this->info('MailReplyService::process() を実行します...');
        $this->mailReplyService->process($rawEmail);

        $this->info('完了。storage/logs/mail_receive.log を確認してください。');
        $this->line('  tail -50 ' . storage_path('logs/mail_receive.log'));

        // ⑥ 保存結果を確認表示
        $this->showSavedReply($tableName, $threadUuid);

        return self::SUCCESS;
    }

    // ──────────────────────────────────────────────
    // スレッド検索
    // ──────────────────────────────────────────────

    private function findThread(string $type, ?string $uuid): array
    {
        if ($type === 'cast') {
            $query = MessageItem::where('sender_type', 'admin')
                ->whereNotNull('thread_uuid');
            if ($uuid) {
                $query->where('thread_uuid', $uuid);
            }
            $original = $query->latest()->first();
            return [$original, 'message_items'];
        }

        // default: member
        $query = Message::where('sender_type', 'admin')
            ->whereNotNull('thread_uuid');
        if ($uuid) {
            $query->where('thread_uuid', $uuid);
        }
        $original = $query->latest()->first();
        return [$original, 'messages'];
    }

    // ──────────────────────────────────────────────
    // 返信者メール取得
    // ──────────────────────────────────────────────

    private function getReceiverEmail(Message|MessageItem $original, string $tableName): ?string
    {
        if ($tableName === 'messages') {
            $member = Member::find($original->receiver_id);
            if (!$member) {
                return null;
            }
            return $this->decryptEmail($member->getRawOriginal('email'));
        }

        $cast = Item::find($original->receiver_id);
        if (!$cast) {
            return null;
        }
        return $this->decryptEmail($cast->getRawOriginal('email'));
    }

    private function decryptEmail(string $raw): string
    {
        try {
            return strtolower(Crypt::decryptString($raw));
        } catch (\Exception) {
            return strtolower($raw);
        }
    }

    // ──────────────────────────────────────────────
    // 生メール構築
    // ──────────────────────────────────────────────

    private function buildRawEmail(
        string $from,
        string $to,
        string $subject,
        string $uuid,
        string $body,
        string $shopDomain
    ): string {
        $date = now()->format('D, d M Y H:i:s O');

        return implode("\r\n", [
            "From: {$from}",
            "To: {$to}",
            "Subject: {$subject}",
            "Date: {$date}",
            "In-Reply-To: <{$uuid}@{$shopDomain}>",
            "X-Thread-ID: {$uuid}",
            "MIME-Version: 1.0",
            "Content-Type: text/plain; charset=UTF-8",
            "Content-Transfer-Encoding: 8bit",
            "",
            $body,
        ]);
    }

    // ──────────────────────────────────────────────
    // 保存結果確認
    // ──────────────────────────────────────────────

    private function showSavedReply(string $tableName, string $uuid): void
    {
        $this->line('');

        if ($tableName === 'messages') {
            $reply = Message::where('thread_uuid', $uuid)
                ->where('sender_type', 'member')
                ->latest()
                ->first();
        } else {
            $reply = MessageItem::where('thread_uuid', $uuid)
                ->where('sender_type', 'item')
                ->latest()
                ->first();
        }

        if ($reply) {
            $this->info('DB保存確認: 返信レコードが見つかりました。');
            $this->table(
                ['id', 'thread_uuid', 'sender_type', 'sender_id', 'message'],
                [[
                    $reply->id,
                    $reply->thread_uuid,
                    $reply->sender_type,
                    $reply->sender_id,
                    mb_strimwidth($reply->message, 0, 60, '...'),
                ]]
            );
        } else {
            $this->warn('DB保存確認: 返信レコードが見つかりません。ログを確認してください。');
        }
    }
}
