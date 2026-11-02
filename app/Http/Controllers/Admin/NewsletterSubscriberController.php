<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;

class NewsletterSubscriberController extends Controller
{

    /**
     * 購読者一覧
     */
    public function index(Request $request)
    {
        $query = NewsletterSubscriber::query();

        // 検索（メールアドレスのハッシュで検索）
        if ($request->filled('search')) {
            $search = strtolower(trim($request->search));
            $hash = hash('sha256', $search);
            $query->where('email_hash', $hash);
        }

        // ステータスフィルター
        if ($request->filled('status')) {
            switch ($request->status) {
                case 'verified':
                    $query->whereNotNull('verified_at')->whereNull('unsubscribed_at');
                    break;
                case 'unverified':
                    $query->whereNull('verified_at');
                    break;
                case 'unsubscribed':
                    $query->whereNotNull('unsubscribed_at');
                    break;
            }
        }

        $subscribers = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.newsletter.subscribers.index', compact('subscribers'));
    }

    /**
     * 購読者作成フォーム
     */
    public function create()
    {
        return view('admin.newsletter.subscribers.create');
    }

    /**
     * 購読者保存
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|max:255',
            'verified' => 'nullable|boolean',
        ], [
            'email.required' => 'メールアドレスを入力してください。',
            'email.email' => '有効なメールアドレスを入力してください。',
        ]);

        $email = strtolower(trim($validated['email']));

        // 既存の購読者をチェック
        $existing = NewsletterSubscriber::findByEmail($email);
        if ($existing) {
            return redirect()->route('admin.newsletter.subscribers.create')
                ->withErrors(['email' => 'このメールアドレスは既に登録されています。'])
                ->withInput();
        }

        $subscriber = NewsletterSubscriber::create([
            'email' => $email,
            'token' => NewsletterSubscriber::generateToken(),
            'verified_at' => $request->has('verified') && $request->verified ? now() : null,
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.newsletter.subscribers.index')
            ->with('success', '購読者を追加しました。');
    }

    /**
     * 購読者詳細
     */
    public function show($id)
    {
        \Log::info('show called', ['id' => $id, 'method' => request()->method()]);
        $subscriber = NewsletterSubscriber::with('logs.campaign')->findOrFail($id);
        return view('admin.newsletter.subscribers.show', compact('subscriber'));
    }

    /**
     * 購読者編集フォーム
     */
    public function edit($id)
    {
        $subscriber = NewsletterSubscriber::findOrFail($id);
        return view('admin.newsletter.subscribers.edit', compact('subscriber'));
    }

    /**
     * 購読者更新
     */
    public function update(Request $request, $id)
    {
        $subscriber = NewsletterSubscriber::findOrFail($id);

        $validated = $request->validate([
            'email' => 'required|email|max:255',
            'verified' => 'nullable|boolean',
            'unsubscribed' => 'nullable|boolean',
        ], [
            'email.required' => 'メールアドレスを入力してください。',
            'email.email' => '有効なメールアドレスを入力してください。',
        ]);

        $email = strtolower(trim($validated['email']));

        // メールアドレスが変更された場合、重複チェック
        if ($subscriber->email !== $email) {
            $existing = NewsletterSubscriber::findByEmail($email);
            if ($existing && $existing->id !== $subscriber->id) {
                return redirect()->route('admin.newsletter.subscribers.edit', $subscriber->id)
                    ->withErrors(['email' => 'このメールアドレスは既に登録されています。'])
                    ->withInput();
            }
        }

        // メールアドレス更新
        $subscriber->email = $email;

        // ステータス更新
        if ($request->has('verified') && $request->verified) {
            $subscriber->verified_at = now();
        } elseif (!$request->has('verified')) {
            $subscriber->verified_at = null;
        }

        if ($request->has('unsubscribed') && $request->unsubscribed) {
            $subscriber->unsubscribed_at = now();
        } elseif (!$request->has('unsubscribed')) {
            $subscriber->unsubscribed_at = null;
        }

        $subscriber->save();

        return redirect()->route('admin.newsletter.subscribers.index')
            ->with('success', '購読者を更新しました。');
    }

    /**
     * 購読者削除
     */
    public function destroy($id)
    {
        $subscriber = NewsletterSubscriber::findOrFail($id);
        $subscriber->delete();

        return redirect()->route('admin.newsletter.subscribers.index')
            ->with('success', '購読者を削除しました。');
    }

    /**
     * 一括削除
     */
    public function bulkDestroy(Request $request)
    {
        \Log::info('bulkDestroy called', ['request' => $request->all()]);
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:newsletter_subscribers,id',
        ]);

        NewsletterSubscriber::whereIn('id', $validated['ids'])->delete();

        return redirect()->route('admin.newsletter.subscribers.index')
            ->with('success', count($validated['ids']) . '件の購読者を削除しました。');
    }

    /**
     * 一括解除
     */
    public function bulkUnsubscribe(Request $request)
    {
        \Log::info('bulkUnsubscribe called', ['request' => $request->all()]);
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:newsletter_subscribers,id',
        ]);

        NewsletterSubscriber::whereIn('id', $validated['ids'])
            ->update(['unsubscribed_at' => now()]);

        return redirect()->route('admin.newsletter.subscribers.index')
            ->with('success', count($validated['ids']) . '件の購読者を解除しました。');
    }

    /**
     * CSVエクスポート
     */
    public function export()
    {
        $subscribers = NewsletterSubscriber::whereNotNull('verified_at')
            ->whereNull('unsubscribed_at')
            ->orderBy('created_at', 'desc')
            ->get();

        $filename = 'newsletter_subscribers_' . date('YmdHis') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($subscribers) {
            $file = fopen('php://output', 'w');
            // BOMを追加（Excelで文字化けしないように）
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // ヘッダー
            fputcsv($file, ['メールアドレス', '登録日時', '確認日時']);

            // データ
            foreach ($subscribers as $subscriber) {
                fputcsv($file, [
                    $subscriber->email,
                    $subscriber->created_at->format('Y-m-d H:i:s'),
                    $subscriber->verified_at ? $subscriber->verified_at->format('Y-m-d H:i:s') : '',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * バウンスメール処理（手動実行）
     */
    public function processBounced()
    {
        try {
            Artisan::call('newsletter:process-bounced');
            $output = Artisan::output();

            return redirect()->route('admin.newsletter.subscribers.index')
                ->with('success', 'バウンスメールの処理を実行しました。' . ($output ? '<br>' . nl2br(e($output)) : ''));
        } catch (\Exception $e) {
            \Log::error('Process bounced newsletters failed', [
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('admin.newsletter.subscribers.index')
                ->with('error', 'バウンスメールの処理に失敗しました: ' . $e->getMessage());
        }
    }
}
