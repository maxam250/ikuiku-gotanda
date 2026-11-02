<?php

namespace App\Http\Controllers;
use App\Models\Cast;
use App\Models\CastImage;
use Illuminate\Http\Request;

class CastController extends Controller
{
    //
    public function list(){


        $date = '2024-08-15'; // 絞り込みの日付

        // 条件に一致するキャストとその関連スケジュールリストを取得
        $casts = Cast::whereHas('schedules', function ($query) use ($date) {
            $query->where('date', '>=', $date);
        })
        ->with(['schedules' => function ($query) {
            $query->orderBy('ts_start');
        }])
        ->get();


        // $casts = Cast::all();
        return view('cast', ['casts' => $casts]);
    }



    public function show($id){

        $cast = Cast::with('schedules')->findOrFail($id);  // 指定されたIDのキャストを取得

        // 画像リストを取得
        $images = CastImage::where('cast_id', $id)->get();

        // dd( $castImages);


        //出勤
        $adjustedDate = adjustTimeWithShift(); //custom helper関数
        $schedules = $cast->schedules;
        // dd($cast);


        return view('prof', compact('cast', 'images', 'schedules'));

    }


}
