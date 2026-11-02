<x-admin-layout>

<x-slot name="header">
注意事項管理
</x-slot>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<section class="modern-register-form">
    <div class="form-container">
        <h3>新規追加</h3>
        <form method="POST" action="{{ route('admin.notes.store') }}">
            @csrf
            <div class="form-group">
                <label>タイトル（h4） <span class="text-danger">*</span></label>
                <input type="text" name="title" class="modern-input" required placeholder="タイトル" value="{{ old('title') }}">
                @error('title')<p class="text-danger">{{ $message }}</p>@enderror
            </div>
            <div class="form-group">
                <label>サブタイトル（h5）</label>
                <input type="text" name="subtitle" class="modern-input" placeholder="サブタイトル" value="{{ old('subtitle') }}">
            </div>
            <div class="form-group">
                <label>コンテンツ</label>
                <textarea name="content" class="modern-input" rows="4" placeholder="コンテンツ（登録後に編集画面でTinyMCEを使って編集できます）">{{ old('content') }}</textarea>
            </div>
            <div class="form-row" style="justify-content:flex-end">
                <button type="submit" class="admin-btn admin-btn-submit">
                    <i class="fas fa-plus"></i> 登録
                </button>
            </div>
        </form>
    </div>
</section>

<section>
    <table class="listTbl">
        <thead>
            <tr>
                <th>並替</th>
                <th>タイトル</th>
                <th>サブタイトル</th>
                <th>アクション</th>
            </tr>
        </thead>
        <tbody id="sortable">
            @foreach($notes as $note)
            <tr data-id="{{ $note->id }}">
                <td class="handle center">
                    <span class="sort-handle">
                        <span></span><span></span><span></span>
                    </span>
                </td>
                <td>{{ $note->title }}</td>
                <td>{{ $note->subtitle }}</td>
                <td class="action">
                    <div class="admin-btn-group">
                        <a href="{{ route('admin.notes.edit', $note) }}" class="admin-btn admin-btn-secondary admin-btn-sm">
                            <i class="fas fa-edit"></i>編集
                        </a>
                        <form action="{{ route('admin.notes.destroy', $note) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button class="admin-btn admin-btn-warning admin-btn-sm" onclick="return confirm('本当に削除しますか？')">
                                <i class="fas fa-trash"></i>削除
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</section>

@push('scripts')
<script>
$(function() {
    $('#sortable').sortable({
        handle: '.sort-handle',
        update: function(event, ui) {
            let order = [];
            $('#sortable tr').each(function(index) {
                order.push({ id: $(this).data('id'), position: index + 1 });
            });
            $.ajax({
                type: 'POST',
                url: '{{ route("admin.notes.sort") }}',
                data: { order: order, _token: '{{ csrf_token() }}' },
                error: function() { alert('並び替えの保存に失敗しました'); }
            });
        }
    });
});
</script>
@endpush

</x-admin-layout>
