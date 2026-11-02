@php
use Illuminate\Support\Facades\Storage;
@endphp

<x-member-layout>

<x-slot name="header">
マイギャラリー
</x-slot>

<section>
    <div class="toPageArea">
        <a href="{{ route('member.gallery.create') }}" class="create">新しいギャラリーを作成</a>
    </div>

    <div class="list">

            @foreach ($galleries as $gallery)
                <div class="box" data-id="{{ $gallery->id }}">
                    <div class="handle center">
                      <span class="sort-handle">
                        <span></span><span></span><span></span>
                      </span>
                    </div>
                    <div class="active{{ $gallery->active == 1 ? ' on' : ' off' }}">
                        {{ $gallery->active == 1 ? '公開中' : '非公開' }}
                        @if($gallery->images->count() > 0)
                            <div class="thumbnail-container">
                                <a href="{{ route('member.gallery.edit', $gallery->id) }}">
                                    <img src="{{ Storage::url('gallery/' . $gallery->images->sortBy('sort')->first()->filename) }}"
                                        alt="サムネイル"
                                        class="thumbnail">
                                </a>
                            </div>
                        @endif
                    </div>
                    <dl class="dates">
                       <dt>UPDATE</dt><dd>{{ $gallery->created_at->format('Y/m/d') }}</dd>
                    </dl>
                    <div class="title">
                        <p>{{ $gallery->title }}</p>
                    </div>
                    <div class="auth auth{{ $gallery->flag }}">
                        公開範囲：
                        @switch($gallery->flag)
                            @case(0)
                                全員
                                @break
                            @case(1)
                                ログイン会員
                                @break
                            @case(2)
                                限定公開
                                @break
                            @default
                                全員
                        @endswitch
                    </div>
                    <div class="action">
                        <div class="af-btn-row">
                            <a href="{{ route('member.gallery.edit', $gallery->id) }}" class="af-btn af-btn-edit af-btn-sm">
                                <i class="fas fa-edit"></i>編集
                            </a>
                            <button type="submit" class="af-btn af-btn-delete af-btn-sm" onclick="return confirm('本当に削除しますか？')" form="delete-form-{{ $gallery->id }}">
                                <i class="fas fa-trash"></i>削除
                            </button>
                        </div>
                        <form action="{{ route('member.gallery.destroy', $gallery->id) }}" method="POST" id="delete-form-{{ $gallery->id }}" style="display: none;">
                            @csrf
                            @method('DELETE')
                        </form>
                    </div>
                </div>
            @endforeach
</section>


<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const list = document.querySelector('.list');
    if (list) {
        Sortable.create(list, {
            animation: 150,
            handle: '.handle',
            onEnd: function(evt) {
                const order = [];
                document.querySelectorAll('.box').forEach((box, index) => {
                    order.push({
                        id: box.dataset.id,
                        position: index + 1
                    });
                });

                fetch('{{ route('member.gallery.sort') }}', {
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
.list {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    padding: 20px;
}

.box {
    flex: 0 0 calc(33.333% - 20px);
    max-width:300px;
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 15px;
    position: relative;
    cursor: move;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    display: flex;
    flex-direction: column;
    gap: 10px;
    min-height: 200px;
}

.box:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.thumbnail-container {
    width: 100%;
    height: 200px;
    margin-top: 0.5rem;
    overflow: hidden;
    border-radius: 4px;
}

.thumbnail-container a {
    display: block;
    width: 100%;
    height: 100%;
}

.thumbnail {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    aspect-ratio: 9 / 6;
}

@media (max-width: 768px) {
    .list {
        padding: 0px;
    }
    .thumbnail-container {
        height: 100px;
    }
}

dl.dates{
    display: flex;
    justify-content: center;
    padding: 4px 8px;
    border-radius: 4px;
    background-color: rgba(240, 240, 240, 1);
}
.dates dt,.dates dd{
    display: inline-block;
    font-size: 0.7rem;
    color: #888;
}
.dates dt{
    margin-right: 0.6rem;
}
.active{
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.8rem;
    font-weight: bold;
}
.active.on {
    color: #28a745;
    background-color: rgba(40, 167, 69, 0.1);
}

.active.off {
    color: #777;
    background-color: rgba(102, 99, 99, 0.1);
}
.auth{
    margin-bottom:0.5rem;
    padding:0.2rem;
    font-size: 0.8rem;
    color: #555;
}
.auth0{
    background:#d5fcde;
}
.auth1{
    background:#cbe3ff;
}
.auth2{
    background:#f8deff;
}
.btnArea {
    display: flex;
    gap: 10px;
    justify-content: center;
    margin-top: 15px;
}

.handle {
    position: absolute;
    top: 10px;
    right: 15px;
    cursor: move;
}

.sort-handle span {
    display: block;
    width: 25px;
    height: 3px;
    background: #ccc;
    margin: 3px 0;
    border-radius: 2px;
}

.gallery-images {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    padding: 10px;
}

.gallery-image {
    position: relative;
    width: calc(33.333% - 10px);
    aspect-ratio: 1;
    overflow: hidden;
    border-radius: 4px;
    cursor: move;
}

.gallery-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.image-handle {
    position: absolute;
    top: 5px;
    right: 5px;
    width: 24px;
    height: 24px;
    background: rgba(0, 0, 0, 0.5);
    border-radius: 4px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    gap: 3px;
    padding: 5px;
    cursor: move;
}

.image-handle span {
    display: block;
    height: 2px;
    background: #fff;
    border-radius: 1px;
}
</style>
</x-member-layout>
