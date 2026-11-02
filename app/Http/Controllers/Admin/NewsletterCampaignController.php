<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsletterCampaign;
use App\Models\NewsletterTemplate;
use App\Models\NewsletterSubscriber;
use App\Models\NewsletterLog;
use App\Jobs\SendNewsletterJob;
use App\Mail\NewsletterCampaignMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Artisan;
use Carbon\Carbon;

class NewsletterCampaignController extends Controller
{
    /**
     * キャンペーン一覧
     */
    public function index()
    {
        $campaigns = NewsletterCampaign::with('template')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('admin.newsletter.campaigns.index', compact('campaigns'));
    }

    /**
     * キャンペーン作成フォーム
     */
    public function create(Request $request)
    {
        $templates = NewsletterTemplate::orderBy('name')->get();
        $templateId = $request->get('template_id');
        
        return view('admin.newsletter.campaigns.create', compact('templates', 'templateId'));
    }

    /**
     * キャンペーン保存
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'template_id' => 'nullable|exists:newsletter_templates,id',
            'subject' => 'required|string|max:255',
            'body_text' => 'nullable|string',
            'body_html' => 'nullable|string',
            'scheduled_at' => 'nullable|date|after:now',
            'filter_status' => 'nullable|string|in:all,verified,unverified',
        ], [
            'subject.required' => '件名を入力してください。',
            'scheduled_at.after' => '配信予定日時は現在より後の日時を指定してください。',
        ]);

        $campaign = NewsletterCampaign::create([
            'template_id' => $validated['template_id'] ?? null,
            'subject' => $validated['subject'],
            'body_text' => $validated['body_text'] ?? null,
            'body_html' => $validated['body_html'] ?? null,
            'scheduled_at' => $validated['scheduled_at'] ? Carbon::parse($validated['scheduled_at']) : null,
            'status' => $validated['scheduled_at'] ? 'scheduled' : 'draft',
            'filter_status' => $validated['filter_status'] ?? 'verified',
        ]);

        // 送信先数を計算
        $campaign->total_recipients = $this->getRecipientCount($campaign);
        $campaign->save();

        return redirect()->route('admin.newsletter.campaigns.show', $campaign->id)
            ->with('success', 'キャンペーンを作成しました。');
    }

    /**
     * キャンペーン詳細
     */
    public function show($id)
    {
        $campaign = NewsletterCampaign::with(['template', 'logs.subscriber'])
            ->findOrFail($id);
        
        return view('admin.newsletter.campaigns.show', compact('campaign'));
    }

    /**
     * キャンペーン編集フォーム
     */
    public function edit($id)
    {
        $campaign = NewsletterCampaign::findOrFail($id);
        
        // 送信済みの場合は編集不可
        if ($campaign->isCompleted() || $campaign->isSending()) {
            return redirect()->route('admin.newsletter.campaigns.show', $campaign->id)
                ->with('error', '送信済みまたは送信中のキャンペーンは編集できません。');
        }

        $templates = NewsletterTemplate::orderBy('name')->get();
        
        return view('admin.newsletter.campaigns.edit', compact('campaign', 'templates'));
    }

    /**
     * キャンペーン更新
     */
    public function update(Request $request, $id)
    {
        $campaign = NewsletterCampaign::findOrFail($id);

        // 送信済みの場合は更新不可
        if ($campaign->isCompleted() || $campaign->isSending()) {
            return redirect()->route('admin.newsletter.campaigns.show', $campaign->id)
                ->with('error', '送信済みまたは送信中のキャンペーンは編集できません。');
        }

        $validated = $request->validate([
            'template_id' => 'nullable|exists:newsletter_templates,id',
            'subject' => 'required|string|max:255',
            'body_text' => 'nullable|string',
            'body_html' => 'nullable|string',
            'scheduled_at' => 'nullable|date|after:now',
            'filter_status' => 'nullable|string|in:all,verified,unverified',
        ], [
            'subject.required' => '件名を入力してください。',
            'scheduled_at.after' => '配信予定日時は現在より後の日時を指定してください。',
        ]);

        $campaign->update([
            'template_id' => $validated['template_id'] ?? null,
            'subject' => $validated['subject'],
            'body_text' => $validated['body_text'] ?? null,
            'body_html' => $validated['body_html'] ?? null,
            'scheduled_at' => $validated['scheduled_at'] ? Carbon::parse($validated['scheduled_at']) : null,
            'status' => $validated['scheduled_at'] ? 'scheduled' : 'draft',
            'filter_status' => $validated['filter_status'] ?? 'verified',
        ]);

        // 送信先数を再計算
        $campaign->total_recipients = $this->getRecipientCount($campaign);
        $campaign->save();

        return redirect()->route('admin.newsletter.campaigns.show', $campaign->id)
            ->with('success', 'キャンペーンを更新しました。');
    }

