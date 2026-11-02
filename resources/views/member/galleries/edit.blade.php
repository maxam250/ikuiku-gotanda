<x-member-layout>

<x-slot name="header">
ギャラリー編集
</x-slot>

<div class="back">
    <a href="{{ route('member.gallery.index') }}" class="member-btn member-btn-secondary">
        <i class="fas fa-arrow-left"></i>戻る
    </a>
</div>

<section>
    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section id="uploadArea">
        <h3>画像アップロード</h3>
        <form action="{{ route('member.gallery.image.upload', $gallery->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="file" name="images[]" multiple accept="image/*">
            <div class="member-btn-group">
                <button type="submit" class="member-btn member-btn-primary">
                    <i class="fas fa-upload"></i>アップロード
                </button>
            </div>
        </form>
    </section>


    @if($gallery->images->count() > 0)
    <section id="imageArea">
        <h3>アップロード済み画像</h3>
        <div class="gallery-images">
            @foreach($gallery->images->sortBy('sort') as $image)
            <div class="gallery-image">
                <div class="image-handle">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
                <div class="image-container">
                    <img src="{{ Storage::url('gallery/' . $image->filename) }}" alt="Gallery image" class="gallery-thumb" onclick="openModal('{{ Storage::url('gallery/' . $image->filename) }}')">
                </div>
                <form action="{{ route('member.gallery.image.destroy', [$gallery->id, $image->id]) }}" method="POST" class="delete-form">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="af-btn af-btn-delete af-btn-sm" onclick="return confirm('この画像を削除しますか？')">
                        <i class="fas fa-trash"></i>削除
                    </button>
                </form>
            </div>
            @endforeach
        </div>
    </section>

    <!-- モーダル -->
    <div id="imageModal" class="modal" onclick="closeModal()">
        <span class="close-button" onclick="closeModal()">&times;</span>
        <img id="modalImage" class="modal-content" src="" alt="Modal Image">
    </div>


    <section id="contentArea">
        <h3>ギャラリー情報編集</h3>
        <form action="{{ route('member.gallery.update', $gallery->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="flex flex-col md:flex-row md:items-center gap-4 p-2">
                <label for="active" class="font-medium text-gray-700 min-w-[120px] bg-gray-200 px-3 py-2 rounded-lg text-center">公開設定</label>
                <div class="text-gray-900">
                <select name="active" id="active">
                    <option value="0" @selected($gallery->active == 0)>非公開</option>
                    <option value="1" @selected($gallery->active == 1)>公開</option>
                </select>
                </div>
            </div>

            <div class="flex flex-col md:flex-row md:items-center gap-4 p-2">
                <label for="flag" class="font-medium text-gray-700 min-w-[120px] bg-gray-200 px-3 py-2 rounded-lg text-center">公開範囲</label>
                <div class="text-gray-900">
                <select name="flag" id="flag">
                    <option value="0" @selected($gallery->flag == 0)>全員に公開</option>
                    <option value="1" @selected($gallery->flag == 1)>ログイン会員のみ</option>
                    <option value="2" @selected($gallery->flag == 2)>限定公開</option>
                </select>
                </div>
            </div>

            <div class="flex flex-col w-full md:flex-row md:items-center gap-4 p-2">
                <label for="title" class="font-medium text-gray-700 min-w-[120px] bg-gray-200 px-3 py-2 rounded-lg text-center">タイトル</label>
                <div class="text-gray-900 w-full">
                <input type="text" name="title" id="title" value="{{ old('title', $gallery->title) }}" class="w-full" style="width:100%;" required>
                </div>
            </div>

            <div class="flex flex-col md:flex-row md:items-center gap-4 p-2">
                <label for="description" class="font-medium text-gray-700 min-w-[120px] bg-gray-200 px-3 py-2 rounded-lg text-center">説明</label>
                <div class="text-gray-900 w-full">
                <textarea name="description" id="description" class="w-full" rows="5">{{ old('description', $gallery->description) }}</textarea>
                </div>
            </div>

            <div class="flex flex-col md:flex-row md:items-center gap-4 p-2">
                <label for="item_id" class="font-medium text-gray-700 min-w-[120px] bg-gray-200 px-3 py-2 rounded-lg text-center">キャスト</label>
                <div class="text-gray-900">
                <select name="item_id" id="item_id" class="size_s">
                    <option value="">選択してください</option>
                    @foreach($items as $item)
                        <option value="{{ $item->id }}" @selected($gallery->item_id == $item->id)>
                            {{ $item->item_name }}
                        </option>
                    @endforeach
                </select>
                </div>
            </div>

            <div class="btnArea">
                <button type="submit" class="af-btn af-btn-primary">
                    <i class="fas fa-save"></i>更新する
                </button>
            </div>
        </form>
    </section>


    <script>
    function openModal(imageSrc) {
        const modal = document.getElementById('imageModal');
        const modalImg = document.getElementById('modalImage');
        modal.style.display = "flex";
        modalImg.src = imageSrc;
        document.body.style.overflow = 'hidden'; // スクロール防止
    }

    function closeModal() {
        const modal = document.getElementById('imageModal');
        modal.style.display = "none";
        document.body.style.overflow = 'auto'; // スクロール再開
    }

    // モーダル外クリックで閉じる
    window.onclick = function(event) {
        const modal = document.getElementById('imageModal');
        if (event.target == modal) {
            closeModal();
        }
    }

    // ESCキーでモーダルを閉じる
    document.addEventListener('keydown', function(event) {
        if (event.key === "Escape") {
            closeModal();
        }
    });
    </script>



<style>
.gallery-images {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 15px;
    padding: 15px;
    margin-bottom: 30px;
}

.gallery-image {
    position: relative;
    aspect-ratio: 1;
    background: #fff;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    cursor: move;
}

.image-container {
    width: 100%;
    height: 100%;
    overflow: hidden;
}

.gallery-thumb {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.gallery-image:hover .gallery-thumb {
    transform: scale(1.05);
}

.image-handle {
    position: absolute;
    top: 10px;
    right: 10px;
    width: 24px;
    height: 24px;
    background: rgba(0, 0, 0, 0.5);
    border-radius: 4px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    gap: 3px;
    padding: 5px;
    z-index: 2;
}

.image-handle span {
    display: block;
    height: 2px;
    background: #fff;
    border-radius: 1px;
}

.delete-form {
    position: absolute;
    bottom: 10px;
    right: 10px;
    z-index: 2;
}

.delete-form .btn.delete {
    background: rgba(220, 53, 69, 0.9);
    color: #fff;
    border: none;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.8rem;
}


@media (max-width: 768px) {
    .gallery-images {
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
        padding: 10px;
    }

    .image-handle {
        width: 20px;
        height: 20px;
        top: 5px;
        right: 5px;
    }

    .delete-form .btn.delete {
        font-size: 0.7rem;
        padding: 3px 6px;
    }
}

@media (min-width: 769px) and (max-width: 1024px) {
    .gallery-images {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (min-width: 1025px) {
    .gallery-images {
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    }
}

</style>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const galleryImages = document.querySelector('.gallery-images');
    if (galleryImages) {
        Sortable.create(galleryImages, {
            animation: 150,
            handle: '.image-handle',
            onEnd: function(evt) {
                const order = [];
                document.querySelectorAll('.gallery-image').forEach((image, index) => {
                    const formAction = image.querySelector('form').action;
                    const matches = formAction.match(/\/(\d+)$/);
                    const imageId = matches ? matches[1] : null;

                    if (imageId) {
                        order.push({
                            id: imageId,
                            position: index + 1
                        });
                    }
                });

                if (order.length > 0) {
                    fetch('{{ route('member.gallery.image.sort', $gallery->id) }}', {
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
                        if (data.status === 'success') {
                            console.log(data.message);
                        } else {
                            throw new Error(data.message || '並び替えの保存に失敗しました');
                        }
                    })
                    .catch(error => {
                        console.error('エラーが発生しました:', error);
                        alert('並び替えの保存に失敗しました。ページをリロードして再度お試しください。');
                    });
                }
            }
        });
    }
});
</script>
    @endif
</section>

</x-member-layout>
