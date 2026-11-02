<x-admin-layout>

<x-slot name="header">キャストタイプ編集</x-slot>

<section class="modern-register-form">
    <div class="form-container">
        <h3>タイプ編集</h3>

        <form method="POST" action="{{ route('admin.item-categories.update', $itemCategory) }}"
              enctype="multipart/form-data">
            @csrf @method('PUT')

            <div class="form-group">
                <label class="form-label form-label-required">タイプ名</label>
                <input type="text" name="name" class="modern-input"
                       value="{{ old('name', $itemCategory->name) }}" required>
                @error('name')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">アイコン画像</label>
                @if($itemCategory->icon_image)
                    <div style="margin-bottom:0.75rem; display:flex; align-items:center; gap:1rem;">
                        <img src="{{ Storage::url($itemCategory->icon_image) }}"
                             alt="{{ $itemCategory->name }}"
                             id="currentIconPreview"
                             style="width:60px; height:60px; border-radius:50%; object-fit:cover; border:2px solid {{ $itemCategory->color }};">
                        <span style="font-size:0.85rem; color:#666;">現在の画像</span>
                    </div>
                @else
                    <div style="margin-bottom:0.75rem; display:flex; align-items:center; gap:1rem;">
                        <span style="
                            display:inline-flex; align-items:center; justify-content:center;
                            background:{{ $itemCategory->color }};
                            color:#fff; width:60px; height:60px; border-radius:50%; font-size:1.4rem;"
                            id="currentIconPreview">
                            <i class="fas fa-tag"></i>
                        </span>
                        <span style="font-size:0.85rem; color:#999;">画像未設定</span>
                    </div>
                @endif
                <input type="file" name="icon_image" accept="image/*" class="modern-input"
                       style="padding:0.3rem;" id="iconImageInput">
                <p class="form-help">変更する場合のみ選択してください（JPEG・PNG・GIF・SVG・WebP、最大1MB）</p>
                @error('icon_image')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="form-group">
                <label class="form-label form-label-required">バッジ枠色</label>
                <div style="display:flex; align-items:center; gap:1rem;">
                    <input type="color" name="color"
                           value="{{ old('color', $itemCategory->color) }}"
                           style="width:50px; height:40px; border:none; cursor:pointer; border-radius:4px;"
                           id="colorInput">
                    <span style="font-size:0.85rem; color:#666;">HEXカラーを選択</span>
                </div>
                @error('color')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div style="display:flex; gap:1rem; margin-top:1.5rem;">
                <button type="submit" class="admin-btn admin-btn-submit">
                    <i class="fas fa-save"></i> 更新
                </button>
                <a href="{{ route('admin.item-categories.index') }}" class="admin-btn admin-btn-secondary">
                    <i class="fas fa-arrow-left"></i> 戻る
                </a>
            </div>
        </form>
    </div>
</section>

<script>
// ファイル選択時にローカルプレビュー
document.getElementById('iconImageInput').addEventListener('change', function() {
    const file = this.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function(e) {
        const preview = document.getElementById('currentIconPreview');
        preview.outerHTML = '<img src="' + e.target.result + '" id="currentIconPreview" style="width:60px; height:60px; border-radius:50%; object-fit:cover; border:2px solid #aaa;">';
    };
    reader.readAsDataURL(file);
});
</script>

</x-admin-layout>
