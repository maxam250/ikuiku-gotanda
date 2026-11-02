<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Note;
use App\Models\Option;
use App\Models\OptionCate;
use App\Models\OptionPay;
use App\Models\Setting;
use Illuminate\Http\Request;

class SystemController extends Controller
{
    /**
     * systemページを表示
     */
    public function index()
    {
        $options     = Option::orderBy('sort', 'asc')->get();
        $option_pays = OptionPay::orderBy('sort', 'asc')->get();
        $optionCates = OptionCate::orderBy('sort', 'asc')->get();
        $setting     = Setting::firstOrCreate([]);

        $courses = Course::where('active', 1)
            ->with(['course_fee' => fn($q) => $q->orderBy('sort')])
            ->orderBy('sort')
            ->get();

        $notes = Note::orderBy('sort', 'asc')->get();

        return view('system', compact('options', 'option_pays', 'optionCates', 'setting', 'courses', 'notes'));
    }
}
