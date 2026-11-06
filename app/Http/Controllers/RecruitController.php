<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\RecruitNotification;
use App\Mail\RecruitAutoReply;
use App\Models\Shop;

class RecruitController extends Controller
{
    public function index()
    {
        $shops = Shop::first();
        return view('recruit', compact('shops'));
    }

    public function submit(Request $request)
    {


        // バリデーション
        $validator = \Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
            'age' => 'required|integer|min:18',
            'mail' => 'required|email|max:255',
            'tel' => 'nullable|string|max:20',
            'lineid' => 'nullable|string|max:255',
            'url' => 'nullable|string|max:255',
            'url2' => 'nullable|string|max:255',
            'exp' => 'required|in:1,2',
            'attach1' => 'required|file|mimes:jpeg,jpg,png,gif|max:10240',
            'attach2' => 'required|file|mimes:jpeg,jpg,png,gif|max:10240',
            'attach3' => 'nullable|file|mimes:jpeg,jpg,png,gif|max:10240',
            'media' => 'required|in:0,1,2,3,4,5,6',
            'bikou' => 'nullable|string|max:1000',
        ],
        [],
        [
            'name' => 'お名前',
            'company' => '会社名',
            'age' => '年齢',
            'mail' => 'メールアドレス',
            'tel' => '電話番号',
            'lineid' => 'LINE ID',
            'url' => 'SNS URL①',
            'url2' => 'SNS URL②',
            'exp' => '経験',
            'attach1' => 'バストアップ画像',
            'attach2' => '全身画像',
            'attach3' => 'その他画像',
            'media' => '媒体',
            'bikou' => '備考',
        ]);

        if ($validator->fails()) {
            return redirect()->route('recruits.index')
                ->withErrors($validator)
                ->withInput()
                ->withFragment('toForm');
        }

        // ファイルアップロード処理
        $attachments = [];
        $uploadPath = 'recruit_attachments';

        if ($request->hasFile('attach1')) {
            $filename1 = time() . '_1_' . $request->file('attach1')->getClientOriginalName();
            $request->file('attach1')->storeAs($uploadPath, $filename1, 'public');
            $attachments[] = storage_path('app/public/' . $uploadPath . '/' . $filename1);
        }

        if ($request->hasFile('attach2')) {
            $filename2 = time() . '_2_' . $request->file('attach2')->getClientOriginalName();
            $request->file('attach2')->storeAs($uploadPath, $filename2, 'public');
            $attachments[] = storage_path('app/public/' . $uploadPath . '/' . $filename2);
        }

        if ($request->hasFile('attach3')) {
            $filename3 = time() . '_3_' . $request->file('attach3')->getClientOriginalName();
            $request->file('attach3')->storeAs($uploadPath, $filename3, 'public');
            $attachments[] = storage_path('app/public/' . $uploadPath . '/' . $filename3);
        }

        $mediaNames = [
            '0' => 'その他',
            '1' => 'ネット検索',
            '2' => '当グループ撮影会',
            '3' => 'キャストブログ/SNS',
            '4' => 'ショップSNS',
            '5' => '雑誌/各種メディア',
            '6' => '口コミ'
        ];

        // メールデータの準備
        $mailData = [
            'name' => $request->name,
            'company' => $request->company,
            'age' => $request->age,
            'mail' => $request->mail,
            'tel' => $request->tel,
            'lineid' => $request->lineid,
            'url' => $request->url,
            'url2' => $request->url2,
            'exp' => $request->exp == '1' ? 'あり' : 'なし',
            'media' => $mediaNames[$request->media] ?? '不明',
            'bikou' => $request->bikou,
            'attachments' => $attachments
        ];

        try {
            // 管理者への通知メール送信
            $shop  = Shop::first();
            $to    = $shop?->shop_mail2 ?: config('mail.from.address');
            $fromName = ($shop?->shop_name ?: config('mail.from.name', '求人担当')) . ' 求人担当';

            // デバッグ用ログ
            \Log::info('Recruit form submission', [
                'to' => $to,
                'mailData' => $mailData
            ]);

            Mail::to($to)->send(new RecruitNotification($mailData));

            // 送信者への自動返信メール送信
            Mail::to($request->mail)->send(new RecruitAutoReply($mailData, $to, $fromName));

            return redirect()->route('recruits.index')->with('success', 'ご応募ありがとうございます。内容を確認の上、ご連絡いたします。')->withFragment('toForm');
        } catch (\Exception $e) {
            // エラーログを記録
            \Log::error('Recruit form submission failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('recruits.index')->with('error', '送信に失敗しました。しばらく時間をおいて再度お試しください。')->withInput()->withFragment('toForm');
        }
    }
}
