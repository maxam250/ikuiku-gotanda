<?php

namespace App\Http\Controllers\Item;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $courses = Course::orderBy('sort', 'asc')->get();


        return view('item.courses.index', compact('courses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('item.courses.create');
    }


    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
        ]);


        Course::create($request->all());

        return redirect()->route('item.courses.index')->with('success', 'コースを作成しました');
    }


    public function edit($course)
    {

        $course = Course::findOrFail($course);
// dd($Course);
        return view('item.courses.edit', compact('course'));
    }


    public function update(Request $request, $course)
    {

// dd($course);

        $request->validate([
            'title' => 'required',
            'body' => 'nullable|string',
        ]);

        $Course = Course::findOrFail($course);
        $Course->update($request->all());

        return redirect()->route('item.courses.index')->with('success', 'コース更新しました');
    }


    public function destroy($id)
    {
        Course::findOrFail($id)->delete();
        return redirect()->route('item.courses.index')->with('success', 'コース削除しました');
    }


    //並び替え
    public function sort(Request $request)
    {
        $order = $request->input('order');

        foreach ($order as $item) {
            // 並び替え順序に基づいて更新

            // timestampsの自動更新を無効化して、並び替えを保存
            $coursecate = Course::find($item['id']);
            $coursecate->timestamps = false;  // updated_atとcreated_atを無効化
            $coursecate->sort = $item['position'];
            $coursecate->save();
        }

        return response()->json(['success' => true]);
    }

}
