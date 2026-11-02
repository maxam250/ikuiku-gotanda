<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Option;
use App\Models\OptionCate;
// use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $options = Option::orderBy('cate', 'asc')->orderBy('sort', 'asc')->get();


        $optionCates = OptionCate::orderBy('sort', 'asc')->get();

        return view('admin.options.index', compact('options', 'optionCates'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('admin.options.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'cate' => 'nullable',
            'title' => 'required',
            'filename' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->all();
        $data['cate'] = $request->filled('cate') ? $request->cate : 0;

        // ファイルアップロード処理
        if ($request->hasFile('filename')) {
            $file = $request->file('filename');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('options', $filename, 'public');
            $data['filename'] = $filename;
        }

        Option::create($data);

        return redirect()->route('admin.options.index')->with('success', 'オプション追加完了');
    }

    public function edit($id)
    {
        $option = Option::findOrFail($id);
        $optionCates = OptionCate::orderBy('sort', 'asc')->get();
        return view('admin.options.edit', compact('option', 'optionCates'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'cate' => 'nullable',
            'title' => 'required',
            'filename' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $option = Option::findOrFail($id);
        $data = $request->all();
        $data['cate'] = $request->filled('cate') ? $request->cate : 0;

        // ファイルアップロード処理
        if ($request->hasFile('filename')) {
            // 古いファイルを削除
            if ($option->filename) {
                Storage::disk('public')->delete('options/' . $option->filename);
            }
            
            $file = $request->file('filename');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('options', $filename, 'public');
            $data['filename'] = $filename;
        }

        $option->update($data);

        return redirect()->route('admin.options.index')->with('success', 'オプション更新完了');
    }

    public function destroy($id)
    {
        Option::findOrFail($id)->delete();
        return redirect()->route('admin.options.index')->with('success', 'オプション削除完了');
    }


    //並び替え
    public function sort(Request $request)
    {
        $order = $request->input('order');

        foreach ($order as $item) {
            // 並び替え順序に基づいて更新

            // timestampsの自動更新を無効化して、並び替えを保存
            $options = Option::find($item['id']);
            $options->timestamps = false;  // updated_atとcreated_atを無効化
            $options->sort = $item['position'];
            $options->save();
        }

        return response()->json(['success' => true]);
    }
}
