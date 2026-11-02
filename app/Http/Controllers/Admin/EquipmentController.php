<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\EquipmentCate;
use App\Models\EquipmentSubImage;
use App\Models\EquipmentListImage;
use App\Models\AccessoryListImage;
use App\Services\ExternalDatabaseService;
use App\Services\ImageCompressor;
// use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EquipmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $equipments = Equipment::orderBy('cate_id', 'asc')->orderBy('sort', 'asc')->get();

        $equipmentCates = EquipmentCate::orderBy('sort', 'asc')->get();

        return view('admin.equipments.index', compact('equipments', 'equipmentCates'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $equipmentCates = EquipmentCate::orderBy('sort', 'asc')->get();

        return view('admin.equipments.create', compact('equipmentCates') );
    }

    public function store(Request $request)
    {
        // dd($request);

        $request->validate([
            'title' => 'required|string|max:255',
            'cate_id' => 'required',
            'flag1' =>  'in:0,1',
            'flag2' =>  'in:0,1',
            'flag3' =>  'in:0,1',
            'flag4' =>  'in:0,1',
            'flag5' =>  'in:0,1',
            'text1' => 'nullable|string',
            'text2' => 'nullable|string',
            'text3' => 'nullable|string',
            'text4' => 'nullable|string',
            'text5' => 'nullable|string',
            'filename' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'filename_other' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'map_url' => 'nullable|url',
            'map_code' => 'nullable|string',
            'external_id' => 'nullable|string|max:255',
        ]);

        $data = $request->all();

        // 画像の保存処理
        if ($request->hasFile('filename')) {
            $file = $request->file('filename');
            $filename = "eq_" . time() . '_' . $file->getClientOriginalName(); // ユニークなファイル名にする
            $file->storeAs('equipment', $filename, 'public'); // 画像を保存
            ImageCompressor::compressIfNeeded(storage_path('app/public/equipment/' . $filename));
            $data['filename'] = $filename; // データにファイル名を追加
        }
        // 見取り図画像の保存処理
        if ($request->hasFile('filename_other')) {
            // 古い画像が存在している場合は削除
            if ($equipment->filename_other && Storage::disk('public')->exists('equipment/' . $equipment->filename_other)) {
                Storage::disk('public')->delete('equipment/' . $equipment->filename_other); // 古い画像を削除
            }

            // 新しい画像の保存処理
            $file = $request->file('filename_other');
            $filename = 'eq_other_' . time() . '_' . $file->getClientOriginalName(); // ユニークなファイル名にする
            $file->storeAs('equipment', $filename, 'public'); // 新しい画像を保存
            $equipment->filename_other = $filename;
        }


        $equipment = Equipment::create($data);

        // 外部DBに保存（external_idが設定されている場合のみ）
        if ($equipment->external_id) {
            try {
                $externalData = [
                    'equipment_id' => $equipment->external_id ?? $equipment->id,
                    'name' => $equipment->title,
                    'sort' => $equipment->sort ?? 0,
                    'active' => $equipment->active ?? true,
                ];
                ExternalDatabaseService::createEquipment($externalData);
                \Log::info('External equipment created successfully', ['equipment_id' => $equipment->external_id ?? $equipment->id]);
            } catch (\Exception $e) {
                \Log::error('Error creating external equipment:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
                // 外部DBの保存に失敗してもローカルは成功させる
            }
        }

        return redirect()->route('admin.equipments.index')->with('success', '追加完了');
    }

    public function edit($id)
    {
        $equipment = Equipment::with(['equipmentSubImages', 'equipmentListImages', 'accessoryListImages'])
            ->findOrFail($id);
        $equipmentCates = EquipmentCate::orderBy('sort', 'asc')->get();

        return view('admin.equipments.edit', compact('equipment', 'equipmentCates'));
    }

    public function update(Request $request, Equipment $equipment)
    {
        $request->validate([
            'cate_id' => 'required',
            'title' => 'required',
            'flag1' =>  'in:0,1',
            'flag2' =>  'in:0,1',
            'flag3' =>  'in:0,1',
            'flag4' =>  'in:0,1',
            'flag5' =>  'in:0,1',
            'text1' => 'nullable|string',
            'text2' => 'nullable|string',
            'text3' => 'nullable|string',
            'text4' => 'nullable|string',
            'text5' => 'nullable|string',
            'filename' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'filename_other' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'map_url' => 'nullable|url',
            'map_code' => 'nullable|string',
            'external_id' => 'nullable|string|max:255',
        ]);


        // 画像削除のチェックがある場合
        if ($request->has('delete_image') && $equipment->filename) {
            if (Storage::exists('equipment/' . $equipment->filename)) {
                Storage::delete('equipment/' . $equipment->filename); // 画像ファイルの削除
            }
            $equipment->filename = null; // データベースのfilenameカラムをnullにする
        }

        // メイン画像の保存処理
        if ($request->hasFile('filename')) {
            // 古い画像が存在している場合は削除
            if ($equipment->filename && Storage::disk('public')->exists('equipment/' . $equipment->filename)) {
                Storage::disk('public')->delete('equipment/' . $equipment->filename); // 古い画像を削除
            }

            // 新しい画像の保存処理
            $file = $request->file('filename');
            $filename = 'eq_' . time() . '_' . $file->getClientOriginalName(); // ユニークなファイル名にする
            $file->storeAs('equipment', $filename, 'public'); // 新しい画像を保存
            ImageCompressor::compressIfNeeded(storage_path('app/public/equipment/' . $filename));
            $equipment->filename = $filename;
        }
        // 画像削除のチェックがある場合
        if ($request->has('delete_image_other') && $equipment->filename_other) {
            if (Storage::exists('equipment/' . $equipment->filename_other)) {
                Storage::delete('equipment/' . $equipment->filename_other); // 画像ファイルの削除
            }
            $equipment->filename_other = null; // データベースのfilenameカラムをnullにする
        }

        // 見取り図画像の保存処理
        if ($request->hasFile('filename_other')) {
            // 古い画像が存在している場合は削除
            if ($equipment->filename_other && Storage::disk('public')->exists('equipment/' . $equipment->filename_other)) {
                Storage::disk('public')->delete('equipment/' . $equipment->filename_other); // 古い画像を削除
            }

            // 新しい画像の保存処理
            $file_other = $request->file('filename_other');
            $filename_other = 'eq_other_' . time() . '_' . $file_other->getClientOriginalName(); // ユニークなファイル名にする
            $file_other->storeAs('equipment', $filename_other, 'public'); // 新しい画像を保存
            ImageCompressor::compressIfNeeded(storage_path('app/public/equipment/' . $filename_other));
            $equipment->filename_other = $filename_other;
        }


        $equipment->fill($request->except(['filename', 'filename_other']));
        $equipment->save();

        // 外部DBに更新（external_idが設定されている場合のみ）
        if ($equipment->external_id) {
            try {
                $equipmentId = $equipment->external_id ?? $equipment->id;
                $externalData = [
                    'equipment_id' => $equipmentId,
                    'name' => $equipment->title,
                    'sort' => $equipment->sort ?? 0,
                    'active' => $equipment->active ?? true,
                ];
                // 外部DBでequipment_idで検索して更新
                $externalEquipment = ExternalDatabaseService::getEquipments(['equipment_id' => $equipmentId])->first();
                if ($externalEquipment) {
                    ExternalDatabaseService::updateEquipment($externalEquipment->id, $externalData);
                } else {
                    // 存在しない場合は新規作成
                    ExternalDatabaseService::createEquipment($externalData);
                }
                \Log::info('External equipment updated successfully', ['equipment_id' => $equipmentId]);
            } catch (\Exception $e) {
                \Log::error('Error updating external equipment:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
                // 外部DBの更新に失敗してもローカルは成功させる
            }
        }

        return redirect()->route('admin.equipments.index')->with('success', '更新完了');
    }

    public function destroy($id)
    {
        $equipment = Equipment::findOrFail($id);
        
        // 外部DBから削除（external_idが設定されている場合のみ）
        if ($equipment->external_id) {
            try {
                $equipmentId = $equipment->external_id ?? $equipment->id;
                $externalEquipment = ExternalDatabaseService::getEquipments(['equipment_id' => $equipmentId])->first();
                if ($externalEquipment) {
                    ExternalDatabaseService::deleteEquipment($externalEquipment->id);
                    \Log::info('External equipment deleted successfully', ['equipment_id' => $equipmentId]);
                }
            } catch (\Exception $e) {
                \Log::error('Error deleting external equipment:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
                // 外部DBの削除に失敗してもローカルは成功させる
            }
        }
        
        $equipment->delete();
        return redirect()->route('admin.equipments.index')->with('success', '削除完了');
    }


    //並び替え
    public function sort(Request $request)
    {
        $order = $request->input('order');

        foreach ($order as $item) {
            // 並び替え順序に基づいて更新

            // timestampsの自動更新を無効化して、並び替えを保存
            $equipments = Equipment::find($item['id']);
            $equipments->timestamps = false;  // updated_atとcreated_atを無効化
            $equipments->sort = $item['position'];
            $equipments->save();
        }

        return response()->json(['success' => true]);
    }

    /**
     * ホテルサブ画像を追加
     */
    public function addEquipmentSubImage(Request $request, $equipmentId)
    {
        $request->validate([
            'filename' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $equipment = Equipment::findOrFail($equipmentId);

        if ($request->hasFile('filename')) {
            $file = $request->file('filename');
            $filename = 'equipment_sub_' . time() . '_' . $file->getClientOriginalName();
            $file->storeAs('equipment_sub', $filename, 'public');
            ImageCompressor::compressIfNeeded(storage_path('app/public/equipment_sub/' . $filename));

            EquipmentSubImage::create([
                'equipment_id' => $equipmentId,
                'filename' => $filename,
                'name' => $request->name,
                'description' => $request->description,
                'sort' => EquipmentSubImage::where('equipment_id', $equipmentId)->max('sort') + 1,
            ]);
        }

        return redirect()->back()->with('success', 'ホテルサブ画像を追加しました');
    }

    /**
     * ホテルサブ画像を削除
     */
    public function deleteEquipmentSubImage($imageId)
    {
        $image = EquipmentSubImage::findOrFail($imageId);

        if (Storage::disk('public')->exists('equipment_sub/' . $image->filename)) {
            Storage::disk('public')->delete('equipment_sub/' . $image->filename);
        }

        $image->delete();

        return redirect()->back()->with('success', 'ホテルサブ画像を削除しました');
    }

    /**
     * 機材リスト画像を追加
     */
    public function addEquipmentListImage(Request $request, $equipmentId)
    {
        $request->validate([
            'filename' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $equipment = Equipment::findOrFail($equipmentId);

        if ($request->hasFile('filename')) {
            $file = $request->file('filename');
            $filename = 'equipment_list_' . time() . '_' . $file->getClientOriginalName();
            $file->storeAs('equipment_list', $filename, 'public');
            ImageCompressor::compressIfNeeded(storage_path('app/public/equipment_list/' . $filename));

            EquipmentListImage::create([
                'equipment_id' => $equipmentId,
                'filename' => $filename,
                'name' => $request->name,
                'description' => $request->description,
                'sort' => EquipmentListImage::where('equipment_id', $equipmentId)->max('sort') + 1,
            ]);
        }

        return redirect()->back()->with('success', '機材リスト画像を追加しました');
    }

    /**
     * 機材リスト画像を削除
     */
    public function deleteEquipmentListImage($imageId)
    {
        $image = EquipmentListImage::findOrFail($imageId);

        if (Storage::disk('public')->exists('equipment_list/' . $image->filename)) {
            Storage::disk('public')->delete('equipment_list/' . $image->filename);
        }

        $image->delete();

        return redirect()->back()->with('success', '機材リスト画像を削除しました');
    }

    /**
     * 小物リスト画像を追加
     */
    public function addAccessoryListImage(Request $request, $equipmentId)
    {
        $request->validate([
            'filename' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $equipment = Equipment::findOrFail($equipmentId);

        if ($request->hasFile('filename')) {
            $file = $request->file('filename');
            $filename = 'accessory_list_' . time() . '_' . $file->getClientOriginalName();
            $file->storeAs('accessory_list', $filename, 'public');
            ImageCompressor::compressIfNeeded(storage_path('app/public/accessory_list/' . $filename));

            AccessoryListImage::create([
                'equipment_id' => $equipmentId,
                'filename' => $filename,
                'name' => $request->name,
                'description' => $request->description,
                'sort' => AccessoryListImage::where('equipment_id', $equipmentId)->max('sort') + 1,
            ]);
        }

        return redirect()->back()->with('success', '小物リスト画像を追加しました');
    }

    /**
     * 小物リスト画像を削除
     */
    public function deleteAccessoryListImage($imageId)
    {
        $image = AccessoryListImage::findOrFail($imageId);

        if (Storage::disk('public')->exists('accessory_list/' . $image->filename)) {
            Storage::disk('public')->delete('accessory_list/' . $image->filename);
        }

        $image->delete();

        return redirect()->back()->with('success', '小物リスト画像を削除しました');
    }
}
