<x-admin-layout>

<x-slot name="header">キャストタイプ</x-slot>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

{{-- 登録フォーム --}}
<section class="modern-register-form">
    <div class="form-container">
        <h3>タイプ登録</h3>
        <p class="form-hint">検索対象となります</p>
        <form method="POST" action="{{ route('admin.item-categories.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="form-row" style="flex-wrap:wrap; gap:0.5rem; align-items:flex-end;">
                <div style="flex:1; min-width:120px;">
                    <label style="font-size:0.8rem; color:#666; display:block; margin-bottom:0.2rem;">タイプ名 <span style="color:red;">*</span></label>
                    <input type="text" name="name" class="modern-input" required placeholder="タイプ名"
                           value="{{ old('name') }}">
                </div>

                <div style="flex:1; min-width:160px;">
                    <label style="font-size:0.8rem; color:#666; display:block; margin-bottom:0.2rem;">アイコン画像</label>
                    <input type="file" name="icon_image" accept="image/*" class="modern-input" style="padding:0.3rem;">
                </div>

                <div style="display:flex; align-items:center; gap:0.4rem;">
                    <label style="font-size:0.8rem; color:#666; display:block; margin-bottom:0.2rem;">バッジ色</label>
                    <input type="color" name="color" value="{{ old('color', '#aaaaaa') }}"
                           style="width:40px; height:36px; border:none; cursor:pointer; border-radius:4px;"
                           title="バッジ色">
                </div>

                <button type="submit" class="admin-btn admin-btn-submit">
                    <i class="fas fa-plus"></i> 登録
                </button>
            </div>
            @error('name')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
            @error('icon_image')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
        </form>
    </div>
</section>

{{-- 一覧テーブル --}}
<section class="contentArea">
    <table class="listTbl">
        <thead>
            <tr>
                <th style="width:40px;">並替</th>
                <th style="width:80px;">アイコン</th>
                <th>タイプ名</th>
                <th style="width:80px;">キャスト数</th>
                <th>アクション</th>
            </tr>
        </thead>
        <tbody id="sortable">
            @foreach($categories as $category)
            <tr data-id="{{ $category->id }}">
                <td class="handle center">
                    <span class="sort-handle"><span></span><span></span><span></span></span>
                </td>
                <td class="center">
                    @if($category->icon_image)
                        <img src="{{ Storage::url($category->icon_image) }}"
                             alt="{{ $category->name }}"
                             style="width:40px; height:40px; border-radius:50%; object-fit:cover; border:2px solid {{ $category->color }};">
                    @else
                        <span style="
                            display:inline-flex; align-items:center; justify-content:center;
                            background:{{ $category->color }};
                            color:#fff; width:40px; height:40px; border-radius:50%;
                            font-size:1.1rem;">
                            <i class="fas fa-tag"></i>
                        </span>
                    @endif
                </td>
                <td>
                    <strong>{{ $category->name }}</strong>
                </td>
                <td class="center">{{ $category->items_count }}</td>
                <td class="action">
                    <div class="admin-btn-group">
                        <a href="{{ route('admin.item-categories.edit', $category) }}"
                           class="admin-btn admin-btn-secondary admin-btn-sm">
                            <i class="fas fa-edit"></i> 編集
                        </a>
                        <form action="{{ route('admin.item-categories.destroy', $category) }}"
                              method="POST" style="display:inline;">
                            @csrf @method('DELETE')
                            <button class="admin-btn admin-btn-warning admin-btn-sm"
                                    onclick="return confirm('削除すると該当キャストのタイプが未設定になります。よろしいですか？')">
                                <i class="fas fa-trash"></i> 削除
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</section>

<script>
$(function() {
    $('#sortable').sortable({
        handle: '.sort-handle',
        update: function() {
            let order = [];
            $('#sortable tr').each(function(index) {
                order.push({ id: $(this).data('id'), position: index + 1 });
            });
            $.post('{{ route('admin.item-categories.sort') }}', {
                order: order,
                _token: '{{ csrf_token() }}'
            });
        }
    });
});
</script>

</x-admin-layout>
