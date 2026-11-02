<?php

namespace App\Console\Commands;

use App\Models\NewsletterCampaign;
use App\Models\NewsletterSubscriber;
use App\Jobs\SendNewsletterJob;
use Illuminate\Console\Command;
use Carbon\Carbon;

class ProcessScheduledNewsletters extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'newsletter:process-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '予約配信を処理し、配信予定時刻を過ぎたキャンペーンを配信開始';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('予約配信の処理を開始します...');

        // 配信予定時刻を過ぎた予約済みキャンペーンを取得
        $campaigns = NewsletterCampaign::where('status', 'scheduled')
            ->where('scheduled_at', '<=', now())
            ->get();

        $processedCount = 0;

        foreach ($campaigns as $campaign) {
            $this->info("キャンペーン #{$campaign->id} の配信を開始します...");

            // ステータスを送信中に変更
            $campaign->status = 'sending';
            $campaign->save();

            // 送信先を取得
            $subscribers = $this->getRecipients($campaign);

            // 同期実行（キューワーカー不要）
            foreach ($subscribers as $subscriber) {
                SendNewsletterJob::dispatchSync($campaign, $subscriber);
            }

            $processedCount++;
            $this->line("キャンペーン #{$campaign->id}: {$subscribers->count()}件のメールをキューに追加しました。");
        }

        $this->info("処理完了: {$processedCount}件のキャンペーンを配信開始しました。");

        return Command::SUCCESS;
    }

    /**
     * 送信先を取得
     */
    protected function getRecipients(NewsletterCampaign $campaign)
    {
        $query = NewsletterSubscriber::query();

        // フィルター適用
        $filterStatus = $campaign->filter_status ?? 'verified';
        switch ($filterStatus) {
            case 'verified':
                $query->whereNotNull('verified_at')->whereNull('unsubscribed_at');
                break;
            case 'unverified':
                $query->whereNull('verified_at');
                break;
            case 'all':
                // フィルターなし
                break;
        }

        return $query->get();
    }
}
