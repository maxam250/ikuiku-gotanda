<?php

namespace App\Http\Controllers\Item;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    //

    public function index()
    {
        $events = Event::orderBy('sort', 'asc')->get();
        return view('item.events.index', compact('events'));
    }



    // 新しいイベントの作成フォームを表示 (GET /item/events/create)
    public function create()
    {
        return view('item.events.create');
    }

    // 新しいイベントを保存 (POST /item/events)
    public function store(Request $request)
    {

        // dd($request);
        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'nullable|string',
            'filename' => 'nullable|image|max:2048',
            'sort' => 'integer',
            'date_start' => 'nullable|date',
            'date_end' => 'nullable|date',
            'timer_flag' => 'nullable|boolean', // これでチェックされたら1、されてなければ0
        ]);


        $validated['timer_flag'] = $request->has('timer_flag') ? 1 : 0;


        // 画像の保存処理
        // $filename = null;
        // if ($request->hasFile('filename')) {
        //     $filename = $request->file('filename')->store('upload/event', 'public');
        // }

        $filename = null;
        if ($request->hasFile('filename')) {
            \Log::info('Store method - File upload detected', [
                'original_name' => $request->file('filename')->getClientOriginalName(),
                'mime_type' => $request->file('filename')->getMimeType(),
                'size' => $request->file('filename')->getSize()
            ]);

            $file = $request->file('filename');
            $filename = "event_".time() . '_' . $file->getClientOriginalName();

            \Log::info('Store method - Attempting to save new file', [
                'new_filename' => $filename,
                'storage_path' => 'events/' . $filename
            ]);

            try {
                $file->storeAs('events', $filename);
                \Log::info('Store method - File saved successfully');
            } catch (\Exception $e) {
                \Log::error('Store method - File save failed', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }

        $createData = [
            'title' => $request->title,
            'body' => $request->body,
            'filename' => $filename,
            'date_start' => $request->date_start,
            'date_end' => $request->date_end,
            'sort' => $request->sort ?? 0,
            'position' => $request->position ?? 0,
            'cate' => $request->cate ?? 0,
            'link' => $request->link,
            'timer_flag' => $request->timer_flag ?? 0,
        ];

        \Log::info('Creating event with data', $createData);

        Event::create($createData);

        return redirect()->route('admin.events.index')->with('success', 'イベントが作成されました');
    }

    // イベントを表示 (GET /item/events/{event})
    public function show(Event $event)
    {
        return view('item.events.show', compact('event'));
    }

    // イベント編集フォームを表示 (GET /item/events/{event}/edit)
    public function edit(Event $event)
    {
        return view('item.events.edit', compact('event'));
    }

    // イベントを更新 (PUT /item/events/{event})
    public function update(Request $request, Event $event)
    {

        \Log::info('Event update request:', [
            'has_file' => $request->hasFile('filename'),
            'file_info' => $request->hasFile('filename') ? [
                'original_name' => $request->file('filename')->getClientOriginalName(),
                'mime_type' => $request->file('filename')->getMimeType(),
                'size' => $request->file('filename')->getSize(),
            ] : null,
            'all_data' => $request->all()
        ]);

        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'nullable|string',
            'filename' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'sort' => 'integer',
            'date_start' => 'nullable|date',
            'date_end' => 'nullable|date',
            'timer_flag' => 'nullable|boolean', // これでチェックされたら1、されてなければ0
        ]);

        // 画像削除のチェックがある場合
        if ($request->has('delete_image') && $event->filename) {
            if (Storage::exists('events/' . $event->filename)) {
                Storage::delete('events/' . $event->filename);
            }
            $event->filename = null; // データベースのfilenameカラムをnullにする
        }


        // 古い画像の削除処理と新しい画像の保存処理
        $filename = $event->filename; // 既存のファイル名を保持


        if ($request->hasFile('filename')) {
            try {
                // 古い画像が存在している場合は削除
                if ($filename && Storage::exists('events/' . $filename)) {
                    Storage::delete('events/' . $filename);
                }

                // 新しい画像の保存処理
                $file = $request->file('filename');
                $filename = 'event_'.time() . '_' . $file->getClientOriginalName();
                \Log::info('Attempting to store file:', [
                    'filename' => $filename,
                    'path' => 'events/' . $filename
                ]);

                // Storageファサードを使用して保存
                $result = Storage::disk('public')->putFileAs('events', $file, $filename);

                \Log::info('Storage result:', ['result' => $result]);

                if (!$result) {
                    \Log::error('Failed to store image:', ['filename' => $filename]);
                    return redirect()->back()->with('error', '画像のアップロードに失敗しました。');
                }
            } catch (\Exception $e) {
                \Log::error('Image upload error:', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                return redirect()->back()->with('error', '画像のアップロード中にエラーが発生しました。');
            }
        }


        // dd($file);

        // dd($request);
        // イベントの更新
        $event->update([
            'title' => $request->title,
            'body' => $request->body,
            'filename' => $filename,
            'date_start' => $request->date_start,
            'date_end' => $request->date_end,
            'sort' => $request->sort ?? 0,
            'position' => $request->position ?? 0,
            'cate' => $request->cate ?? 0,
            'link' => $request->link,
            'timer_flag' => $request->timer_flag ?? 0,
        ]);

        return redirect()->route('item.events.index')->with('success', 'イベントが更新されました');
    }


    // イベントを削除 (DELETE /item/events/{event})
    public function destroy($event)
    {
        $event = Event::findOrFail($event);

        // 画像が存在している場合は削除
        if ($event->filename && Storage::exists('events/' . $event->filename)) {
            Storage::delete('events/' . $event->filename);
        }

        // イベントデータの削除
        $event->delete();

        return redirect()->route('item.events.index')->with('success', 'イベントが削除されました');
    }


    //並び替え
    public function sort(Request $request)
    {
        $order = $request->input('order');

        foreach ($order as $item) {
            // イベントを並び替え順序に基づいて更新
            Event::where('id', $item['id'])->update(['sort' => $item['position']]);
        }

        return response()->json(['success' => true]);
    }

}
