<x-admin-layout>

<x-slot name="header">
系列店管理
</x-slot>

@if(session('success'))
    <div class="alert alert-success" style="padding:0.75rem 1rem;margin-bottom:1rem;background:#d4edda;color:#155724;border:1px solid #c3e6cb;border-radius:4px;">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
@endif

<section>
    <div class="toPageArea">
        <a href="{{ route('admin.shop-groups.create') }}" class="create">新しい系列店を追加</a>
    </div>

    <table class="listTbl">
        <thead>
            <tr>
                <th>並替</th>
                <th>画像</th>
                <th>店舗名</th>
                <th>URL</th>
                <th>公開</th>
                <th>アクション</th>
            </tr>
        </thead>
        <tbody id="sortable">
            @foreach ($shopGroups as $sg)
                <tr data-id="{{ $sg->id }}">
                    <td class="handle center">
                        <span class="sort-handle">
                            <span></span><span></span><span></span>
                        </span>
                    </td>
                    <td class="size_10">
                        @if ($sg->filename)
                            <img src="{{ Storage::url($sg->filename) }}" alt="{{ $sg->name }}" width="80" style="border-radius:4px;">
                        @elseif ($sg->image_url)
                            <img src="{{ $sg->image_url }}" alt="{{ $sg->name }}" width="80" style="border-radius:4px;">
                        @else
                            <span style="color:#999;font-size:0.8rem;">なし</span>
                        @endif
                    </td>
                    <td>{{ $sg->name }}</td>
                    <td class="size_12">
                        @if ($sg->url)
                            <a href="{{ $sg->url }}" target="_blank" rel="noopener noreferrer" style="color:#3b82f6;word-break:break-all;">
                                {{ $sg->url }}
                            </a>
                        @else
                            <span style="color:#999;">未設定</span>
                        @endif
                    </td>
                    <td class="center">
                        @if ($sg->is_publish)
                            <span style="color:#16a34a;font-weight:600;"><i class="fas fa-check-circle"></i> 公開</span>
                        @else
                            <span style="color:#9ca3af;"><i class="fas fa-eye-slash"></i> 非公開</span>
                        @endif
                    </td>
                    <td class="action">
                        <div class="admin-btn-group">
                            <a href="{{ route('admin.shop-groups.edit', $sg->id) }}" class="admin-btn admin-btn-secondary admin-btn-sm">
                                <i class="fas fa-edit"></i>編集
                            </a>
                            <form action="{{ route('admin.shop-groups.destroy', $sg->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button class="admin-btn admin-btn-warning admin-btn-sm" onclick="return confirm('「{{ $sg->name }}」を削除しますか？')">
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
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
                url: '{{ route("admin.shop-groups.sort") }}',
                data: { order: order, _token: '{{ csrf_token() }}' },
                success: function() {},
                error: function() { alert('並び替えの保存に失敗しました'); }
            });
        }
    });
});
</script>

</x-admin-layout>
