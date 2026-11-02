<?php

namespace App\Http\Controllers;

use App\Models\Option;
use App\Models\OptionPay;
use Illuminate\Http\Request;

class GuidanceController extends Controller
{
    /**
     * guidanceページを表示
     */
    public function index()
    {
        // オプションとオプションPAYのデータを取得
        $options = Option::orderBy('sort', 'asc')->get();
        $option_pays = OptionPay::orderBy('sort', 'asc')->get();

        return view('guidance', compact('options', 'option_pays'));
    }
}
