<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Interview;
use Illuminate\Http\Request;

class InterviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $interviews = Interview::orderBy('sort', 'asc')->get();

        return view('admin.interviews.index', compact('interviews'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('admin.interviews.index');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $interview = Interview::findOrFail($id);
        return view('admin.interviews.show', compact('interview'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
        ]);

        Interview::create($request->all());

        return redirect()->route('admin.interviews.index')->with('success', 'Q&A追加完了');
    }

    public function edit($id)
    {
        $interview = Interview::findOrFail($id);
        return view('admin.interviews.edit', compact('interview'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required',
        ]);

        $interview = Interview::findOrFail($id);
        $interview->update($request->all());

        return redirect()->route('admin.interviews.index')->with('success', 'Q&A更新完了');
    }

    public function destroy($id)
    {
        Interview::findOrFail($id)->delete();
        return redirect()->route('admin.interviews.index')->with('success', 'Q&A削除完了');
    }

    //並び替え
    public function sort(Request $request)
    {
        $order = $request->input('order');

        foreach ($order as $item) {
            // 並び替え順序に基づいて更新

            // timestampsの自動更新を無効化して、並び替えを保存
            $interviews = Interview::find($item['id']);
            $interviews->timestamps = false;  // updated_atとcreated_atを無効化
            $interviews->sort = $item['position'];
            $interviews->save();
        }

        return response()->json(['success' => true]);
    }
}
