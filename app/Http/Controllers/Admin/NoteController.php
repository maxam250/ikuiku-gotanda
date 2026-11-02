<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Note;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    public function index()
    {
        $notes = Note::orderBy('sort', 'asc')->get();
        return view('admin.notes.index', compact('notes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $maxSort = Note::max('sort') ?? 0;

        Note::create([
            'title'    => $request->title,
            'subtitle' => $request->subtitle,
            'content'  => $request->content,
            'sort'     => $maxSort + 1,
        ]);

        return redirect()->route('admin.notes.index')->with('success', '注意事項を作成しました');
    }

    public function edit(Note $note)
    {
        return view('admin.notes.edit', compact('note'));
    }

    public function update(Request $request, Note $note)
    {
        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $note->update([
            'title'    => $request->title,
            'subtitle' => $request->subtitle,
            'content'  => $request->content,
        ]);

        return redirect()->route('admin.notes.index')->with('success', '注意事項を更新しました');
    }

    public function destroy(Note $note)
    {
        $note->delete();
        return redirect()->route('admin.notes.index')->with('success', '注意事項を削除しました');
    }

    public function sort(Request $request)
    {
        foreach ($request->input('order', []) as $item) {
            $note = Note::find($item['id']);
            if ($note) {
                $note->timestamps = false;
                $note->sort = $item['position'];
                $note->save();
            }
        }
        return response()->json(['success' => true]);
    }
}
