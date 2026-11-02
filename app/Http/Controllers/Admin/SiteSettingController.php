<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SiteSettingController extends Controller
{
    public function edit()
    {
        $setting = Setting::firstOrCreate([]);

        return view('admin.settings.site', compact('setting'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'square_payment_url' => ['nullable', 'url', 'max:2048'],
            'fanclub_join_terms' => ['nullable', 'string'],
            'payment_description' => ['nullable', 'string'],
            'specified_commercial_transactions' => ['nullable', 'string'],
            'fanclub_withdraw_message' => ['nullable', 'string'],
            'pricing_type'         => ['required', 'in:unified,individual'],
            'tinymce_skin'         => ['required', 'in:oxide,oxide-dark'],
            'schedule_switch_hour' => ['required', 'integer', 'min:0', 'max:23'],
        ]);

        $setting = Setting::firstOrCreate([]);
        $setting->update($validated);

        return redirect()
            ->route('admin.site-settings.edit')
            ->with('success', 'サイト全般設定を更新しました。');
    }
}
