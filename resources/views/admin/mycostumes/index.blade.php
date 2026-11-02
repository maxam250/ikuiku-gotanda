<x-admin-layout>


<x-slot name="header">
 マイコスチューム管理
</x-slot>

<section class="toPageArea">
    <a href="{{ route('admin.mycostume_cates.index') }}" class="cate">カテゴリの編集</a>
    <a href="{{ route('admin.mycostumes.create') }}" class="create">新規作成</a>
</section>

<section>
    <div class="cast-list">
        @foreach ($items as $item)
            <div class="cast-item">
                <div class="cast-info">
                    <h3 class="cast-name">{{ $item->item_name }}</h3>
                    <div class="cast-counts">
                        <span class="badge badge-primary">登録数: {{ $categoryCounts[$item->id]['total'] }}</span>
                        @foreach ($categories as $category)
                            @if (isset($categoryCounts[$item->id]['by_category'][$category->id]))
                                <span class="badge badge-secondary">{{ $category->name }}: {{ $categoryCounts[$item->id]['by_category'][$category->id] }}</span>
                            @endif
                        @endforeach
                    </div>
                </div>
                <div class="cast-actions">
                    <a href="{{ route('admin.mycostumes.cast', $item->id) }}" class="btn btn-primary">マイコスチューム一覧</a>
                </div>
            </div>
        @endforeach
    </div>
</section>

<style>
.cast-list {
    max-width: 1200px;
    margin: 0 auto;
}

.cast-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border: 1px solid #ddd;
    padding: 20px;
    margin-bottom: 15px;
    border-radius: 8px;
    background-color: #f9f9f9;
    transition: all 0.3s ease;
}

.cast-item:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}

.cast-info {
    flex: 1;
}

.cast-name {
    color: #333;
    margin-bottom: 10px;
    font-size: 1.3em;
    font-weight: bold;
}

.cast-counts {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.badge {
    padding: 5px 10px;
    border-radius: 4px;
    font-size: 0.9em;
}

.badge-primary {
    background-color: #007bff;
    color: white;
}

.badge-secondary {
    background-color: #6c757d;
    color: white;
}

.cast-actions {
    margin-left: 20px;
}

.btn {
    display: inline-block;
    font-weight: 400;
    text-align: center;
    vertical-align: middle;
    user-select: none;
    border: 1px solid transparent;
    padding: 0.375rem 0.75rem;
    font-size: 1rem;
    line-height: 1.5;
    border-radius: 0.25rem;
    transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    text-decoration: none;
    cursor: pointer;
}

.btn-primary {
    color: #fff;
    background-color: #007bff;
    border-color: #007bff;
}

.btn-primary:hover {
    color: #fff;
    background-color: #0069d9;
    border-color: #0062cc;
}
</style>

<script>
    $(function() {
        // 各キャストのテーブルを sortable に設定
        $('.sortable').each(function() {
            $(this).sortable({
                handle: '.sort-handle',
                update: function(event, ui) {
                    let order = [];
                    $(this).find('tr').each(function(index, element) {
                        order.push({
                            id: $(this).data('id'),
                            position: index + 1
                        });
                    });

                    // 並び替えた順序をサーバーに送信
                    $.ajax({
                        type: "POST",
                        url: "{{ route('admin.mycostumes.sort') }}",
                        data: {
                            order: order,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            alert('並び替えが保存されました');
                        },
                        error: function(xhr) {
                            alert('並び替えの保存に失敗しました');
                        }
                    });
                }
            });
        });
    });
</script>



</x-admin-layout>
