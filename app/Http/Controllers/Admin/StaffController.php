<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
{
    /**
     * スタッフ一覧表示
     */
    public function index(Request $request)
    {
        $query = Staff::query();

        if ($request->has('is_active') && $request->is_active !== '') {
            $query->where('is_active', $request->is_active === '1');
        }

        $staffs = $query->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends($request->query());

        return view('admin.staff.index', compact('staffs'));
    }

    /**
     * スタッフ作成フォーム表示
     */
    public function create()
    {
        $offices = \App\Models\Office::where('is_active', 1)->orderBy('sort')->orderBy('name')->get();
        return view('admin.staff.create', compact('offices'));
    }

    /**
     * スタッフ保存
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'default_start_time' => 'nullable|date_format:H:i',
            'default_end_time' => 'nullable|date_format:H:i',
            'work_days' => 'nullable|array',
            'work_days.*' => 'integer|in:1,2,3,4,5,6,7',
            'weekly_schedule' => 'nullable|array',
            'work_start_time' => 'nullable|date_format:H:i',
            'work_end_time' => 'nullable|date_format:H:i',
            'office_id' => 'nullable|exists:offices,id',
            'skills' => 'nullable|array',
            'specialties' => 'nullable|string|max:255',
            'experience' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'memo' => 'nullable|string',
            'color' => 'nullable|string|max:7',
            'sort' => 'nullable|integer',
            'auto_schedule_enabled' => 'boolean',
            'auto_schedule_start_date' => 'nullable|date',
            'auto_schedule_end_date' => 'nullable|date|after:auto_schedule_start_date',
        ]);

        Staff::create([
            'name' => $request->name,
            'default_start_time' => $request->default_start_time,
            'default_end_time' => $request->default_end_time,
            'work_days' => $request->work_days,
            'weekly_schedule' => $request->weekly_schedule,
            'work_start_time' => $request->work_start_time,
            'work_end_time' => $request->work_end_time,
            'office_id' => $request->office_id,
            'skills' => $request->skills,
            'specialties' => $request->specialties,
            'experience' => $request->experience,
            'is_active' => $request->has('is_active'),
            'memo' => $request->memo,
            'color' => $request->color,
            'sort' => $request->sort,
            'auto_schedule_enabled' => $request->has('auto_schedule_enabled'),
            'auto_schedule_start_date' => $request->auto_schedule_start_date,
            'auto_schedule_end_date' => $request->auto_schedule_end_date,
        ]);

        return redirect()->route('admin.staff.index')->with('success', 'スタッフが作成されました');
    }

    /**
     * スタッフ編集フォーム表示
     */
    public function edit(Staff $staff)
    {
        $offices = \App\Models\Office::where('is_active', 1)->orderBy('sort')->orderBy('name')->get();
        return view('admin.staff.edit', compact('staff', 'offices'));
    }

    /**
     * スタッフ更新
     */
    public function update(Request $request, Staff $staff)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'default_start_time' => 'nullable|date_format:H:i',
            'default_end_time' => 'nullable|date_format:H:i',
            'work_days' => 'nullable|array',
            'work_days.*' => 'integer|in:1,2,3,4,5,6,7',
            'weekly_schedule' => 'nullable|array',
            'work_start_time' => 'nullable|date_format:H:i',
            'work_end_time' => 'nullable|date_format:H:i',
            'office_id' => 'nullable|exists:offices,id',
            'skills' => 'nullable|array',
            'specialties' => 'nullable|string|max:255',
            'experience' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'memo' => 'nullable|string',
            'color' => 'nullable|string|max:7',
            'sort' => 'nullable|integer',
            'auto_schedule_enabled' => 'boolean',
            'auto_schedule_start_date' => 'nullable|date',
            'auto_schedule_end_date' => 'nullable|date|after:auto_schedule_start_date',
        ]);

        $staff->update([
            'name' => $request->name,
            'default_start_time' => $request->default_start_time,
            'default_end_time' => $request->default_end_time,
            'work_days' => $request->work_days,
            'weekly_schedule' => $request->weekly_schedule,
            'work_start_time' => $request->work_start_time,
            'work_end_time' => $request->work_end_time,
            'office_id' => $request->office_id,
            'skills' => $request->skills,
            'specialties' => $request->specialties,
            'experience' => $request->experience,
            'is_active' => $request->has('is_active'),
            'memo' => $request->memo,
            'color' => $request->color,
            'sort' => $request->sort,
            'auto_schedule_enabled' => $request->has('auto_schedule_enabled'),
            'auto_schedule_start_date' => $request->auto_schedule_start_date,
            'auto_schedule_end_date' => $request->auto_schedule_end_date,
        ]);

        return redirect()->route('admin.staff.index')->with('success', 'スタッフが更新されました');
    }

    /**
     * スタッフ削除
     */
    public function destroy(Staff $staff)
    {
        $staff->delete();
        return redirect()->route('admin.staff.index')->with('success', 'スタッフが削除されました');
    }
}
