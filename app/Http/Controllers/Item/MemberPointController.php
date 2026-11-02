<?php

namespace App\Http\Controllers\Item;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MemberPoint;
use App\Models\Member;
use Carbon\Carbon;



class MemberPointController extends Controller
{
    /**
     * Display a listing of the resource.
     */



    public function index(MemberPoint $member_point)
    {
        //
        return view('item.member_points.index');

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
    public function show(MemberPoint $member_point)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */

    public function edit(string $member_point)
    {
        //
        $member_point = MemberPoint::findOrFail($member_point);
        // 各メンバーごとのポイントの計算　Memberモデルにアクセサで設定
        // $items = Item::where('item_active', '1' )->orderBy('id', 'desc')->get();


        // 暗号化されたemailを復号
        // $decryptedEmail = Crypt::decryptString($member->email);

        return view('item.member_points.edit', compact('member_point'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MemberPoint $member_point)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MemberPoint $member_point)
    {
        //
    }

}
