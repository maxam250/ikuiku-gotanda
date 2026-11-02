
<x-item-layout>


<x-slot name="header">
 マイコスチューム
</x-slot>

<section class="toPageArea">
    <a href="{{ route('item.mycostumes.create') }}" class="af-btn af-btn-primary af-btn-sm">新規作成</a>
    <a href="{{ route('item.mycostumes.index') }}" class="af-btn af-btn-cancel af-btn-sm">マイコスチューム一覧</a>
</section>

<section>
    <div class="sortable-list">
        @foreach ($mycostumes as $mycostume)
            <div class="sortable-item bg-white border border-gray-200 rounded-lg p-1 relative cursor-move transition-all duration-200 hover:transform hover:-translate-y-1 hover:shadow-lg mx-0" data-id="{{ $mycostume->id }}">
                <div class="handle">
                    <span class="sort-handle">
                        <span></span><span></span><span></span>
                    </span>
                </div>
                
                <div class="w-full h-48 overflow-hidden rounded-md mb-3">
                    @if ($mycostume->filename)
                        <img src="{{ Storage::url('mycostume/' . $mycostume->filename) }}" alt="{{ $mycostume->name }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-gray-100 text-gray-500 text-sm">画像なし</div>
                    @endif
                </div>
                
                <div class="flex gap-2 mb-2">
                    <div class="status {{ $mycostume->active == 1 ? 'active-on' : 'active-off' }} px-2 py-1 rounded text-xs font-bold self-start">
                        {{ $mycostume->active == 1 ? '表示' : '非表示' }}
                    </div>
                    
                    <div class="text-xs text-gray-600 px-2 py-1 bg-gray-100 rounded self-start">
                        @foreach($mycostumeCates as $cate)
                            @if($mycostume->mycostume_cate_id == $cate->id)
                                {{ $cate->name }}
                            @endif
                        @endforeach
                    </div>
                </div>    
                <div class="flex flex-col gap-2">
                    <div class="title">
                        <h3 class="text-sm font-bold text-gray-800 m-0">{{ $mycostume->name }}</h3>
                    </div>
                    
                    {{-- <div class="text-lg font-bold text-red-500">
                        {{ number_format($mycostume->price) }}円
                    </div> --}}
                    
                    <div class="description">
                        <p class="text-xs text-gray-600 leading-relaxed m-0">{{ Str::limit($mycostume->body, 100) }}</p>
                    </div>
                    
                    <div class="action flex justify-center gap-2 mt-auto pt-3">
                        <a href="{{ route('item.mycostumes.edit', $mycostume) }}" class="af-btn af-btn-edit af-btn-sm">
                            <i class="fas fa-edit"></i>編集
                        </a>
                        <form action="{{ route('item.mycostumes.destroy', $mycostume) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button class="af-btn af-btn-delete af-btn-sm" onclick="return confirm('本当に削除しますか？')">
                                <i class="fas fa-trash"></i>削除
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</section>


<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const list = document.querySelector('.sortable-list');
    if (list) {
        Sortable.create(list, {
            animation: 150,
            handle: '.handle',
            onEnd: function() {
                const order = [];
                list.querySelectorAll('.sortable-item').forEach((box, index) => {
                    order.push({
                        id: box.dataset.id,
                        position: index + 1
                    });
                });

                fetch('{{ route('item.mycostumes.sort') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ order: order })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('並び替えが保存されました');
                })
                .catch(error => {
                    console.error('エラーが発生しました:', error);
                    alert('並び替えの保存に失敗しました。ページをリロードして再度お試しください。');
                });
            }
        });
    }
});
</script>

<style>



.handle {
    position: absolute;
    top: 10px;
    right: 15px;
    cursor: move;
}

.sortable-list {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    justify-content: flex-start;
}

.sortable-item {
    width: 260px;
    max-width: 100%;
}

.sort-handle span {
    display: block;
    width: 25px;
    height: 3px;
    background: #ccc;
    margin: 3px 0;
    border-radius: 2px;
}

.image-container {
    width: 100%;
    height: 200px;
    overflow: hidden;
    border-radius: 4px;
    margin-bottom: 10px;
}

.costume-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.no-image {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f5f5f5;
    color: #999;
    font-size: 0.9rem;
}

.content {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.status {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.8rem;
    font-weight: bold;
    align-self: flex-start;
}

.status.active-on {
    color: #28a745;
    background-color: rgba(40, 167, 69, 0.1);
}

.status.active-off {
    color: #777;
    background-color: rgba(102, 99, 99, 0.1);
}

.category {
    font-size: 0.8rem;
    color: #666;
    padding: 2px 6px;
    background: #f0f0f0;
    border-radius: 3px;
    align-self: flex-start;
}

.price {
    font-size: 1.2rem;
    font-weight: bold;
    color: #e74c3c;
}



.action {
    display: flex;
    gap: 8px;
    margin-top: auto;
}

.action {
    display: flex;
    justify-content: center;
    gap: 8px;
    margin-top: auto;
}

.action .funny-btn {
    flex: 1;
    padding: 8px 8px;
    font-size: 0.85rem;
    text-decoration: none;
    border-radius: 6px;
    text-align: center;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    height: 36px;
    white-space: nowrap;
}

.funny-btn-edit {
    background: #6c757d;
    color: #fff;
}

.funny-btn-edit:hover {
    background: #5a6268;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(108, 117, 125, 0.3);
}

.funny-btn-delete {
    background: #6c757d;
    color: #fff;
}

.funny-btn-delete:hover {
    background: #dc3545;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(220, 53, 69, 0.3);
}

.funny-btn i {
    margin-right: 4px;
    font-size: 0.75rem;
}

@media (max-width: 768px) {
    .sortable-item {
        width: calc(50% - 8px);
    }
    
    .image-container {
        height: 150px;
    }
}

@media (max-width: 480px) {
    .sortable-item {
        width: calc(50% - 8px);
    }
}
</style>



</x-item-layout>
