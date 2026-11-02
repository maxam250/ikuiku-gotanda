<?php
namespace App\Http\Controllers\Item;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\Mycostume;
use App\Models\MycostumeCate;

class MycostumeController extends Controller
{
    public function index()
    {
        $itemId = Auth::guard('item')->user()->id;
        $mycostumes = Mycostume::where('item_id', $itemId)->orderBy('sort', 'asc')->get();
        $mycostumeCates = MycostumeCate::orderBy('sort', 'asc')->get();

        return view('item.mycostumes.index', compact('mycostumes', 'mycostumeCates'));
    }


    public function create()
    {
        $itemId = Auth::guard('item')->user()->id;
        $mycostumeCates = MycostumeCate::orderBy('sort', 'asc')->get();

        return view('item.mycostumes.create', compact('mycostumeCates'));
    }

    public function store(Request $request)
    {
        $itemId = Auth::guard('item')->user()->id;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'body' => 'nullable|string',
            'active' => 'required|boolean',
            'mycostume_cate_id' => 'nullable|exists:mycostume_cates,id',
            'price' => 'nullable|integer|min:0',
            'filename' => 'nullable|image|max:5120',
            'sort' => 'nullable|integer',
        ]);

        $data = $validated;
        $data['item_id'] = $itemId;
        $data['mycostume_cate_id'] = $validated['mycostume_cate_id'] ?? null;
        unset($data['filename']);

        if ($request->hasFile('filename')) {
            $file = $request->file('filename');
            $filename = $file->hashName();
            $file->storeAs('mycostume', $filename, 'public');
            $data['filename'] = $filename;
        }

        Mycostume::create($data);

        return redirect()->route('item.mycostumes.index')->with('success', 'マイコスチュームを登録しました');
    }

    public function edit(Mycostume $mycostume)
    {
        $itemId = Auth::guard('item')->user()->id;

        if ($mycostume->item_id != $itemId) {
            abort(403);
        }

        $mycostumeCates = MycostumeCate::orderBy('sort', 'asc')->get();

        return view('item.mycostumes.edit', compact('mycostume', 'mycostumeCates'));
    }

    public function update(Request $request, Mycostume $mycostume)
    {
        $itemId = Auth::guard('item')->user()->id;

        if ($mycostume->item_id != $itemId) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'body' => 'nullable|string',
            'active' => 'required|boolean',
            'mycostume_cate_id' => 'nullable|exists:mycostume_cates,id',
            'price' => 'nullable|integer|min:0',
            'filename' => 'nullable|image|max:5120',
            'delete_image' => 'nullable|boolean',
            'sort' => 'nullable|integer',
        ]);

        $data = $validated;
        $data['mycostume_cate_id'] = $validated['mycostume_cate_id'] ?? null;
        unset($data['filename']);

        if ($request->boolean('delete_image') && $mycostume->filename) {
            Storage::disk('public')->delete('mycostume/' . $mycostume->filename);
            $data['filename'] = null;
        }

        if ($request->hasFile('filename')) {
            if ($mycostume->filename) {
                Storage::disk('public')->delete('mycostume/' . $mycostume->filename);
            }
            $file = $request->file('filename');
            $filename = $file->hashName();
            $file->storeAs('mycostume', $filename, 'public');
            $data['filename'] = $filename;
        }

        $mycostume->update($data);

        return redirect()->route('item.mycostumes.index')->with('success', 'マイコスチュームを更新しました');
    }

    public function destroy(Mycostume $mycostume)
    {
        $itemId = Auth::guard('item')->user()->id;

        if ($mycostume->item_id != $itemId) {
            abort(403);
        }

        if ($mycostume->filename) {
            Storage::disk('public')->delete('mycostume/' . $mycostume->filename);
        }

        $mycostume->delete();

        return redirect()->route('item.mycostumes.index')->with('success', 'マイコスチュームを削除しました');
    }

    public function sort(Request $request)
    {
        $itemId = Auth::guard('item')->user()->id;
        $order = $request->input('order', []);

        foreach ($order as $item) {
            Mycostume::where('id', $item['id'])
                ->where('item_id', $itemId)
                ->update(['sort' => $item['position']]);
        }

        return response()->json(['success' => true]);
    }
}

