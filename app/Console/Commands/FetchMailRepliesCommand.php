<?php

namespace App\Console\Commands;

use App\Services\MailReplyService;
use Illuminate\Console\Command;

/**
 * Maildir の new/ ディレクトリを監視し、新着メールを処理するコマンド。
 *
 * .forward のパイプが使えない環境（さくらインターネット等）向け。
 * cron で毎分実行する:
 *   * * * * * /usr/local/bin/php /path/to/castup/artisan mail:fetch-replies
 */
class FetchMailRepliesCommand extends Command
{
    protected $signature = 'mail:fetch-replies
        {--maildir= : Maildir のパス（省略時は設定値を使用）}
        {--dry-run  : DBに保存せず処理内容のみ表示}';

    protected $description = 'Maildirの新着メールを処理してDBに保存する（cron用）';

    public function __construct(private readonly MailReplyService $mailReplyService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $maildir = $this->option('maildir')
            ?? env('REPLY_MAILDIR', storage_path('app/mail_reply/maildir'));

        $newDir = rtrim($maildir, '/') . '/new';
        $curDir = rtrim($maildir, '/') . '/cur';

        if (!is_dir($newDir)) {
            $this->error("Maildir/new が見つかりません: {$newDir}");
            return self::FAILURE;
        }

        $files = array_filter(glob($newDir . '/*') ?: [], 'is_file');

        if (empty($files)) {
            $this->line('新着メールなし。');
            return self::SUCCESS;
        }

        $this->info(count($files) . " 件の新着メールを処理します。");

        foreach ($files as $file) {
            $rawEmail = file_get_contents($file);

            if (empty(trim($rawEmail))) {
                continue;
            }

            $basename = basename($file);
            $this->info("処理中: {$basename}");

            if ($this->option('dry-run')) {
                $this->line('--- メール先頭200文字 ---');
                $this->line(mb_strimwidth($rawEmail, 0, 200, '...'));
                $this->line('--- ここまで（--dry-run のため保存しません）---');
                continue;
            }

            $this->mailReplyService->process($rawEmail);

            // 処理済みとして cur/ に移動（Maildir 規約）
            if (is_dir($curDir)) {
                rename($file, $curDir . '/' . $basename . ':2,S');
            } else {
                unlink($file);
            }
        }

        $this->info('完了。');
        return self::SUCCESS;
    }
}
