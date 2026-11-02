<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * 問い合わせ一覧を表示
     */
    public function index(Request $request)
    {
        $query = Contact::query();

        // ステータスフィルター
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 問い合わせ種別フィルター
        if ($request->filled('inquiry_type')) {
            $query->where('inquiry_type', $request->inquiry_type);
        }

        // スパムフィルター
        if ($request->filled('is_spam')) {
            $query->where('is_spam', $request->is_spam);
        }

        // 検索
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }

        $contacts = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.contacts.index', compact('contacts'));
    }

    /**
     * 問い合わせ詳細を表示
     */
    public function show(Contact $contact)
    {
        return view('admin.contacts.show', compact('contact'));
    }

    /**
     * 問い合わせを編集
     */
    public function edit(Contact $contact)
    {
        return view('admin.contacts.edit', compact('contact'));
    }

    /**
     * 問い合わせを更新
     */
    public function update(Request $request, Contact $contact)
    {
        $request->validate([
            'status' => 'required|in:unread,in_progress,completed',
            'admin_memo' => 'nullable|string|max:1000',
        ]);

        $contact->update([
            'status' => $request->status,
            'admin_memo' => $request->admin_memo,
        ]);

        return redirect()->route('admin.contacts.show', $contact)
            ->with('success', '問い合わせを更新しました');
    }

    /**
     * 問い合わせを削除
     */
    public function destroy(Contact $contact)
    {
        $contact->delete();

        return redirect()->route('admin.contacts.index')
            ->with('success', '問い合わせを削除しました');
    }

    /**
     * 未読問い合わせ数を取得（API）
     */
    public function getUnreadCount()
    {
        $count = Contact::where('status', 'unread')->count();
        return response()->json(['count' => $count], 200, ['Content-Type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
    }

    /**
     * スパム問い合わせ数を取得（API）
     */
    public function getSpamCount()
    {
        $count = Contact::where('is_spam', true)->count();
        return response()->json(['count' => $count]);
    }
}
