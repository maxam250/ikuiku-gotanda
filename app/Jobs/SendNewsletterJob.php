<?php

namespace App\Jobs;

use App\Models\NewsletterCampaign;
use App\Models\NewsletterSubscriber;
use App\Models\NewsletterLog;
use App\Mail\NewsletterCampaignMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;

class SendNewsletterJob implements ShouldQueue
{
    use Queueable, SerializesModels, InteractsWithQueue;

    public NewsletterCampaign $campaign;
    public NewsletterSubscriber $subscriber;

    /**
     * ジョブのタイムアウト時間（秒）
     */
    public $timeout = 120;

    /**
     * ジョブの試行回数
     */
    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(NewsletterCampaign $campaign, NewsletterSubscriber $subscriber)
    {
        $this->campaign = $campaign;
        $this->subscriber = $subscriber;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // 購読者が有効かチェック
        if (!$this->subscriber->isActive()) {
            $this->createLog('failed', '購読者が無効です');
            return;
        }

        try {
            // メール送信
            Mail::to($this->subscriber->email)->send(
                new NewsletterCampaignMail(
                    $this->subscriber,
                    $this->campaign->subject,
                    $this->campaign->body_text,
                    $this->campaign->body_html
                )
            );

            // ログ記録
            $this->createLog('sent');
            
            // キャンペーンの送信数を更新
            $this->campaign->increment('sent_count');
            
            // すべての送信が完了したかチェック
            $this->checkCampaignCompletion();
        } catch (\Exception $e) {
            \Log::error('Newsletter send failed', [
                'campaign_id' => $this->campaign->id,
                'subscriber_id' => $this->subscriber->id,
                'error' => $e->getMessage(),
            ]);

            $this->createLog('failed', $e->getMessage());
            $this->campaign->increment('failed_count');
            
            throw $e; // リトライのために例外を再スロー
        }
    }

    /**
     * ログを作成
     */
    protected function createLog(string $status, ?string $errorMessage = null): void
    {
        NewsletterLog::create([
            'campaign_id' => $this->campaign->id,
            'subscriber_id' => $this->subscriber->id,
            'status' => $status,
            'sent_at' => $status === 'sent' ? now() : null,
            'error_message' => $errorMessage,
        ]);
    }

    /**
     * ジョブが失敗した場合の処理
     */
    public function failed(\Throwable $exception): void
    {
        $this->createLog('failed', $exception->getMessage());
        $this->campaign->increment('failed_count');
        $this->checkCampaignCompletion();
    }

    /**
     * キャンペーンの完了をチェック
     */
    protected function checkCampaignCompletion(): void
    {
        // キャンペーンを再取得（最新の状態を取得）
        $campaign = NewsletterCampaign::find($this->campaign->id);
        
        if (!$campaign || $campaign->status !== 'sending') {
            return;
        }

        // 送信済み数 + 失敗数が送信先数に達した場合
        if (($campaign->sent_count + $campaign->failed_count) >= $campaign->total_recipients) {
            $campaign->status = 'completed';
            $campaign->sent_at = now();
            $campaign->save();
        }
    }
}
