<?php

namespace App\Console\Commands;

use App\Models\NewsletterLog;
use App\Models\NewsletterSubscriber;
use Illuminate\Console\Command;

class ProcessBouncedNewsletters extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'newsletter:process-bounced';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'バウンスメールを処理し、該当する購読者を自動削除';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('バウンスメールの処理を開始します...');

        // バウンスログを取得
        $bouncedLogs = NewsletterLog::where('status', 'bounced')
            ->whereDoesntHave('subscriber', function ($query) {
                $query->whereNotNull('unsubscribed_at');
            })
            ->with('subscriber')
            ->get();

        $deletedCount = 0;

        foreach ($bouncedLogs as $log) {
            if ($log->subscriber) {
                // 購読者を削除
                $log->subscriber->delete();
                $deletedCount++;
                
                $this->line("購読者を削除しました: {$log->subscriber->email}");
            }
        }

        $this->info("処理完了: {$deletedCount}件の購読者を削除しました。");

        return Command::SUCCESS;
    }
}
