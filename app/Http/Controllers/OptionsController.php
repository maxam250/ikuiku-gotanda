<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OptionCate;
use App\Models\Option;
use App\Models\OptionPay;

class OptionsController extends Controller
{
    public function index()
    {
        // カテゴリごとに無料・有料オプションをまとめて取得
        $cates = OptionCate::with([
            'options' => fn($q) => $q->orderBy('sort'),
            'option_pay' => fn($q) => $q->orderBy('sort'),
        ])->orderBy('sort')->get();

        // カテゴリ未設定分も拾う（cate=0 or NULL）
        $freeOptions = Option::whereNotIn('cate', $cates->pluck('id'))
            ->orWhereNull('cate')
            ->orderBy('sort')
            ->get();

        $payOptions = OptionPay::whereNotIn('cate', $cates->pluck('id'))
            ->orWhereNull('cate')
            ->orderBy('sort')
            ->get();

        return view('options', compact('cates', 'freeOptions', 'payOptions'));
    }
}
