<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PremiumRank;
use Illuminate\Http\Request;

class PremiumRankController extends Controller
{
    public function index()
    {
        $premiumRanks = PremiumRank::orderBy('sort', 'asc')->get();
        return view('admin.premium-ranks.index', compact('premiumRanks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        PremiumRank::create($request->all());

        return redirect()->route('admin.premium-ranks.index')->with('success', 'プレミアランク追加完了');
    }

    public function edit($id)
    {
        $premiumRank = PremiumRank::findOrFail($id);
        return view('admin.premium-ranks.edit', compact('premiumRank'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $premiumRank = PremiumRank::findOrFail($id);
        $premiumRank->update($request->all());

        return redirect()->route('admin.premium-ranks.index')->with('success', 'プレミアランク更新完了');
    }

    public function destroy($id)
    {
        PremiumRank::findOrFail($id)->delete();
        return redirect()->route('admin.premium-ranks.index')->with('success', 'プレミアランク削除完了');
    }

    public function sort(Request $request)
    {
        $order = $request->input('order');

        foreach ($order as $item) {
            $premiumRank = PremiumRank::find($item['id']);
            $premiumRank->timestamps = false;
            $premiumRank->sort = $item['position'];
            $premiumRank->save();
        }

        return response()->json(['success' => true]);
    }
}
