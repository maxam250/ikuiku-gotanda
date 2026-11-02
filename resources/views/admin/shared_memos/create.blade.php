<x-admin-layout>
    <x-slot name="header">
        共有メモ登録
    </x-slot>

    <div class="admin-card" style="max-width:700px;">
        <div class="admin-card-header">
            <h2 class="admin-card-title">新規共有メモ登録</h2>
        </div>

        <form method="POST" action="{{ route('admin.shared_memos.store') }}" class="admin-card-body">
            @csrf

            <div class="form_section">
                <div class="form_group form_parts">
                    <label>日付 <span class="text-danger">*</span></label>
                    <input type="date" name="memo_date" value="{{ old('memo_date', now()->format('Y-m-d')) }}"
                           class="form-control @error('memo_date') is-invalid @enderror" required>
                    @error('memo_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="form_group form_parts">
                    <label>カテゴリ</label>
                    <select name="category_id" class="form-select @error('category_id') is-invalid @enderror">
                        <option value="">-- 未選択 --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="form_group form_parts">
                    <label>スタッフ</label>
                    <select name="staff_id" class="form-select @error('staff_id') is-invalid @enderror">
                        <option value="">-- 未選択 --</option>
                        @foreach($staffs as $staff)
                            <option value="{{ $staff->id }}"
                                {{ old('staff_id', session('selected_staff.id')) == $staff->id ? 'selected' : '' }}>
                                {{ $staff->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('staff_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="form_group form_parts">
                    <label>タイトル</label>
                    <input type="text" name="title" value="{{ old('title') }}"
                           class="form-control @error('title') is-invalid @enderror" placeholder="タイトル（任意）">
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="form_group form_parts">
                    <label>メモ</label>
                    <textarea name="body" rows="5"
                              class="form-control @error('body') is-invalid @enderror">{{ old('body') }}</textarea>
                    @error('body')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="form_group form_parts">
                    <label>URL</label>
                    <input type="url" name="url" value="{{ old('url') }}"
                           class="form-control @error('url') is-invalid @enderror" placeholder="https://example.com">
                    @error('url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('admin.shared_memos.index') }}" class="admin-btn admin-btn-secondary">キャンセル</a>
                <button type="submit" class="admin-btn admin-btn-primary">登録</button>
            </div>
        </form>
    </div>
</x-admin-layout>
