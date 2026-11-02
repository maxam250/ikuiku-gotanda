<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Member;

class OthersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $memberId = Auth::id();
        $member = Member::findOrFail($memberId);

        return view('member.others', compact('member'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //　バリデータ
        $request->validate([
            'line' => 'nullable|string|max:255',
            'url' => 'nullable|string|max:255',
            'sns' => 'nullable|string|max:255',
            ]);




            // 項目の取得
            $member = Member::findOrFail($id);

            // 名前を更新
            $member->update([
                'line' => $request->line,
                'url' => $request->url,
                'sns' => $request->sns,
            ]);

            // リダイレクト（成功メッセージ付き）
            return redirect()->route('member.others.index', $member->id)->with('success', '情報が更新されました。');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
