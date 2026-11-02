<x-admin-layout>
    <x-slot name="header">
        共有メモ カテゴリ作成
    </x-slot>

    <div class="admin-card" style="max-width:500px;">
        <div class="admin-card-header">
            <h2 class="admin-card-title">新規カテゴリ作成</h2>
        </div>

        <form method="POST" action="{{ route('admin.shared_memo_categories.store') }}" class="admin-card-body">
            @csrf

            <div class="form_section">
                <div class="form_group form_parts">
                    <label>カテゴリ名 <span class="text-danger">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}"
                           class="form-control @error('name') is-invalid @enderror" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="form_group form_parts">
                    <label>説明</label>
                    <textarea name="description" rows="3"
                              class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="form_group form_parts">
                    <label>並び順</label>
                    <input type="number" name="sort" value="{{ old('sort', 0) }}"
                           class="form-control @error('sort') is-invalid @enderror" style="width:100px;">
                    @error('sort')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('admin.shared_memo_categories.index') }}" class="admin-btn admin-btn-secondary">キャンセル</a>
                <button type="submit" class="admin-btn admin-btn-primary">作成</button>
            </div>
        </form>
    </div>
</x-admin-layout>
