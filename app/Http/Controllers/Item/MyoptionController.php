<?php
namespace App\Http\Controllers\Item;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\Myoption;
use App\Models\MyoptionCate;
use Carbon\Carbon;

class MyoptionController extends Controller
{
    public function index()
    {
        $item = Auth::guard('item')->user();
        
        $myoptions = Myoption::where('item_id', $item->id)
            ->orderBy('sort', 'asc')
            ->get();
        $myoptionCates = MyoptionCate::orderBy('sort', 'asc')->get();
        return view('item.myoptions.index', compact('myoptions', 'myoptionCates'));
    }

    public function create()
    {
        $myoptionCates = MyoptionCate::orderBy('sort', 'asc')->get();
        return view('item.myoptions.create', compact('myoptionCates'));
    }

    public function store(Request $request)
    {
        $item = Auth::guard('item')->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'body' => 'nullable|string',
            'filename' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'sort' => 'integer',
            'cate' => 'nullable|integer',
            'active' => 'nullable|boolean',
        ]);

        $validated['active'] = $request->has('active') ? 1 : 0;

        $filename = null;
        if ($request->hasFile('filename')) {
            $file = $request->file('filename');
            $filename = "mycos_".time() . '_' . $file->getClientOriginalName();
            $file->storeAs('myoption', $filename);
        }

        Myoption::create([
            'name' => $request->name,
            'active' => $request->active ?? 0,
            'filename' => $filename,
            'body' => $request->body,
            'price' => $request->price ?? 0,
            'sort' => $request->sort ?? 0,
            'myoption_cate_id' => $request->myoption_cate_id ?? 0,
            'item_id' => $item->id,
        ]);

        return redirect()->route('item.myoptions.index')->with('success', 'イベントが作成されました');
    }

    public function show(Myoption $myoption)
    {
        return view('item.myoptions.show', compact('myoption'));
    }

    public function edit(Myoption $myoption)
    {
        $myoptionCates = MyoptionCate::orderBy('sort', 'asc')->get();
        return view('item.myoptions.edit', compact('myoption', 'myoptionCates'));
    }

    public function update(Request $request, Myoption $myoption)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'body' => 'nullable|string',
            'filename' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'sort' => 'integer',
            'myoption_cate_id' => 'nullable|integer',
        ]);

        if ($request->has('delete_image') && $myoption->filename) {
            if (Storage::exists('myoption/' . $myoption->filename)) {
                Storage::delete('myoption/' . $myoption->filename);
            }
            $myoption->filename = null;
        }

        $filename = $myoption->filename;

        if ($request->hasFile('filename')) {
            if ($filename && Storage::exists('myoption/' . $filename)) {
                Storage::delete('myoption/' . $filename);
            }

            $file = $request->file('filename');
            $filename = 'myoption_'.time() . '_' . $file->getClientOriginalName();
            $file->storeAs('myoption', $filename);
        }

        $myoption->update([
            'name' => $request->name,
            'body' => $request->body,
            'filename' => $filename,
            'sort' => $request->sort ?? 0,
            'active' => $request->active ?? 0,
            'price' => $request->price ?? 0,
            'myoption_cate_id' => $request->myoption_cate_id ?? 0,
        ]);

        return redirect()->route('item.myoptions.index')->with('success', 'イベントが更新されました');
    }

    public function destroy($myoption)
    {
        $myoption = Myoption::findOrFail($myoption);

        if ($myoption->filename && Storage::exists('myoption/' . $myoption->filename)) {
            Storage::delete('myoption/' . $myoption->filename);
        }

        $myoption->delete();

        return redirect()->route('item.myoptions.index')->with('success', 'イベントが削除されました');
    }

    public function sort(Request $request)
    {
        $itemId = Auth::guard('item')->user()->id;
        $order = $request->input('order');

        foreach ($order as $item) {
            Myoption::where('id', $item['id'])
                ->where('item_id', $itemId)
                ->update(['sort' => $item['position']]);
        }

        return response()->json(['success' => true]);
    }
}

