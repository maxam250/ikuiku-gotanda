<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseFee;
// use App\Models\Item;
use Illuminate\Http\Request;



class CourseFeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $courses = Course::orderBy('sort', 'asc')->get();
        $courseFees = CourseFee::orderBy('course_id', 'asc')->orderBy('sort', 'asc')->get();

        return view('admin.course_fees.index', compact('courses', 'courseFees'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $courses = Course::orderBy('sort', 'asc')->get();
        $courseFee = CourseFee::orderBy('course_id', 'asc')->orderBy('sort', 'asc')->get();
        return view('admin.course_fees.create', compact('courses', 'courseFee'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required|integer',
            'course_time' => 'nullable|integer',
            'course' => 'nullable|string',
            'fee' => 'required|integer',
        ]);

        CourseFee::create($request->all());

        return redirect()->route('admin.course_fees.index')->with('success', '料金追加完了');
    }

    public function edit($id)
    {
        $courses = Course::orderBy('sort', 'asc')->get();
        $courseFee = CourseFee::findOrFail($id);

        return view('admin.course_fees.edit', compact('courses', 'courseFee'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'course_id' => 'required|integer',
            'course_time' => 'nullable|integer',
            'course' => 'nullable|string',
            'fee' => 'required|integer',
        ]);

        $course = CourseFee::findOrFail($id);
        $course->update($request->all());

        return redirect()->route('admin.course_fees.index')->with('success', '料金更新完了');
    }

    public function destroy($id)
    {
        CourseFee::findOrFail($id)->delete();
        return redirect()->route('admin.course_fees.index')->with('success', '料金削除完了');
    }


    //並び替え
    public function sort(Request $request)
    {
        $order = $request->input('order');

        foreach ($order as $item) {
            // 並び替え順序に基づいて更新

            // timestampsの自動更新を無効化して、並び替えを保存
            $course = CourseFee::find($item['id']);
            $course->timestamps = false;  // updated_atとcreated_atを無効化
            $course->sort = $item['position'];
            $course->save();
        }

        return response()->json(['success' => true]);
    }
}
