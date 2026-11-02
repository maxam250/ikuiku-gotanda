<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class StaffSelectController extends Controller
{
    /**
     * アクティブなスタッフ一覧を取得（Ajax用）
     */
    public function getStaffList(): JsonResponse
    {
        $staffs = Staff::where('is_active', 1)
            ->select('id', 'name', 'role')
            ->orderBy('sort')
            ->orderBy('name')
            ->get();

        return response()->json($staffs);
    }

    /**
     * 選択したスタッフをセッションに保存
     */
    public function setStaffSession(Request $request): JsonResponse
    {
        $staffId = $request->input('staff_id');

        if (!$staffId) {
            session()->forget('selected_staff');
            return response()->json(['success' => true, 'message' => 'スタッフ選択をクリアしました']);
        }

        $staff = Staff::find($staffId);

        if (!$staff) {
            return response()->json(['success' => false, 'message' => '指定されたスタッフが見つかりません'], 404);
        }

        session(['selected_staff' => [
            'id'   => $staff->id,
            'name' => $staff->name,
            'role' => $staff->role,
        ]]);

        return response()->json([
            'success' => true,
            'message' => "{$staff->name} を選択しました",
            'staff'   => [
                'id'   => $staff->id,
                'name' => $staff->name,
                'role' => $staff->role,
            ],
        ]);
    }

    /**
     * 現在選択中のスタッフを取得
     */
    public function getCurrentStaff(): JsonResponse
    {
        $selected = session('selected_staff');

        if (!$selected) {
            return response()->json(['success' => false]);
        }

        return response()->json(['success' => true, 'staff' => $selected]);
    }
}
