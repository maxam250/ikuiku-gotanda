<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OptionPay;
use App\Models\OptionCate;
// use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OptionPayController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $option_pays = OptionPay::orderBy('cate', 'asc')->orderBy('sort', 'asc')->get();

        $optionCates = OptionCate::orderBy('sort', 'asc')->get();


        return view('admin.option_pays.index', compact('option_pays', 'optionCates'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('admin.option_pays.index');
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
        $data['price'] = $request->filled('price') ? preg_replace('/[^0-9]/', '', mb_convert_kana($request->price, 'n')) : null;

        // ファイルアップロード処理
        if ($request->hasFile('filename')) {
            $file = $request->file('filename');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('option_pays', $filename, 'public');
            $data['filename'] = $filename;
        }

        OptionPay::create($data);

        return redirect()->route('admin.option_pays.index')->with('success', 'オプション追加完了');
    }

    public function edit($id)
    {
        $option_pays = OptionPay::findOrFail($id);
        $optionCates = OptionCate::orderBy('sort', 'asc')->get();
        return view('admin.option_pays.edit', compact('option_pays', 'optionCates'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'cate' => 'nullable',
            'title' => 'required',
            'filename' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $option = OptionPay::findOrFail($id);
        $data = $request->all();
        $data['cate'] = $request->filled('cate') ? $request->cate : 0;
        $data['price'] = $request->filled('price') ? preg_replace('/[^0-9]/', '', mb_convert_kana($request->price, 'n')) : null;

        // ファイルアップロード処理
        if ($request->hasFile('filename')) {
            // 古いファイルを削除
            if ($option->filename) {
                Storage::disk('public')->delete('option_pays/' . $option->filename);
            }
            
            $file = $request->file('filename');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('option_pays', $filename, 'public');
            $data['filename'] = $filename;
        }

        $option->update($data);

        return redirect()->route('admin.option_pays.index')->with('success', 'オプション更新完了');
    }


    public function destroy($id)
    {
        OptionPay::findOrFail($id)->delete();
        return redirect()->route('admin.option_pays.index')->with('success', 'オプション削除完了');
    }


    //並び替え
    public function sort(Request $request)
    {
        $order = $request->input('order');

        foreach ($order as $item) {
            // 並び替え順序に基づいて更新

            // timestampsの自動更新を無効化して、並び替えを保存
            $option_pays = OptionPay::find($item['id']);
            $option_pays->timestamps = false;  // updated_atとcreated_atを無効化
            $option_pays->sort = $item['position'];
            $option_pays->save();
        }

        return response()->json(['success' => true]);
    }
}
