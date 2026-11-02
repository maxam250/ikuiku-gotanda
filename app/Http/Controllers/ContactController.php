<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactNotification;

class ContactController extends Controller
{
    /**
     * 問い合わせフォームを表示
     */
    public function index()
    {
        return view('contact');
    }

    /**
     * 問い合わせ内容を確認画面に送信
     */
    public function confirm(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'inquiry_type' => 'required|in:general,reservation,cast,other',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
        ]);

        // スパムチェック
        $isSpam = $this->checkSpam($request);

        return view('contact.confirm', [
            'contact' => $request->all(),
            'isSpam' => $isSpam
        ]);
    }

    /**
     * 問い合わせを保存してメール送信
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'inquiry_type' => 'required|in:general,reservation,cast,other',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
        ]);

        // スパムチェック
        $isSpam = $this->checkSpam($request);

        // データベースに保存
        $contact = Contact::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'inquiry_type' => $request->inquiry_type,
            'subject' => $request->subject,
            'message' => $request->message,
            'status' => 'unread',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'is_spam' => $isSpam,
        ]);

        // 管理者にメール通知（スパムでない場合のみ）
        if (!$isSpam) {
            try {
                Mail::to($shops->mail) // $shopは適切に定義されていると仮定
                    ->send(new ContactNotification($contact));
            } catch (\Exception $e) {
                \Log::error('Contact notification email failed: ' . $e->getMessage());
            }
        }

        return redirect()->route('contact.complete');
    }

    /**
     * 完了画面を表示
     */
    public function complete()
    {
        return view('contact.complete');
    }

    /**
     * スパムチェック
     */
    private function checkSpam(Request $request)
    {
        $isSpam = false;

        // 1. 短時間での連続投稿チェック
        $recentContacts = Contact::where('ip_address', $request->ip())
            ->where('created_at', '>=', now()->subMinutes(5))
            ->count();

        if ($recentContacts > 2) {
            $isSpam = true;
        }

        // 2. 特定のキーワードチェック
        $spamKeywords = ['viagra', 'casino', 'loan', 'credit', 'buy now', 'click here'];
        $message = strtolower($request->message);
        $subject = strtolower($request->subject);

        foreach ($spamKeywords as $keyword) {
            if (strpos($message, $keyword) !== false || strpos($subject, $keyword) !== false) {
                $isSpam = true;
                break;
            }
        }

        // 3. リンクの数チェック
        $linkCount = substr_count($message, 'http') + substr_count($message, 'www');
        if ($linkCount > 3) {
            $isSpam = true;
        }

        return $isSpam;
    }
}
