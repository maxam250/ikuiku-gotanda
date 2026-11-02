<x-admin-layout>

    <x-slot name="header">
    ホテル編集
    </x-slot>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1>ホテル編集</h1>
                <a href="{{ route('admin.equipments.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> 一覧に戻る
                </a>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.equipments.update', $equipment->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">基本情報</h5>
                            </div>
                            <div class="card-body">
                                <!-- カテゴリ -->
                                <div class="mb-3">
                                    <label for="cate_id" class="form-label">カテゴリ <span class="text-danger">*</span></label>
                                    <select class="form-control" id="cate_id" name="cate_id" required>
                                        <option value="" disabled>カテゴリを選択してください</option>
                                        @foreach($equipmentCates as $cate)
                                            <option value="{{ $cate->id }}" {{ old('cate_id', $equipment->cate_id) == $cate->id ? 'selected' : '' }}>
                                                {{ $cate->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- 名称 -->
                                <div class="mb-3">
                                    <label for="title" class="form-label">名称 <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="title" name="title"
                                           value="{{ old('title', $equipment->title) }}" required>
                                </div>

                                <!-- 外部DB用ID -->
                                {{-- <div class="mb-3">
                                    <label for="external_id" class="form-label">外部DB用ID</label>
                                    <input type="text" class="form-control" id="external_id" name="external_id"
                                           value="{{ old('external_id', $equipment->external_id) }}" placeholder="外部DBで使用するIDを入力">
                                </div> --}}

                                <!-- 概要 -->
                                <div class="mb-3">
                                    <label for="text1" class="form-label">概要</label>
                                    <input type="text" class="form-control" id="text1" name="text1"
                                           value="{{ old('text1', $equipment->text1) }}">
                                </div>

                                <!-- 項目2-5 -->
                                <div class="mb-3">
                                    <label for="text2" class="form-label">電話番号</label>
                                    <input type="text" class="form-control" id="text2" name="text2"
                                           value="{{ old('text2', $equipment->text2) }}">
                                </div>

                                <div class="mb-3">
                                    <label for="text3" class="form-label">住所</label>
                                    <input type="text" class="form-control" id="text3" name="text3"
                                           value="{{ old('text3', $equipment->text3) }}">
                                </div>

                                <div class="mb-3">
                                    <label for="text4" class="form-label">料金</label>
                                    <input type="text" class="form-control" id="text4" name="text4"
                                           value="{{ old('text4', $equipment->text4) }}">
                                </div>
                                {{--
                                <div class="mb-3">
                                    <label for="text5" class="form-label">項目5</label>
                                    <input type="text" class="form-control" id="text5" name="text5"
                                           value="{{ old('text5', $equipment->text5) }}">
                                </div>
                                --}}
                                <!-- リンク -->
                                <div class="mb-3">
                                    <label for="link" class="form-label">リンク</label>
                                    <input type="text" class="form-control" id="link" name="link"
                                           value="{{ old('link', $equipment->link) }}">
                                </div>

                                <!-- アクセスマップURL -->

                            {{--
                                <div class="mb-3">
                                    <label for="map_url" class="form-label">アクセスマップURL</label>
                                    <input type="url" class="form-control" id="map_url" name="map_url"
                                           value="{{ old('map_url', $equipment->map_url) }}" placeholder="https://maps.google.com/...">
                                </div>
                            --}}
                                <!-- アクセスマップHTML -->

                            {{--
                                <div class="mb-3">
                                    <label for="map_code" class="form-label">アクセスマップHTML</label>
                                    <textarea class="form-control" id="map_code" name="map_code"
                                              rows="5" placeholder="<iframe src='...'></iframe>">{{ old('map_code', $equipment->map_code) }}</textarea>
                                </div>
                            --}}
                            </div>
                        </div>

                        <!-- 画像アップロード -->

                {{--
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5 class="mb-0">画像設定</h5>
                            </div>
                            <div class="card-body">
                                <!-- メイン画像 -->
                                <div class="mb-3">
                                    <label for="filename" class="form-label">メイン画像</label>
                                    @if($equipment->filename)
                                        <div class="mb-2">
                                            <div class="current-image-label">現在の画像:</div>
                                            <img src="{{ Storage::url('equipment/' . $equipment->filename) }}"
                                                 alt="現在の画像" class="img-thumbnail"
                                                 style="max-width: 200px; height: auto; margin-top: 0.5rem;">
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" id="delete_image"
                                                   name="delete_image" value="1">
                                            <label class="form-check-label" for="delete_image">
                                                現在の画像を削除する
                                            </label>
                                        </div>
                                    @endif
                                    <input type="file" class="form-control" id="filename" name="filename"
                                           accept="image/*">
                                    <div class="form-text">対応形式: JPEG, PNG, JPG, GIF (最大2MB)</div>
                                </div>

                                <!-- 見取り図画像 -->
                                <div class="mb-3">
                                    <label for="filename_other" class="form-label">見取り図画像</label>
                                    @if($equipment->filename_other)
                                        <div class="mb-2">
                                            <div class="current-image-label">現在の画像:</div>
                                            <img src="{{ Storage::url('equipment/' . $equipment->filename_other) }}"
                                                 alt="現在の見取り図画像" class="img-thumbnail"
                                                 style="max-width: 200px; height: auto; margin-top: 0.5rem;">
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" id="delete_image_other"
                                                   name="delete_image_other" value="1">
                                            <label class="form-check-label" for="delete_image_other">
                                                現在の見取り図画像を削除する
                                            </label>
                                        </div>
                                    @endif
                                    <input type="file" class="form-control" id="filename_other" name="filename_other"
                                           accept="image/*">
                                    <div class="form-text">対応形式: JPEG, PNG, JPG, GIF (最大2MB)</div>
                                </div>
                            </div>
                        </div>
                    --}}
                    </div>

                    <div class="col-lg-4">
                        <!-- フラグ設定 -->
                    {{--
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">フラグ設定</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="flag1"
                                               name="flag1" value="1" {{ old('flag1', $equipment->flag1) == 1 ? 'checked' : '' }}>
                                        <label class="form-check-label" for="flag1">
                                            フラグ1
                                        </label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="flag2"
                                               name="flag2" value="1" {{ old('flag2', $equipment->flag2) == 1 ? 'checked' : '' }}>
                                        <label class="form-check-label" for="flag2">
                                            フラグ2
                                        </label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="flag3"
                                               name="flag3" value="1" {{ old('flag3', $equipment->flag3) == 1 ? 'checked' : '' }}>
                                        <label class="form-check-label" for="flag3">
                                            フラグ3
                                        </label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="flag4"
                                               name="flag4" value="1" {{ old('flag4', $equipment->flag4) == 1 ? 'checked' : '' }}>
                                        <label class="form-check-label" for="flag4">
                                            フラグ4
                                        </label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="flag5"
                                               name="flag5" value="1" {{ old('flag5', $equipment->flag5) == 1 ? 'checked' : '' }}>
                                        <label class="form-check-label" for="flag5">
                                            フラグ5
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    --}}
                        <!-- 操作ボタン -->
                        <div class="card">
                            <div class="card-body">
                                <button type="submit" class="af-btn af-btn-primary af-btn-full">
                                    <i class="fas fa-save"></i> 更新する
                                </button>
                                <a href="{{ route('admin.equipments.index') }}" class="btn btn-secondary w-100">
                                    <i class="fas fa-times"></i> キャンセル
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <!-- ホテル画像管理セクション -->

        {{--
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">ホテル画像管理</h5>
                        </div>
                        <div class="card-body">
                            <!-- ホテルサブ画像 -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0">ホテルサブ画像</h6>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('admin.equipments.addEquipmentSubImage', $equipment->id) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input type="file" name="filename" class="form-control" required>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="text" name="name" class="form-control" placeholder="画像名" required>
                                            </div>
                                            <div class="col-md-4">
                                                <input type="text" name="description" class="form-control" placeholder="説明">
                                            </div>
                                            <div class="col-md-2">
                                                <button type="submit" class="af-btn af-btn-primary af-btn-full">追加</button>
                                            </div>
                                        </div>
                                    </form>

                                    <div class="mt-3">
                                        @foreach($equipment->equipmentSubImages as $image)
                                            <div class="d-flex align-items-center mb-2 p-2 border">
                                                <img src="{{ Storage::url('equipment_sub/' . $image->filename) }}" alt="" width="50" class="mr-3">
                                                <div class="flex-grow-1">
                                                    <strong>{{ $image->name }}</strong>
                                                    @if($image->description)
                                                        <br><small>{{ $image->description }}</small>
                                                    @endif
                                                </div>
                                                <form action="{{ route('admin.equipments.deleteEquipmentSubImage', $image->id) }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('削除しますか？')">削除</button>
                                                </form>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- 機材リスト画像 -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0">機材リスト画像</h6>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('admin.equipments.addEquipmentListImage', $equipment->id) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input type="file" name="filename" class="form-control" required>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="text" name="name" class="form-control" placeholder="画像名" required>
                                            </div>
                                            <div class="col-md-4">
                                                <input type="text" name="description" class="form-control" placeholder="説明">
                                            </div>
                                            <div class="col-md-2">
                                                <button type="submit" class="af-btn af-btn-primary af-btn-full">追加</button>
                                            </div>
                                        </div>
                                    </form>

                                    <div class="mt-3">
                                        @foreach($equipment->equipmentListImages as $image)
                                            <div class="d-flex align-items-center mb-2 p-2 border">
                                                <img src="{{ Storage::url('equipment_list/' . $image->filename) }}" alt="" width="50" class="mr-3">
                                                <div class="flex-grow-1">
                                                    <strong>{{ $image->name }}</strong>
                                                    @if($image->description)
                                                        <br><small>{{ $image->description }}</small>
                                                    @endif
                                                </div>
                                                <form action="{{ route('admin.equipments.deleteEquipmentListImage', $image->id) }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('削除しますか？')">削除</button>
                                                </form>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- 小物リスト画像 -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0">小物リスト画像</h6>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('admin.equipments.addAccessoryListImage', $equipment->id) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input type="file" name="filename" class="form-control" required>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="text" name="name" class="form-control" placeholder="画像名" required>
                                            </div>
                                            <div class="col-md-4">
                                                <input type="text" name="description" class="form-control" placeholder="説明">
                                            </div>
                                            <div class="col-md-2">
                                                <button type="submit" class="af-btn af-btn-primary af-btn-full">追加</button>
                                            </div>
                                        </div>
                                    </form>

                                    <div class="mt-3">
                                        @foreach($equipment->accessoryListImages as $image)
                                            <div class="d-flex align-items-center mb-2 p-2 border">
                                                <img src="{{ Storage::url('accessory_list/' . $image->filename) }}" alt="" width="50" class="mr-3">
                                                <div class="flex-grow-1">
                                                    <strong>{{ $image->name }}</strong>
                                                    @if($image->description)
                                                        <br><small>{{ $image->description }}</small>
                                                    @endif
                                                </div>
                                                <form action="{{ route('admin.equipments.deleteAccessoryListImage', $image->id) }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('削除しますか？')">削除</button>
                                                </form>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    --}}
    </div>
</div>

@push('styles')
<style>
.current-image-label {
    font-size: 0.875rem;
    font-weight: 500;
    color: #495057;
    margin-bottom: 0.5rem;
}
</style>
@endpush
</x-admin-layout>
