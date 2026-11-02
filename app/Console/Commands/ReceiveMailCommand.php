<?php

namespace App\Console\Commands;

use App\Services\MailReplyService;
use Illuminate\Console\Command;

/**
 * メール受信 Artisan コマンド。
 *
 * さくらインターネットの .forward から以下のように呼び出す:
 *   "| /usr/local/bin/php /home/username/public_html/artisan mail:receive"
 *
 * 受信したメール全文を stdin から読み込み、MailReplyService に委譲する。
 * Fat Command を避けるため、ビジネスロジックは Service 層に集約する。
 */
class ReceiveMailCommand extends Command
{
    protected $signature = 'mail:receive';

    protected $description = 'メール返信を標準入力から受け取りDBに保存する';

    public function __construct(private readonly MailReplyService $mailReplyService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        // .forward はメール全文を標準入力に渡す
        $rawEmail = $this->readStdin();

        if (empty(trim($rawEmail))) {
            $this->error('No email data received from stdin.');
            return self::FAILURE;
        }

        $this->mailReplyService->process($rawEmail);

        // MTA に対して 0 を返すことが重要（0 以外だとバウンスメールが生成される場合がある）
        return self::SUCCESS;
    }

    /**
     * 標準入力からメール全文を読み込む。
     * php://stdin はストリームなので file_get_contents で一括取得する。
     */
    private function readStdin(): string
    {
        $handle = fopen('php://stdin', 'r');
        if ($handle === false) {
            return '';
        }

        $content = '';
        while (!feof($handle)) {
            $content .= fread($handle, 8192);
        }
        fclose($handle);

        return $content;
    }
}