    /**
     * キャンペーン削除
     */
    public function destroy($id)
    {
        $campaign = NewsletterCampaign::findOrFail($id);

        // 送信済みまたは送信中の場合は削除不可
        if ($campaign->isCompleted() || $campaign->isSending()) {
            return redirect()->route('admin.newsletter.campaigns.index')
                ->with('error', '送信済みまたは送信中のキャンペーンは削除できません。');
        }

        $campaign->delete();

        return redirect()->route('admin.newsletter.campaigns.index')
            ->with('success', 'キャンペーンを削除しました。');
    }

    /**
     * 即座に配信
     */
    public function send($id)
    {
        $campaign = NewsletterCampaign::findOrFail($id);

        if (!in_array($campaign->status, ['draft', 'scheduled'])) {
            return redirect()->route('admin.newsletter.campaigns.show', $campaign->id)
                ->with('error', 'このキャンペーンは配信できません。（送信済み・送信中・キャンセル済みは再送できません）');
        }

        $campaign->status = 'sending';
        $campaign->scheduled_at = now();
        $campaign->save();

        // 送信先を取得
        $subscribers = $this->getRecipients($campaign);

        // 同期実行（キューワーカー不要）
        foreach ($subscribers as $subscriber) {
            SendNewsletterJob::dispatchSync($campaign, $subscriber);
        }

        return redirect()->route('admin.newsletter.campaigns.show', $campaign->id)
            ->with('success', '配信が完了しました。');
    }

    /**
     * テスト送信
     */
    public function testSend(Request $request, $id)
    {
        $campaign = NewsletterCampaign::findOrFail($id);

        $validated = $request->validate([
            'test_email' => 'required|email',
        ], [
            'test_email.required' => 'テスト送信先のメールアドレスを入力してください。',
            'test_email.email' => '有効なメールアドレスを入力してください。',
        ]);

        try {
            // テスト用の購読者オブジェクトを作成
            $testSubscriber = new NewsletterSubscriber();
            $testSubscriber->email = $validated['test_email'];
            $testSubscriber->token = NewsletterSubscriber::generateToken();

            Mail::to($validated['test_email'])->send(
                new NewsletterCampaignMail(
                    $testSubscriber,
                    $campaign->subject,
                    $campaign->body_text,
                    $campaign->body_html
                )
            );

            return redirect()->route('admin.newsletter.campaigns.show', $campaign->id)
                ->with('success', 'テストメールを送信しました。');
        } catch (\Exception $e) {
            \Log::error('Test email send failed', [
                'campaign_id' => $campaign->id,
                'email' => $validated['test_email'],
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('admin.newsletter.campaigns.show', $campaign->id)
                ->with('error', 'テストメールの送信に失敗しました。');
        }
    }

    /**
     * キャンペーンキャンセル
     */
    public function cancel($id)
    {
        $campaign = NewsletterCampaign::findOrFail($id);

        if ($campaign->status !== 'scheduled') {
            return redirect()->route('admin.newsletter.campaigns.show', $campaign->id)
                ->with('error', '予約済みのキャンペーンのみキャンセルできます。');
        }

        $campaign->status = 'cancelled';
        $campaign->save();

        return redirect()->route('admin.newsletter.campaigns.show', $campaign->id)
            ->with('success', 'キャンペーンをキャンセルしました。');
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

    /**
     * 送信先数を取得
     */
    protected function getRecipientCount(NewsletterCampaign $campaign): int
    {
        return $this->getRecipients($campaign)->count();
    }

    /**
     * 「送信中」で止まったキャンペーンを下書きにリセット
     */
    public function resetSending($id)
    {
        $campaign = NewsletterCampaign::findOrFail($id);

        if ($campaign->status !== 'sending') {
            return redirect()->route('admin.newsletter.campaigns.show', $campaign->id)
                ->with('error', '送信中のキャンペーンのみリセットできます。');
        }

        $campaign->status = 'draft';
        $campaign->sent_count = 0;
        $campaign->save();

        return redirect()->route('admin.newsletter.campaigns.show', $campaign->id)
            ->with('success', 'キャンペーンを下書きにリセットしました。再度送信操作を行ってください。');
    }

    /**
     * 予約配信処理（手動実行）
     */
    public function processScheduled()
    {
        try {
            Artisan::call('newsletter:process-scheduled');
            $output = Artisan::output();
            
            $detail = $output ? ' ' . trim(strip_tags($output)) : '';
            return redirect()->route('admin.newsletter.campaigns.index')
                ->with('success', '予約配信の処理を実行しました。' . $detail);
        } catch (\Exception $e) {
            \Log::error('Process scheduled newsletters failed', [
                'error' => $e->getMessage(),
            ]);
            
            return redirect()->route('admin.newsletter.campaigns.index')
                ->with('error', '予約配信の処理に失敗しました: ' . $e->getMessage());
        }
    }

}
