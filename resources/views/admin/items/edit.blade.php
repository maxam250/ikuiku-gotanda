<x-admin-layout>


<x-slot name="header">
キャスト編集
</x-slot>

<section class="PageTransitionArea">
    <div class="back">
    <a href="{{ route('admin.items.index') }}">戻る</a>
    </div>
</section>

<div class="link mt-3">
    <a href="{{ url('/items/' . $item->id) }}" target="_blank">{{ $item->item_name }}のプロフィールページ&nbsp;&raquo;</a>
</div>

<section class="MessageArea">
    @if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif


    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
</section>

<section id="ImgUploader">

    <div class="uploader-section photo">
        <h3><i class="fa-solid fa-image-portrait"></i> 画像アップロード&emsp;[ 推奨サイズ　横600 × 縦900ピクセル ]</h3>

        <section>
            <!-- ファイル選択フォーム -->
            <form id="imageUploadForm" enctype="multipart/form-data">
                <input type="file" name="image" id="imageInput" accept="image/*">
                <button type="submit" class="btn">画像アップロード</button>
            </form>
        </section>
    </div>
    <!-- 画像リスト -->
    <section id="imageList">
        <!-- 画像リスト -->
        <ul id="sortable" class="list">
        @foreach($images as $image)
            <li class="box" id="image-{{ $image->id }}">
                <div class="img">
                    <img src="{{ Storage::url($image->filename) }}" alt="Image">
                </div>

                <div class="file w-full">
                    <form id="replaceForm-{{ $image->id }}" enctype="multipart/form-data">
                        <input type="file" name="image" onchange="replaceImage({{ $image->id }})" class="w-36">
                    </form>
                </div>

                <div class="flex">
                    <div class="check">
                    <label>
                        <input type="radio" name="thumbnail_flag" value="{{ $image->id }}" {{ $image->flag ? 'checked' : '' }}> サムネイルに設定
                    </label>
                    </div>
                    <div class="del">
                    <button class="admin-btn admin-btn-warning admin-btn-sm" onclick="deleteImage({{ $image->id }})">
                        <i class="fas fa-trash"></i>削除
                    </button>
                    </div>
                </div>

            </li>
        @endforeach
        </ul>
    </section>



<script src="{{ asset('js/Sortable.min.js') }}"></script>

<script>
// 画像アップロード
document.getElementById('imageUploadForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    formData.append('_token', '{{ csrf_token() }}');

    fetch('/kanrigamen/items/{{ $item->id }}/upload-image', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(async response => {
        if (!response.ok) {
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                const text = await response.text();
                const cleanedText = text.replace(/^\uFEFF/, '').trim(); // BOMを除去
                try {
                    const err = JSON.parse(cleanedText);
                    throw new Error(err.message || 'サーバーエラー');
                } catch (e) {
                    if (e instanceof SyntaxError) {
                        throw new Error('サーバーエラー: レスポンスの解析に失敗しました');
                    }
                    throw e;
                }
            }
            throw new Error('サーバーエラーが発生しました');
        }
        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('application/json')) {
            const text = await response.text();
            const cleanedText = text.replace(/^\uFEFF+/, '').trim(); // BOMを除去
            try {
                return JSON.parse(cleanedText);
            } catch (e) {
                console.error('JSON parse error:', e, 'Response:', cleanedText.substring(0, 100));
                return { success: true, message: '処理が完了しました' };
            }
        }
        // JSONでない場合でも、テキストとして読み込む
        const text = await response.text();
        const cleanedText = text.replace(/^\uFEFF+/, '').trim(); // BOMを除去
        try {
            return JSON.parse(cleanedText);
        } catch {
            return { success: true, message: '処理が完了しました' };
        }
    })
    .then(data => {
        if (data && data.success) {
            // 画像リストの最後尾に新しい画像を追加
            const imageList = document.querySelector('#sortable');
            const newImage = document.createElement('li');
            newImage.className = 'box';
            newImage.id = `image-${data.image_id}`;
            newImage.innerHTML = `
                <div class="img">
                    <img src="${data.image_url}" alt="Image">
                </div>
                <div class="file">
                    <form id="replaceForm-${data.image_id}" enctype="multipart/form-data">
                        <input type="file" name="image" onchange="replaceImage(${data.image_id})">
                    </form>
                </div>
                <div class="flex">
                    <div class="check">
                        <label>
                            <input type="radio" name="thumbnail_flag" value="${data.image_id}"> サムネイルに設定
                        </label>
                    </div>
                    <div class="del">
                        <button class="admin-btn admin-btn-warning admin-btn-sm" onclick="deleteImage(${data.image_id})">
                            <i class="fas fa-trash"></i>削除
                        </button>
                    </div>
                </div>
            `;
            imageList.appendChild(newImage);

            // フォームをリセット
            this.reset();
            console.log('Image uploaded successfully');
        } else {
            alert(data ? (data.message || '画像のアップロードに失敗しました。') : 'エラーが発生しました①: レスポンスが不正です');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert(error.message || 'エラーが発生しました①');
    });
});

//画像の削除
function deleteImage(imageId) {
    if (confirm('この画像を削除してもよろしいですか？')) {
        fetch(`/kanrigamen/items/images/${imageId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(async response => {
            if (!response.ok) {
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    const text = await response.text();
                    const cleanedText = text.replace(/^\uFEFF+/, '').trim(); // BOMを除去
                    try {
                        const err = JSON.parse(cleanedText);
                        throw new Error(err.message || 'サーバーエラー');
                    } catch (e) {
                        if (e instanceof SyntaxError) {
                            throw new Error('サーバーエラー: レスポンスの解析に失敗しました');
                        }
                        throw e;
                    }
                }
                throw new Error('サーバーエラーが発生しました');
            }
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                const text = await response.text();
                const cleanedText = text.replace(/^\uFEFF+/, '').trim(); // BOMを除去
                try {
                    return JSON.parse(cleanedText);
                } catch (e) {
                    console.error('JSON parse error:', e);
                    return { success: true, message: '削除が完了しました' };
                }
            }
            const text = await response.text();
            const cleanedText = text.replace(/^\uFEFF+/, '').trim(); // BOMを除去
            try {
                return JSON.parse(cleanedText);
            } catch {
                return { success: true, message: '削除が完了しました' };
            }
        })
        .then(data => {
            if (data && data.success) {
                const imageElement = document.getElementById('image-' + imageId);
                // フェードアウトアニメーションを追加
                imageElement.classList.add('fadeOut');
                // アニメーションが終了した後に要素を削除
                imageElement.addEventListener('animationend', () => {
                    imageElement.remove();
                });
            } else {
                alert(data ? (data.message || '画像の削除に失敗しました。') : 'エラーが発生しました②: レスポンスが不正です');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert(error.message || 'エラーが発生しました②');
        });
    }
}

//サムネイルの設定
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('input[name="thumbnail_flag"]').forEach(function(radio) {
        radio.addEventListener('change', function() {
            const imageId = this.value;
            fetch('/kanrigamen/items/{{ $item->id }}/set-thumbnail', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ image_id: imageId })
            })
            .then(async response => {
                if (!response.ok) {
                    const contentType = response.headers.get('content-type');
                    if (contentType && contentType.includes('application/json')) {
                        const text = await response.text();
                        const cleanedText = text.replace(/^\uFEFF+/, '').trim(); // BOMを除去
                        try {
                            const err = JSON.parse(cleanedText);
                            throw new Error(err.message || 'サーバーエラー');
                        } catch (e) {
                            if (e instanceof SyntaxError) {
                                throw new Error('サーバーエラー: レスポンスの解析に失敗しました');
                            }
                            throw e;
                        }
                    }
                    throw new Error('サーバーエラーが発生しました');
                }
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    const text = await response.text();
                    const cleanedText = text.replace(/^\uFEFF+/, '').trim(); // BOMを除去
                    try {
                        return JSON.parse(cleanedText);
                    } catch (e) {
                        console.error('JSON parse error:', e);
                        return { success: true, message: '更新が完了しました' };
                    }
                }
                const text = await response.text();
                const cleanedText = text.replace(/^\uFEFF+/, '').trim(); // BOMを除去
                try {
                    return JSON.parse(cleanedText);
                } catch {
                    return { success: true, message: '更新が完了しました' };
                }
            })
            .then(data => {
                if (data && data.success) {
                    // 他のラジオボタンのチェックを外す
                    document.querySelectorAll('input[name="thumbnail_flag"]').forEach(function(otherRadio) {
                        if (otherRadio !== radio) {
                            otherRadio.checked = false;
                        }
                    });
                    console.log('Thumbnail updated');
                } else {
                    alert(data ? (data.message || 'サムネイルの設定に失敗しました。') : 'エラーが発生しました③: レスポンスが不正です');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert(error.message || 'エラーが発生しました③');
            });
        });
    });
});

// 画像のソート
document.addEventListener('DOMContentLoaded', function() {
    var el = document.getElementById('sortable');
    var sortable = new Sortable(el, {
        animation: 150,
        ghostClass: 'sortable-ghost',
        onEnd: function (/**Event*/evt) {
            updateOrder();
        }
    });

    function updateOrder() {
        var order = [];
        document.querySelectorAll('#sortable .box').forEach(function(item, index) {
            const imageId = item.id.split('-')[1];
            if (imageId) {
                order.push({ id: imageId, item_sort: index + 1 });
            }
        });

        if (order.length > 0) {
            fetch('/kanrigamen/items/{{ $item->id }}/sort-image', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ order: order })
            })
            .then(async response => {
                if (!response.ok) {
                    throw new Error('ソートの更新に失敗しました');
                }
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    const text = await response.text();
                    const cleanedText = text.replace(/^\uFEFF+/, '').trim(); // BOMを除去
                    try {
                        return JSON.parse(cleanedText);
                    } catch (e) {
                        console.error('JSON parse error:', e);
                        return { success: true };
                    }
                }
                const text = await response.text();
                const cleanedText = text.replace(/^\uFEFF+/, '').trim(); // BOMを除去
                try {
                    return JSON.parse(cleanedText);
                } catch {
                    return { success: true };
                }
            })
            .then(data => {
                if (data && data.success) {
                    console.log('Order updated successfully');
                } else {
                    console.error('Failed to update order');
                }
            })
            .catch(error => {
                console.error('Error updating order:', error);
            });
        }
    }
});

// 画像の差し替え
function replaceImage(imageId) {
    const form = document.getElementById(`replaceForm-${imageId}`);
    const formData = new FormData(form);
    formData.append('_token', '{{ csrf_token() }}');

    fetch(`/kanrigamen/items/${imageId}/replace-image`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(async response => {
        if (!response.ok) {
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                const text = await response.text();
                const cleanedText = text.replace(/^\uFEFF+/, '').trim(); // BOMを除去
                try {
                    const err = JSON.parse(cleanedText);
                    throw new Error(err.message || 'サーバーエラー');
                } catch (e) {
                    if (e instanceof SyntaxError) {
                        throw new Error('サーバーエラー: レスポンスの解析に失敗しました');
                    }
                    throw e;
                }
            }
            throw new Error('サーバーエラーが発生しました');
        }
        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('application/json')) {
            const text = await response.text();
            const cleanedText = text.replace(/^\uFEFF+/, '').trim(); // BOMを除去
            try {
                return JSON.parse(cleanedText);
            } catch (e) {
                console.error('JSON parse error:', e);
                return { success: true, message: '画像の差し替えが完了しました' };
            }
        }
        const text = await response.text();
        const cleanedText = text.replace(/^\uFEFF+/, '').trim(); // BOMを除去
        try {
            return JSON.parse(cleanedText);
        } catch {
            return { success: true, message: '画像の差し替えが完了しました' };
        }
    })
    .then(data => {
        if (data && data.success) {
            // 画像を更新
            const imgElement = document.querySelector(`#image-${imageId} .img img`);
            if (imgElement) {
                imgElement.src = data.newImageUrl;
                console.log('Image replaced successfully');
            } else {
                alert('エラーが発生しました④: 画像要素が見つかりません');
            }
        } else {
            alert(data ? (data.message || '画像の差し替えに失敗しました。') : 'エラーが発生しました④: レスポンスが不正です');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert(error.message || 'エラーが発生しました④');
    });
}
</script>

</section>

<section id="ThumbnailVideoUploader">

    <div class="uploader-section thumb">
    <h3><i class="fa-solid fa-clapperboard"></i> サムネ動画（mp4）アップロード&emsp;[ MP4のみ &nbsp;最大1MB / 自動圧縮します ]</h3>

    <section style="margin:0 0 1rem;">
        <form id="thumbnailVideoUploadForm" enctype="multipart/form-data">
            <input type="file" name="video" id="thumbnailVideoInput" accept="video/mp4">
            <button type="submit" class="btn">サムネ動画アップロード</button>
        </form>
        <small style="color:#888;display:block;margin-top:0.3rem;">アップロード後、FFmpegで約300KBに自動圧縮されます。縦長（ポートレート）推奨。</small>
    </section>
    </div>

    <section id="thumbnailVideoDisplay">
        @if($item->thumbnail_video)
        <div id="thumbnailVideoWrapper" style="margin-bottom:1rem;">
            <video id="thumbnailVideoPreview" src="{{ Storage::url($item->thumbnail_video) }}" controls muted style="max-width:200px;display:block;margin-bottom:0.5rem;"></video>
            <button type="button" class="admin-btn admin-btn-warning admin-btn-sm" onclick="deleteThumbnailVideo()">
                <i class="fas fa-trash"></i>サムネ動画を削除
            </button>
        </div>
        @else
        <div id="thumbnailVideoWrapper" style="display:none;margin-bottom:1rem;">
            <video id="thumbnailVideoPreview" src="" controls muted style="max-width:200px;display:block;margin-bottom:0.5rem;"></video>
            <button type="button" class="admin-btn admin-btn-warning admin-btn-sm" onclick="deleteThumbnailVideo()">
                <i class="fas fa-trash"></i>サムネ動画を削除
            </button>
        </div>
        @endif
    </section>

<script>
document.getElementById('thumbnailVideoUploadForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const fileInput = document.getElementById('thumbnailVideoInput');
    if (!fileInput.files.length) {
        alert('ファイルを選択してください');
        return;
    }
    const btn = this.querySelector('button[type="submit"]');
    btn.disabled = true;
    btn.textContent = 'アップロード中...（圧縮処理があるため少々お待ちください）';

    const formData = new FormData(this);
    formData.append('_token', '{{ csrf_token() }}');

    fetch('/kanrigamen/items/{{ $item->id }}/upload-thumbnail-video', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(async response => {
        const text = await response.text();
        const cleanedText = text.replace(/^\uFEFF+/, '').trim();
        try { return JSON.parse(cleanedText); } catch { return { success: false, message: 'エラー' }; }
    })
    .then(data => {
        btn.disabled = false;
        btn.textContent = 'サムネ動画アップロード';
        if (data && data.success) {
            const wrapper = document.getElementById('thumbnailVideoWrapper');
            const preview = document.getElementById('thumbnailVideoPreview');
            preview.src = data.video_url;
            wrapper.style.display = 'block';
            this.reset();
        } else {
            alert(data ? (data.message || 'サムネ動画のアップロードに失敗しました') : 'エラーが発生しました');
        }
    })
    .catch(error => {
        btn.disabled = false;
        btn.textContent = 'サムネ動画アップロード';
        alert(error.message || 'エラーが発生しました');
    });
});

function deleteThumbnailVideo() {
    if (!confirm('サムネ動画を削除してもよろしいですか？')) return;
    fetch('/kanrigamen/items/{{ $item->id }}/delete-thumbnail-video', {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(async response => {
        const text = await response.text();
        const cleanedText = text.replace(/^\uFEFF+/, '').trim();
        try { return JSON.parse(cleanedText); } catch { return { success: false }; }
    })
    .then(data => {
        if (data && data.success) {
            const wrapper = document.getElementById('thumbnailVideoWrapper');
            const preview = document.getElementById('thumbnailVideoPreview');
            preview.src = '';
            wrapper.style.display = 'none';
        } else {
            alert(data ? (data.message || 'サムネ動画の削除に失敗しました') : 'エラーが発生しました');
        }
    })
    .catch(error => { alert(error.message || 'エラーが発生しました'); });
}
</script>

</section>

<section id="VideoUploader">

    <div class="uploader-section video">
    <h3><i class="fa-solid fa-film"></i> 動画アップロード&emsp;[ MP4 / MOV / WEBM &nbsp;最大50MB &nbsp;※自動圧縮 ]</h3>

    <section style="margin:0 0 1rem;">
        <form id="videoUploadForm" enctype="multipart/form-data">
            <input type="file" name="video" id="videoInput" accept="video/*">
            <button type="submit" class="btn">動画アップロード</button>
        </form>
    </section>
    </div>
<section id="videoDisplay">
    @if($item->item_video)
    <div id="videoWrapper" style="margin-bottom:1rem;">
        @if(str_starts_with($item->item_video, 'http'))
            {{-- 外部URL（TikTok等） --}}
            <p style="margin-bottom:0.5rem;">
                <a href="{{ $item->item_video }}" target="_blank" rel="noopener noreferrer">{{ $item->item_video }}</a>
                <small style="color:#888;">（外部URLのため削除はDBから直接対応してください）</small>
            </p>
            <p style="color:#e67e22;font-size:0.85rem;">※ 上から動画ファイルをアップロードすると上書きされます</p>
        @else
            <video id="videoPreview" src="{{ Storage::url($item->item_video) }}" controls style="max-width:400px;display:block;margin-bottom:0.5rem;"></video>
            <button type="button" class="admin-btn admin-btn-warning admin-btn-sm" onclick="deleteVideo()">
                <i class="fas fa-trash"></i>動画を削除
            </button>
        @endif
    </div>
    @else
    <div id="videoWrapper" style="display:none;margin-bottom:1rem;">
        <video id="videoPreview" src="" controls style="max-width:400px;display:block;margin-bottom:0.5rem;"></video>
        <button type="button" class="admin-btn admin-btn-warning admin-btn-sm" onclick="deleteVideo()">
            <i class="fas fa-trash"></i>動画を削除
        </button>
    </div>
    @endif
</section>

<script>
document.getElementById('videoUploadForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    formData.append('_token', '{{ csrf_token() }}');

    fetch('/kanrigamen/items/{{ $item->id }}/upload-video', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(async response => {
        const text = await response.text();
        const cleanedText = text.replace(/^\uFEFF+/, '').trim();
        try { return JSON.parse(cleanedText); } catch { return { success: false, message: 'エラー' }; }
    })
    .then(data => {
        if (data && data.success) {
            const wrapper = document.getElementById('videoWrapper');
            const preview = document.getElementById('videoPreview');
            preview.src = data.video_url;
            wrapper.style.display = 'block';
            this.reset();
        } else {
            alert(data ? (data.message || '動画のアップロードに失敗しました') : 'エラーが発生しました');
        }
    })
    .catch(error => { alert(error.message || 'エラーが発生しました'); });
});

function deleteVideo() {
    if (!confirm('この動画を削除してもよろしいですか？')) return;
    fetch('/kanrigamen/items/{{ $item->id }}/delete-video', {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(async response => {
        const text = await response.text();
        const cleanedText = text.replace(/^\uFEFF+/, '').trim();
        try { return JSON.parse(cleanedText); } catch { return { success: false }; }
    })
    .then(data => {
        if (data && data.success) {
            const wrapper = document.getElementById('videoWrapper');
            const preview = document.getElementById('videoPreview');
            preview.src = '';
            wrapper.style.display = 'none';
        } else {
            alert(data ? (data.message || '動画の削除に失敗しました') : 'エラーが発生しました');
        }
    })
    .catch(error => { alert(error.message || 'エラーが発生しました'); });
}
</script>

</section>

{{-- 画像リスト
<section id="imageList">
<!-- 画像リスト -->
<ul class="list">
    @foreach ($images as $image)
    <li class="box" id="image-{{ $image->id }}">
        <div class="img">
            <img src="{{ Storage::url($image->filename) }}" alt="Image">
        </div>

        <div class="file">
            <form id="replaceForm-{{ $image->id }}" enctype="multipart/form-data">
                <input type="file" name="image" onchange="replaceImage({{ $image->id }})">
            </form>
        </div>
        <div class="flex">
            <div class="check">
            <label>
                <input type="radio" name="thumbnail_flag" value="{{ $image->id }}" {{ $image->flag ? 'checked' : '' }}> サムネイルに設定
            </label>
            </div>
            <div class="del">
            <button class="admin-btn admin-btn-warning admin-btn-sm" onclick="deleteImage({{ $image->id }})">
                <i class="fas fa-trash"></i>削除
            </button>
            </div>
        </div>
    </li>
@endforeach
</ul>

</section>
--}}


<section id="loginSection">
    <form method="POST" action="{{ route('admin.items.updateLogin', $item->id) }}">
        @csrf
        @method('patch')
    <table>
        <tr>
            <th>メール</th>
            <td>
                <input type="text" name="email" value="{{ old('email', $item->email) }}" class="size_m"></td>
        </tr>
        <tr>
            <th>電話番号</th>
            <td>
                <input type="text" name="item_tel" value="{{ old('item_tel', $item->item_tel) }}" class="size_m"></td>
        </tr>
        <tr>
            <th>LINE</th>
            <td>
                <input type="text" name="item_line" value="{{ old('item_line', $item->item_line) }}" class="size_m"></td>
        </tr>
        <tr>
            <th>ログインID</th>
            <td>
                <input type="text" name="loginid" value="{{ old('loginid', $item->loginid) }}" class="size_m"></td>
        </tr>
        <tr>
            <th>パスワード</th>
            <td>
                <input type="password" name="password" placeholder="変更する場合のみ入力" class="size_m">
                @if(!empty($item->item_pass_text))
                <div style="margin-top:4px;font-size:0.8rem;color:#6b7280;">
                    現在: <code style="background:#1e293b;color:#86efac;padding:2px 6px;border-radius:3px;font-size:0.85rem;">{{ $item->item_pass_text }}</code>
                </div>
                @endif
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <button type="submit" class="admin-btn admin-btn-submit">
                    <i class="fas fa-save"></i>更新
                </button>
            </td>
        </tr>
    </table>
    </form>
</section>



<div class="tab-wrap">



<input id="tab-info" type="radio" name="tabs" class="tab-switch" checked>
<label class="tab-label" for="tab-info">キャスト情報</label>

<section id="itemInfoArea" class="editSection tab-content">

    <form method="POST" action="{{ route('admin.items.update', $item) }}">
        @csrf
        @method('patch')
    <table>

        <tr>
            <th><label>公開:</label></th>
            <td class="form_parts">
                <input type="radio" name="item_active" id="item_active1" value="1" {{ $item->item_active ? 'checked' : '' }}><label class="radio" for="item_active1">公開</label>
                <input type="radio" name="item_active" id="item_active0" value="0" {{ !$item->item_active ? 'checked' : '' }}><label class="radio" for="item_active0">非公開</label>
            </td>
        </tr>
        <tr>
            <th><label for="item_cate">種別:</label></th>
            <td class="form_parts">
                <input type="radio" name="item_cate" id="item_cate0" value="0" {{ $item->item_cate == 0 ? 'checked' : '' }}><label class="radio" for="item_cate0">キャスト</label>
                {{-- <input type="radio" name="item_cate" id="item_cate1" value="1" {{ $item->item_cate == 1 ? 'checked' : '' }}><label class="radio" for="item_cate1">プレミア</label>
                <input type="radio" name="item_cate" id="item_cate2" value="2" {{ $item->item_cate == 2 ? 'checked' : '' }}><label class="radio" for="item_cate2">プラチナ</label> --}}
                <input type="radio" name="item_cate" id="item_cate3" value="3" {{ $item->item_cate == 3 ? 'checked' : '' }}><label class="radio" for="item_cate3">バナー</label>
                <span class="text-xs">（バナーはスリーサイズを非表示）</span>
            </td>
        </tr>
        {{--
        <tr>
            <th><label for="item_cate">可能コース:</label></th>
            <td class="form_parts">
                <!-- 可能コースのラジオは Ajax のみで使用。メイン更新フォームに含めない（fill への混入防止） -->
                <form id="course-status-forms-helper" style="display:none" aria-hidden="true"></form>
                <div class="course-settings">
                    @foreach($courses as $course)
                        @php
                            $currentStatus = isset($courseStatuses[$course->id]) ? $courseStatuses[$course->id] : 'ng';
                        @endphp
                        <div class="course-item" data-course-id="{{ $course->id }}">
                            <span class="course-name">{{ $course->title }}</span>
                            <div class="course-status">
                                <input type="radio" class="course-status-radio" form="course-status-forms-helper" name="course_status[{{ $course->id }}]" id="course_status_{{ $course->id }}_ok" value="ok"
                                    {{ $currentStatus == 'ok' ? 'checked' : '' }}>
                                <label class="radio" for="course_status_{{ $course->id }}_ok">OK</label>
                                <input type="radio" class="course-status-radio" form="course-status-forms-helper" name="course_status[{{ $course->id }}]" id="course_status_{{ $course->id }}_ng" value="ng"
                                    {{ $currentStatus == 'ng' ? 'checked' : '' }}>
                                <label class="radio" for="course_status_{{ $course->id }}_ng">NG</label>
                                <input type="radio" class="course-status-radio" form="course-status-forms-helper" name="course_status[{{ $course->id }}]" id="course_status_{{ $course->id }}_confirm" value="confirm"
                                    {{ $currentStatus == 'confirm' ? 'checked' : '' }}>
                                <label class="radio" for="course_status_{{ $course->id }}_confirm">要確認</label>
                            </div>
                        </div>
                    @endforeach
                </div>
            </td>
        </tr>
         --}}
        <tr>
            <th><label for="premium_rank_id">プレミアランク</label></th>
            <td class="form_parts">
                @foreach ($premiumRanks as $premiumRank)
                    <input type="radio" name="premium_rank_id" id="premium_rank_{{ $premiumRank->id }}" value="{{ $premiumRank->id }}"
                        {{ $item->premium_rank_id == $premiumRank->id ? 'checked' : '' }}>
                    <label class="radio" for="premium_rank_{{ $premiumRank->id }}">{{ $premiumRank->name }}</label>
                @endforeach
            </td>
        </tr>
        <tr>
            <th>タイプ</th>
            <td class="form_parts">
                <input type="radio" name="item_category_id" id="item_category_id_none" value="" @checked($item->item_category_id == null)><label class="radio" for="item_category_id_none">未設定</label>
                @foreach(\App\Models\ItemCategory::orderBy('sort')->get() as $cat)
                    <input type="radio" name="item_category_id" id="item_category_id_{{ $cat->id }}" value="{{ $cat->id }}" @checked($item->item_category_id == $cat->id)><label class="radio" for="item_category_id_{{ $cat->id }}">{{ $cat->name }}</label>
                @endforeach
            </td>
        </tr>
        <tr>
            <th><label for="item_name">名前:</label></th>
            <td>
                <input type="text" id="item_name" style="width:200px" name="item_name" value="{{ $item->item_name }}" required></td>
        </tr>
        <tr>
            <th>年齢</th>
            <td>
                <select name="item_age" style="width:80px">
                    <option value="0"
                    @if( $item->item_age == 0 )
                     selected
                     @endif
                     > 未設定 </option>
                    @for ($a = 18; $a <= 40; $a++)
                        <option value="{{ $a }}"
                        @if( $a == $item->item_age )
                         selected
                         @endif
                         >{{ $a }}</option>
                    @endfor
                    </select> 歳
        </td>
        </tr>
        <tr>
            <th>サイズ</th>
            <td>
            @php
                //初期値の設定
                $df_height = $item->item_height; // ?? 155
                $df_b = $item->item_b; // ?? 85
                $df_w = $item->item_w; // ?? 58
                $df_h = $item->item_h; // ?? 85
                $df_s = $item->item_shoes; // ?? 23
            @endphp

                T <select name="item_height" style="width:80px">
                    <option value=""
                    @if( $item->item_height == '-' )
                         selected
                    @endif
                         >-</option>
                @for ($t = 135; $t <= 190; $t++)
                    <option value="{{ $t }}"
                    @if( $t == $df_height )
                     selected
                    @endif
                     >{{ $t }}</option>
                @endfor
                </select>&nbsp;
                B <select name="item_b" style="width:80px">
                    <option value=""
                    @if( empty($item->item_b) )
                         selected
                    @endif
                         >-</option>
                @for ($b = 70; $b <= 100; $b++)
                    <option value="{{ $b }}"
                    @if( $b == $df_b )
                     selected
                    @endif
                     >{{ $b }}</option>
                @endfor
                </select>&nbsp;
                @php
                $cups = range('A', 'K');
                @endphp
                CUP <select name="item_cup" style="width:80px">
                    <option value="-"
                    @if( $item->item_cup == '-' )
                         selected
                    @endif
                         >-</option>
                    @foreach ($cups as $cup)
                        <option value="{{ $cup }}"
                        @if( $cup == $item->item_cup )
                         selected
                         @endif
                         >{{ $cup }}</option>
                    @endforeach
                 </select>&nbsp;

                W <select name="item_w" style="width:80px">
                    <option value=""
                    @if( empty($item->item_w) )
                         selected
                    @endif
                         >-</option>
                @for ($w = 50; $w <= 90; $w++)
                    <option value="{{ $w }}"
                    @if( $w == $df_w )
                     selected
                    @endif
                     >{{ $w }}</option>
                @endfor
                </select>&nbsp;
                H <select name="item_h" style="width:80px">
                    <option value=""
                    @if( empty($item->item_h) )
                         selected
                    @endif
                         >-</option>
                @for ($h = 70; $h <= 100; $h++)
                    <option value="{{ $h }}"
                    @if( $h == $df_h )
                     selected
                    @endif
                     >{{ $h }}</option>
                @endfor
                </select>&nbsp;
                {{--
                靴 <select name="item_shoes">
                    <option value=""
                    @if( empty($item->item_shoes) )
                         selected
                    @endif
                         >-</option>
                @for ($s = 22; $s <= 28; $s++)
                <option value="{{ $s }}"
                @if( $s == $df_s )
                 selected
                @endif
                 >{{ $s }}</option>
                 <option value="{{ $s }}.5"
                 @if( ($s.".5") == $item->item_shoes )
                  selected
                  @endif
                  >{{ $s }}.5</option>
                @endfor
                </select>&nbsp;
                --}}
            </td>
        </tr>
        <tr>
            <th>キャッチコピー(20文字)</th>
            <td>
                <input type="text" name="item_catch" value="{{ old('item_catch', $item->item_catch) }}" style="width:100%;max-width:500px;">
            </td>
        </tr>
        <tr>
            <th></th>
            <td>
                <span class="text-base font-semibold text-gray-700 bg-gray-100 p-2">エディタの改行はshift + Enterで改行されます。Enterのみで改行すると、HTML上では段落として認識されます。</span>
            </td>
        </tr>
        <tr>
            <th>店長コメント<p class="text-xs text-red-500">文字推奨サイズ18～22</p></th>
            <td>
                <textarea id="item_comment2" name="item_comment2" style="width: 100%;height:350px;">{{ old('item_comment2', $item->item_comment2) }}</textarea></td>
        </tr>
        <tr>
            <th>Q&amp;A<p class="text-xs text-red-500">文字推奨サイズ18～22</p></th>
            <td>
                <textarea id="item_comment_faq" name="item_comment_faq" style="width: 100%;height:350px;">{{ old('item_comment_faq', $item->item_comment_faq) }}</textarea></td>
        </tr>
        <tr>
            <th>X ID</th>
            <td>
                <input type="text"  name="item_twitter" value="{{ old('item_twitter', $item->item_twitter) }}" class="size_m" placeholder="@は不要　IDのみ"></td>
        </tr>
        <tr>
            <th>写メ日記URL</th>
            <td>
                <input type="text" name="item_url" value="{{ old('item_url', $item->item_url) }}" class="size_m"></td>
        </tr>
        <tr>
            <th>写メ日記ウィジェット</th>
            <td>
                <textarea name="item_shame_wiget" style="width:100%;height:5rem;" placeholder="シティヘブン等のウィジェットタグ（プロフィールの「写メ日記」に表示）">{{ old('item_shame_wiget', $item->item_shame_wiget) }}</textarea></td>
        </tr>
        <tr>
            <th>口コミウィジェット</th>
            <td>
                <textarea name="item_review_wiget" style="width:100%;height:5rem;" placeholder="ウィジェットタグ（プロフィールの「口コミ」に表示）">{{ old('item_review_wiget', $item->item_review_wiget) }}</textarea></td>
        </tr>
        <tr>
            <th>出勤ウィジェット</th>
            <td>
                <textarea name="item_sch_wiget" style="width:100%;height:5rem;" placeholder="ウィジェットタグ（プロフィールの出勤スケジュールの下に表示）">{{ old('item_sch_wiget', $item->item_sch_wiget) }}</textarea></td>
        </tr>
        <tr>
            <th>外部動画サイトURL</th>
            <td>
                <input type="text" name="item_youtube" value="{{ old('item_youtube', $item->item_youtube) }}" class="size_m"></td>
        </tr>
        {{-- <tr>
            <th>コメント（キャスト）</th>
            <td>
                <textarea id="item_comment2" name="item_comment2" style="width: 100%;height:350px;">{{ old('item_comment2', $item->item_comment2) }}</textarea></td>
        </tr> --}}
        {{--
        <tr>
            <th>風じゃエロボイスURL</th>
            <td>
                <input type="text" name="item_blog" value="{{ old('item_blog', $item->item_blog) }}" class="size_m"></td>
        </tr>
        <tr>
            <th>Q&amp;Aコメント</th>
            <td>
                <textarea id="item_comment_faq" name="item_comment_faq" style="width: 100%;height:350px;">{{ old('item_comment_faq', $item->item_comment_faq) }}</textarea></td>
        </tr>
        <tr>
            <th>ブログ名</th>
            <td>
                <input type="text" name="item_blogname" value="{{ old('item_blogname', $item->item_blogname) }}" class="size_m"></td>
        </tr>
        <tr>
            <th>ブログURL</th>
            <td>
                <input type="text" name="item_blog" value="{{ old('item_blog', $item->item_blog) }}" class="size_m"></td>
        </tr>
        <tr>
            <th>インスタグラム</th>
            <td>
                <input type="text" name="item_instagram" value="{{ old('item_instagram', $item->item_instagram) }}" class="size_m"></td>
        </tr>
        <tr>
            <th>TikTok</th>
            <td>
                <input type="text" name="item_tiktok" value="{{ old('item_tiktok', $item->item_tiktok) }}" class="size_m"></td>
        </tr>
        <tr>
            <th>本指名コメント<p class="text-xs text-red-500">文字推奨サイズ16</p></th>
            <td>
                <textarea id="item_p_comment" name="item_p_comment" style="width: 100%;">{{ old('item_p_comment', $item->item_p_comment) }}</textarea></td>
        </tr>
        --}}
        <tr>
            <th>本指名コメント</th>
            <td>
                <textarea id="item_comment_nominate" name="item_comment_nominate" style="width: 100%;height:350px;">{{ old('item_comment_nominate', $item->item_comment_nominate) }}</textarea></td>
        </tr>
        <tr>
            <th>デフォルト出勤時間</th>
            <td>
                <div class="flex gap-4">
                    <div>
                        <label for="default_sch_start" class="block text-sm font-medium text-gray-700 mb-1">
                            開始時間
                        </label>
                        <select name="default_sch_start" id="default_sch_start" class="size_m">
                            <option value="">選択してください</option>
                            @foreach (generateTimeOptions('09:00', '44:00') as $time => $label)
                                <option value="{{ $time }}" {{ $item->default_sch_start && $item->default_sch_start == $time ? 'selected' : '' }}>
                                    @if ($time > '24:00:00')
                                        翌
                                    @endif
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="default_sch_end" class="block text-sm font-medium text-gray-700 mb-1">
                            終了時間
                        </label>
                        <select name="default_sch_end" id="default_sch_end" class="size_m">
                            <option value="">選択してください</option>
                            @foreach (generateTimeOptions('10:00', '44:00') as $time => $label)
                                <option value="{{ $time }}" {{ $item->default_sch_end && $item->default_sch_end == $time ? 'selected' : '' }}>
                                    @if ($time > '24:00:00')
                                        翌
                                    @endif
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <button type="submit" class="admin-btn admin-btn-submit">
                    <i class="fas fa-save"></i>更新
                </button>
            </td>
        </tr>
    </table>
    </form>

</section>

@if ($options->isNotEmpty())
<input id="tab-option" type="radio" name="tabs" class="tab-switch">
<label class="tab-label" for="tab-option">オプション</label>

<section id="optionArea" class="editSection tab-content">
    <h3>オプション
    <span>※チェックをON/OFFで即時反映されます</span></h3>
    <div class="option-checkbox-grid">
    @foreach($options as $option)
    <label>
        <input type="checkbox" name="options[]" value="{{ $option->id }}"
        {{ in_array($option->id, $selectedOptions) ? 'checked' : '' }}
        class="option-checkbox" data-option-id="{{ $option->id }}">
        {{ $option->title }}
    </label>
    @endforeach
    </div>
</section>
@endif

@if ($option_pays->isNotEmpty())
<input id="tab-option-pay" type="radio" name="tabs" class="tab-switch">
<label class="tab-label" for="tab-option-pay">オプションPAY</label>

<section id="optionPayArea" class="editSection tab-content">
    <h3>オプションPAY
    <span>※チェックをON/OFFで即時反映されます</span></h3>
    @foreach($option_pays as $option_pay)
    <label>
        <input type="checkbox" name="option_pays[]" value="{{ $option_pay->id }}"
        {{ in_array($option_pay->id, $selectedOptionPays) ? 'checked' : '' }}
        class="option-pay-checkbox" data-option-pay-id="{{ $option_pay->id }}">
        {{ $option_pay->title }}
    </label>
    @endforeach
</section>
@endif
{{--
<section>
    <h3>プロフィール</h3>
    @foreach($profiles as $profile)
    <label>
        <input type="text" name="profiles[]" value="{{ $profile->title }}" class="profile-text" data-profile-id="{{ $profile->id }}">
    </label><br>
@endforeach
</section>
--}}

<!-- url: '{{ route("admin.items.updateProfile", ["id" => $item->id]) }}', -->

@if ($profiles->isNotEmpty())
<input id="tab-profile" type="radio" name="tabs" class="tab-switch">
<label class="tab-label" for="tab-profile">プロフィール</label>

<section id="profileArea" class="editSection tab-content">
    <h3>プロフィール</h3>
    <form action="{{ route('admin.items.updateProfile', $item->id) }}" method="POST">
        @csrf
        @method('patch')
        <div>
            @foreach ($profiles as $profile)
                <div class="mb-2">
                    <p class="p-2 bg-gray-100">Q. {{ $profile->title }}</p>
                    @php
                        // キャストに紐づいたプロフィールの内容を取得
                        $content = $item->profiles->where('id', $profile->id)->first()->pivot->content ?? '';
                    @endphp
                    <input type="text" name="profiles[{{ $profile->id }}]" value="{{ $content }}" class="w-full">
                </div>
            @endforeach
        </div>
        <button type="submit" class="admin-btn admin-btn-submit">
            <i class="fas fa-save"></i>更新
        </button>
    </form>
</section>
@endif

@if ($interviews->isNotEmpty())
<input id="tab-interview" type="radio" name="tabs" class="tab-switch">
<label class="tab-label" for="tab-interview">Q&A</label>

<section id="interviewArea" class="editSection tab-content">
    <h3>Q&A</h3>
    <form action="{{ route('admin.items.updateInterview', $item->id) }}" method="POST">
        @csrf
        @method('patch')
        <div>
            @foreach ($interviews as $interview)
                <div class="mb-2">
                    <p class="p-2 bg-gray-100">Q. {{ $interview->title }}</p>
                    @php
                        // キャストに紐づいたインタビューの内容を取得
                        $content = $item->interviews->where('id', $interview->id)->first()->pivot->content ?? '';
                    @endphp
                    <textarea name="interviews[{{ $interview->id }}]" value="{{ $content }}" class="w-full">{{ $content }}</textarea>
                </div>
            @endforeach
        </div>
        <button type="submit" class="admin-btn admin-btn-submit">
            <i class="fas fa-save"></i>更新
        </button>
    </form>
</section>
@endif

@if ($icon_items->isNotEmpty())
<input id="tab-icon" type="radio" name="tabs" class="tab-switch">
<label class="tab-label" for="tab-icon">キャストアイコン</label>

<section id="itemIconArea" class="editSection tab-content">
    <h3>キャストアイコン</h3>
    @foreach($icon_items as $icon_item)
    <label>
        <input type="checkbox" name="icon_items[]" value="{{ $icon_item->id }}"
        {{ in_array($icon_item->id, $selectedIconItems) ? 'checked' : '' }}
        class="iconItem-checkbox" data-iconitem-id="{{ $icon_item->id }}">
        <p>{{ $icon_item->title }}</p>

        @if($icon_item->filename)
        <p class="img"><img src="{{ Storage::url('icon_item/' . $icon_item->filename) }}" alt=""></p>
    @endif
    </label>
    @endforeach
</section>
@endif

<!-- 料金設定タブ（個別料金制のみ表示） -->
@if(($setting->pricing_type ?? 'unified') === 'individual')
<input id="tab-fee" type="radio" name="tabs" class="tab-switch">
<label class="tab-label" for="tab-fee">個別料金設定</label>

<section id="feeArea" class="editSection tab-content">
    <h3>料金設定</h3>
    <div class="fee-settings">

        <p class="mb-4">
            料金設定はコースごとに行います。<br>
            「一律料金を使用」をONにすると、全ての時間帯で一律の料金が適用されます。<br>
            料金を入力すると即時で確定されます。
        </p>
        @foreach($courses as $course)
            @php
                $feeSettings = $courseFeeSettings[$course->id] ?? [];
                $fee_60 = $feeSettings['fee_60'] ?? '';
                $fee_90 = $feeSettings['fee_90'] ?? '';
                $fee_120 = $feeSettings['fee_120'] ?? '';
                $fee_150 = $feeSettings['fee_150'] ?? '';
                $fee_180 = $feeSettings['fee_180'] ?? '';
                $fee_extension = $feeSettings['fee_extension'] ?? '';
                $uniform_rate = $feeSettings['uniform_rate'] ?? '';
                $use_uniform_rate = $feeSettings['use_uniform_rate'] ?? false;
            @endphp
            <div class="course-fee-item" data-course-id="{{ $course->id }}">
                <h4 class="course-name">{{ $course->title }}</h4>

                <!-- 一律料金設定 -->
                <div class="uniform-rate-section mb-4 p-3 bg-gray-50 rounded">
                    <label class="flex items-center mb-2">
                        <input type="checkbox" class="uniform-rate-checkbox"
                               data-course-id="{{ $course->id }}"
                               {{ $use_uniform_rate ? 'checked' : '' }}>
                        <span class="ml-2">一律料金を使用</span>
                    </label>
                    <div class="flex items-center">
                        <input type="number" class="uniform-rate-input w-24 mr-2"
                               placeholder="料金" value="{{ $uniform_rate }}"
                               data-course-id="{{ $course->id }}">
                        <span>円（30分単位）</span>
                    </div>
                </div>

                <!-- 料金テーブル -->
                <div class="fee-table">
                    <table class="w-full border-collapse border border-gray-300">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border border-gray-300 p-2 text-left">コース名</th>
                                <th class="border border-gray-300 p-2 text-center">60分</th>
                                <th class="border border-gray-300 p-2 text-center">90分</th>
                                <th class="border border-gray-300 p-2 text-center">120分</th>
                                <th class="border border-gray-300 p-2 text-center">150分</th>
                                <th class="border border-gray-300 p-2 text-center">180分</th>
                                <th class="border border-gray-300 p-2 text-center">延長30分</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="border border-gray-300 p-2 font-medium">{{ $course->title }}</td>
                                <td class="border border-gray-300 p-2">
                                    <input type="number" class="fee-input w-20 text-center"
                                           placeholder="料金" value="{{ $fee_60 }}"
                                           data-course-id="{{ $course->id }}" data-fee-type="fee_60">
                                </td>
                                <td class="border border-gray-300 p-2">
                                    <input type="number" class="fee-input w-20 text-center"
                                           placeholder="料金" value="{{ $fee_90 }}"
                                           data-course-id="{{ $course->id }}" data-fee-type="fee_90">
                                </td>
                                <td class="border border-gray-300 p-2">
                                    <input type="number" class="fee-input w-20 text-center"
                                           placeholder="料金" value="{{ $fee_120 }}"
                                           data-course-id="{{ $course->id }}" data-fee-type="fee_120">
                                </td>
                                <td class="border border-gray-300 p-2">
                                    <input type="number" class="fee-input w-20 text-center"
                                           placeholder="料金" value="{{ $fee_150 }}"
                                           data-course-id="{{ $course->id }}" data-fee-type="fee_150">
                                </td>
                                <td class="border border-gray-300 p-2">
                                    <input type="number" class="fee-input w-20 text-center"
                                           placeholder="料金" value="{{ $fee_180 }}"
                                           data-course-id="{{ $course->id }}" data-fee-type="fee_180">
                                </td>
                                <td class="border border-gray-300 p-2">
                                    <input type="number" class="fee-input w-20 text-center"
                                           placeholder="料金" value="{{ $fee_extension }}"
                                           data-course-id="{{ $course->id }}" data-fee-type="fee_extension">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
    </div>
</section>
@endif {{-- pricing_type === 'individual' --}}

<!-- クーポン設定タブ -->
 {{--
<input id="tab-coupon" type="radio" name="tabs" class="tab-switch">
<label class="tab-label" for="tab-coupon">クーポン設定</label>

<section id="couponArea" class="editSection tab-content">
    <h3>クーポン設定</h3>
    <p class="form-hint">※チェックをON/OFFで即時反映されます</p>

    @if($coupons->isNotEmpty())
        <div class="coupon-settings">
            @foreach($coupons as $coupon)
            <label class="coupon-item">
                <input type="checkbox" name="coupons[]" value="{{ $coupon->id }}"
                    {{ in_array($coupon->id, $selectedCoupons) ? 'checked' : '' }}
                    class="coupon-checkbox" data-coupon-id="{{ $coupon->id }}">
                <div class="coupon-info">
                    <div class="coupon-title">{{ $coupon->title }}</div>
                    @if($coupon->code)
                        <div class="coupon-code">コード: {{ $coupon->code }}</div>
                    @endif
                    @if($coupon->description)
                        <div class="coupon-description">{{ $coupon->description }}</div>
                    @endif
                    @if($coupon->expires_at)
                        <div class="coupon-expires">期限: {{ $coupon->expires_at->format('Y/m/d') }}</div>
                    @endif
                </div>
            </label>
            @endforeach
        </div>
    @else
        <p>利用可能なクーポンがありません。</p>
    @endif
</section>
--}}

<input id="tab-memo" type="radio" name="tabs" class="tab-switch">
<label class="tab-label" for="tab-memo">コメント元データ</label>

<section id="memoArea" class="editSection tab-content">
    <h3>コメント元データ</h3>
    <form action="{{ route('admin.items.update', $item) }}" method="POST">
        @csrf
        @method('patch')
        <input type="hidden" name="item_name" value="{{ $item->item_name }}">
        <table>
            <tr>
                <th>メモ</th>
                <td>
                    <textarea id="item_memo" name="item_memo" style="width:100%;height:400px;">{{ old('item_memo', $item->item_memo) }}</textarea>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <button type="submit" class="admin-btn admin-btn-submit">
                        <i class="fas fa-save"></i>更新
                    </button>
                </td>
            </tr>
        </table>
    </form>
</section>
    <div class="delete-section">
        <form action="{{ route('admin.items.destroy', $item) }}" method="POST" style="display:inline;">
            @csrf
            @method('DELETE')
            <button type="submit" class="delete-cast-text-btn" onclick="return confirm('本当に{{ $item->item_name }}を削除しますか？')">
                <i class="fas fa-trash"></i> {{ $item->item_name }}を削除する
            </button>
        </form>
    </div>
</div>

<script>
    $(document).ready(function() {
        // オプションのチェックボックスに変更があった場合にAjaxで反映
        $('.option-checkbox').on('change', function() {
            var optionId = $(this).data('option-id');
            var isChecked = $(this).is(':checked');
            var itemId = {{ $item->id }};

            console.log(optionId);
            $.ajax({
                url: '{{ route("admin.items.updateOption", ["id" => $item->id]) }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    option_id: optionId,
                    checked: isChecked
                },
                success: function(response) {
                    if (response.success) {
                        console.log('オプションが更新されました');
                    }
                },
                error: function(xhr) {
                    console.log('エラーが発生しました⑤');
                }
            });
        });
    });

    $(document).ready(function() {
        // オプションのチェックボックスに変更があった場合にAjaxで反映
        $('.option-pay-checkbox').on('change', function() {
            var optionPayId = $(this).data('option-pay-id');
            var isCheckedPay = $(this).is(':checked');
            var itemId = {{ $item->id }};

            $.ajax({
                url: '{{ route("admin.items.updateOptionPay", ["id" => $item->id]) }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    option_pay_id: optionPayId,
                    pay_checked: isCheckedPay
                },
                success: function(response) {
                    if (response.success) {
                        console.log('オプションPAYが更新されました');
                    }
                },
                error: function(xhr) {
                    console.log('エラーが発生しました⑥');
                }
            });
        });
    });


    $(document).ready(function() {
        // キャストアイコンのチェックボックスに変更があった場合にAjaxで反映
        $('.iconItem-checkbox').on('change', function() {
            var iconItem_id = $(this).data('iconitem-id');
            var isCheckedIconItem = $(this).is(':checked');
            var itemId = {{ $item->id }};

            $.ajax({
                url: '{{ route("admin.items.updateIconItem", ["id" => $item->id]) }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    iconItem_id: iconItem_id,
                    iconItem_checked: isCheckedIconItem
                },
                success: function(response) {
                    if (response.success) {
                        console.log('キャストアイコンが更新されました');
                    }
                },
                error: function(xhr) {
                    console.log('エラーが発生しました⑦');
                }
            });
        });
    });


    // $(document).ready(function() {
    //     // プロフィールのチェックボックスに変更があった場合にAjaxで反映
    //     $('.profile-text').on('change', function() {
    //         var profile_id = $(this).data('profile-id');
    //         var isCheckedProfile = $(this).is(':checked');
    //         var itemId = {{ $item->id }};

    //         $.ajax({
    //             url: '{{ route("admin.items.updateProfile", ["id" => $item->id]) }}',
    //             method: 'POST',
    //             data: {
    //                 _token: '{{ csrf_token() }}',
    //                 profile_id: profile_id,
    //                 profile_checked: isCheckedProfile
    //             },
    //             success: function(response) {
    //                 if (response.success) {
    //                     console.log('プロフィールが更新されました');
    //                 }
    //             },
    //             error: function(xhr) {
    //                 console.log('エラーが発生しました');
    //             }
    //         });
    //     });
    // });

    $(document).on('change', '.course-status-radio', function() {
        var courseId = $(this).closest('.course-item').data('course-id');
        var status = $(this).val();
        updateCourseStatus({{ $item->id }}, courseId, status);
    });

    // コース設定を更新する関数
    function updateCourseStatus(itemId, courseId, status) {
        console.log('updateCourseStatus called:', {itemId, courseId, status});
        $.ajax({
            url: '{{ route("admin.items.updateCourse", ["id" => $item->id]) }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                course_id: courseId,
                status: status
            },
            dataType: 'text',  // まずテキストとして受け取る
            success: function(responseText) {
                // BOMを除去してからJSONパース
                const cleanedText = responseText.replace(/^\uFEFF+/, '').trim();
                const response = JSON.parse(cleanedText);

                if (response.success) {
                    console.log('コース設定が更新されました');

                    // ラジオの選択状態を同期
                    const courseItem = document.querySelector(`.course-item[data-course-id="${courseId}"]`);
                    if (courseItem) {
                        courseItem.querySelectorAll('.course-status input[type=radio]').forEach(r => {
                            r.checked = (r.value === status);
                        });
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('エラーが発生しました⑧');
                console.error('Status:', status);
                console.error('Error:', error);
                console.error('Response:', xhr.responseText);
                console.error('Status Code:', xhr.status);
            }
        });
    }

    // 料金設定を更新する関数
    function updateCourseFee(courseId, feeData) {
        console.log('料金設定更新開始:', courseId, feeData);

        $.ajax({
            url: '{{ route("admin.items.updateCourseFee", ["id" => $item->id]) }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                course_id: courseId,
                ...feeData
            },
            success: function(response) {
                console.log('料金設定更新成功:', response);
                if (response.success) {
                    console.log('料金設定が更新されました');
                }
            },
            error: function(xhr, status, error) {
                console.log('料金設定更新エラー:', xhr.responseText);
                console.log('ステータス:', status);
                console.log('エラー:', error);
            }
        });
    }

    // 一律料金チェックボックスのイベントリスナー
    document.addEventListener('DOMContentLoaded', function() {
        const uniformRateCheckboxes = document.querySelectorAll('.uniform-rate-checkbox');

        uniformRateCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const courseId = this.getAttribute('data-course-id');
                const uniformRateInput = document.querySelector(`.uniform-rate-input[data-course-id="${courseId}"]`);
                const feeInputs = document.querySelectorAll(`.fee-input[data-course-id="${courseId}"]`);

                if (this.checked && uniformRateInput.value) {
                    const uniformRate = parseInt(uniformRateInput.value);

                    // 30分単位で料金を自動計算
                    feeInputs.forEach(input => {
                        const feeType = input.getAttribute('data-fee-type');
                        let calculatedFee = 0;

                        switch(feeType) {
                            case 'fee_60':
                                calculatedFee = uniformRate * 2; // 60分 = 30分 × 2
                                break;
                            case 'fee_90':
                                calculatedFee = uniformRate * 3; // 90分 = 30分 × 3
                                break;
                            case 'fee_120':
                                calculatedFee = uniformRate * 4; // 120分 = 30分 × 4
                                break;
                            case 'fee_150':
                                calculatedFee = uniformRate * 5; // 150分 = 30分 × 5
                                break;
                            case 'fee_180':
                                calculatedFee = uniformRate * 6; // 180分 = 30分 × 6
                                break;
                            case 'fee_extension':
                                calculatedFee = uniformRate; // 延長30分 = 30分 × 1
                                break;
                        }

                        input.value = calculatedFee;
                    });

                    // 料金設定を更新
                    const feeData = {
                        fee_60: uniformRate * 2,
                        fee_90: uniformRate * 3,
                        fee_120: uniformRate * 4,
                        fee_150: uniformRate * 5,
                        fee_180: uniformRate * 6,
                        fee_extension: uniformRate,
                        uniform_rate: uniformRate,
                        use_uniform_rate: 'true'
                    };

                    console.log('一律料金計算結果:', feeData);
                    updateCourseFee(courseId, feeData);
                } else {
                    // チェックを外した場合は料金をクリア
                    feeInputs.forEach(input => {
                        input.value = '';
                    });

                    // 料金設定を更新
                    const feeData = {
                        fee_60: '',
                        fee_90: '',
                        fee_120: '',
                        fee_150: '',
                        fee_180: '',
                        fee_extension: '',
                        uniform_rate: uniformRateInput.value,
                        use_uniform_rate: 'false'
                    };

                    console.log('料金クリア結果:', feeData);
                    updateCourseFee(courseId, feeData);
                }
            });
        });

        // 一律料金入力フィールドのイベントリスナー
        const uniformRateInputs = document.querySelectorAll('.uniform-rate-input');

        uniformRateInputs.forEach(input => {
            input.addEventListener('input', function() {
                const courseId = this.getAttribute('data-course-id');
                const checkbox = document.querySelector(`.uniform-rate-checkbox[data-course-id="${courseId}"]`);

                if (checkbox.checked && this.value) {
                    const uniformRate = parseInt(this.value);

                    // 料金を再計算
                    const feeInputs = document.querySelectorAll(`.fee-input[data-course-id="${courseId}"]`);
                    feeInputs.forEach(feeInput => {
                        const feeType = feeInput.getAttribute('data-fee-type');
                        let calculatedFee = 0;

                        switch(feeType) {
                            case 'fee_60':
                                calculatedFee = uniformRate * 2;
                                break;
                            case 'fee_90':
                                calculatedFee = uniformRate * 3;
                                break;
                            case 'fee_120':
                                calculatedFee = uniformRate * 4;
                                break;
                            case 'fee_150':
                                calculatedFee = uniformRate * 5;
                                break;
                            case 'fee_180':
                                calculatedFee = uniformRate * 6;
                                break;
                            case 'fee_extension':
                                calculatedFee = uniformRate;
                                break;
                        }

                        feeInput.value = calculatedFee;
                    });

                    // 料金設定を更新
                    const feeData = {
                        fee_60: uniformRate * 2,
                        fee_90: uniformRate * 3,
                        fee_120: uniformRate * 4,
                        fee_150: uniformRate * 5,
                        fee_180: uniformRate * 6,
                        fee_extension: uniformRate,
                        uniform_rate: uniformRate,
                        use_uniform_rate: 'true'
                    };

                    console.log('一律料金入力結果:', feeData);
                    updateCourseFee(courseId, feeData);
                }
            });
        });

        // 個別料金入力フィールドのイベントリスナー
        const feeInputs = document.querySelectorAll('.fee-input');

        feeInputs.forEach(input => {
            input.addEventListener('blur', function() {
                const courseId = this.getAttribute('data-course-id');
                const feeType = this.getAttribute('data-fee-type');
                const value = this.value || '';

                // 料金設定を更新
                const feeData = {};
                feeData[feeType] = value;

                // 他の料金フィールドの値を取得
                const otherInputs = document.querySelectorAll(`.fee-input[data-course-id="${courseId}"]`);
                otherInputs.forEach(otherInput => {
                    if (otherInput !== this) {
                        const otherFeeType = otherInput.getAttribute('data-fee-type');
                        feeData[otherFeeType] = otherInput.value || '';
                    }
                });

                // 一律料金の値も含める
                const uniformRateInput = document.querySelector(`.uniform-rate-input[data-course-id="${courseId}"]`);
                const checkbox = document.querySelector(`.uniform-rate-checkbox[data-course-id="${courseId}"]`);

                feeData.uniform_rate = uniformRateInput.value || '';
                feeData.use_uniform_rate = checkbox.checked ? 'true' : 'false';

                console.log('個別料金入力結果:', feeData);
                updateCourseFee(courseId, feeData);
            });
        });
    });

    // クーポン設定の即時反映
    document.addEventListener('DOMContentLoaded', function() {
        const couponCheckboxes = document.querySelectorAll('.coupon-checkbox');

        couponCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const couponId = this.dataset.couponId;
                const isChecked = this.checked;

                // 選択されたクーポンのIDを取得
                const selectedCoupons = Array.from(document.querySelectorAll('.coupon-checkbox:checked'))
                    .map(cb => cb.value);

                // サーバーに送信
                fetch(`/kanrigamen/items/{{ $item->id }}/update-coupons`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        coupons: selectedCoupons
                    })
                })
                .then(async response => {
                    if (!response.ok) {
                        throw new Error('エラーが発生しました: クーポン設定の更新に失敗しました');
                    }
                    const contentType = response.headers.get('content-type');
                    if (contentType && contentType.includes('application/json')) {
                        const text = await response.text();
                        const cleanedText = text.replace(/^\uFEFF+/, '').trim(); // BOMを除去
                        try {
                            return JSON.parse(cleanedText);
                        } catch (e) {
                            console.error('JSON parse error:', e);
                            return { success: true, message: 'クーポン設定が更新されました' };
                        }
                    }
                    const text = await response.text();
                    const cleanedText = text.replace(/^\uFEFF+/, '').trim(); // BOMを除去
                    try {
                        return JSON.parse(cleanedText);
                    } catch {
                        return { success: true, message: 'クーポン設定が更新されました' };
                    }
                })
                .then(data => {
                    if (data && data.success) {
                        console.log('クーポン設定が更新されました');
                    } else {
                        console.error('クーポン設定の更新に失敗しました');
                        // チェックボックスの状態を元に戻す
                        this.checked = !isChecked;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert(error.message || 'エラーが発生しました');
                    // チェックボックスの状態を元に戻す
                    this.checked = !isChecked;
                });
            });
        });
    });

</script>

<!-- TinyMCE -->
<script src="{{ asset('js/tinymce/tinymce.min.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded, checking for textareas...');
        console.log('item_comment exists:', document.getElementById('item_comment') !== null);
        console.log('item_comment2 exists:', document.getElementById('item_comment2') !== null);
        console.log('item_comment_faq exists:', document.getElementById('item_comment_faq') !== null);
        console.log('item_p_comment exists:', document.getElementById('item_p_comment') !== null);
        console.log('document.compatMode:', document.compatMode);
        console.log('document.doctype:', document.doctype);

        if (typeof tinymce !== 'undefined') {
            console.log('TinyMCE loaded successfully');

            // document.compatModeがstandards modeでない場合の警告
            if (document.compatMode !== 'CSS1Compat') {
                console.error('Document is not in standards mode! Current mode: ' + document.compatMode);
                console.error('This will prevent TinyMCE from initializing.');
                console.error('Please ensure the HTML document starts with <!DOCTYPE html>');
            }

            // タブがアクティブになったときにTinyMCEを初期化
            const infoTab = document.getElementById('tab-info');
            if (infoTab) {
                infoTab.addEventListener('change', function() {
                    if (this.checked && tinymce.get('item_comment') === null) {
                        setTimeout(function() {
                            // document.compatModeを確認
                            if (document.compatMode !== 'CSS1Compat') {
                                console.warn('Document is not in standards mode. Current mode: ' + document.compatMode);
                            }

                            // document.compatModeを確認してから初期化
                            if (document.compatMode !== 'CSS1Compat') {
                                console.error('Document is not in standards mode! Current mode: ' + document.compatMode);
                                console.error('TinyMCE requires standards mode. Please check the HTML document.');
                                alert('エラー: ドキュメントがstandards modeではありません。TinyMCEを初期化できません。');
                                return;
                            }

                            tinymce.init({
                                selector: '#item_comment, #item_comment2, #item_comment_nominate, #item_comment_faq, #item_p_comment',
                                height: 350,
                                menubar: false,
                                iframe: true,
                                doctype: '<!DOCTYPE html>',
                                schema: 'html5',
                                forced_root_block: 'p',
                                valid_elements: '*[*]',
                                plugins: [
                                    'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                                    'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                                    'insertdatetime', 'media', 'table', 'help', 'wordcount'
                                ],
                                toolbar: 'undo redo | blocks fontsize | ' +
                                    'bold italic underline forecolor backcolor | alignleft aligncenter ' +
                                    'alignright alignjustify | bullist numlist outdent indent | ' +
                                    'removeformat | code | help',
                                font_size_formats: '10px 12px 14px 16px 18px 24px 36px 48px',
                                content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }',
                                skin_url: '{{ asset("js/tinymce/skins/ui/" . ($siteSettings?->tinymce_skin ?? "oxide")) }}',
                                content_css: '{{ asset("js/tinymce/skins/content/" . (($siteSettings?->tinymce_skin ?? "oxide") === "oxide-dark" ? "dark" : "default") . "/content.min.css") }}',
                                branding: false,
                                setup: function(editor) {
                                    editor.on('init', function() {
                                        console.log('TinyMCE initialized for: ' + editor.id);
                                    });
                                    editor.on('error', function(e) {
                                        console.error('TinyMCE error:', e);
                                    });
                                }
                            });
                        }, 100);
                    }
                });

                // 初期表示時（最初からキャスト情報タブがアクティブ）に初期化
                if (infoTab.checked) {
                    setTimeout(function() {
                        // document.compatModeを確認してから初期化
                        if (document.compatMode !== 'CSS1Compat') {
                            console.error('Document is not in standards mode! Current mode: ' + document.compatMode);
                            console.error('TinyMCE requires standards mode. Please check the HTML document.');
                            alert('エラー: ドキュメントがstandards modeではありません。TinyMCEを初期化できません。');
                            return;
                        }

                        tinymce.init({
                            selector: '#item_comment, #item_comment2, #item_comment_nominate, #item_comment_faq, #item_p_comment',
                            height: 350,
                            menubar: false,
                            iframe: true,
                            doctype: '<!DOCTYPE html>',
                            schema: 'html5',
                            forced_root_block: 'p',
                            valid_elements: '*[*]',
                            plugins: [
                                'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                                'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                                'insertdatetime', 'media', 'table', 'help', 'wordcount'
                            ],
                            toolbar: 'undo redo | blocks fontsize | ' +
                                'bold italic underline forecolor backcolor | alignleft aligncenter ' +
                                'alignright alignjustify | bullist numlist outdent indent | ' +
                                'removeformat | code | help',
                            font_size_formats: '10px 12px 14px 16px 18px 24px 36px 48px',
                            content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }',
                            skin_url: '{{ asset("js/tinymce/skins/ui/" . ($siteSettings?->tinymce_skin ?? "oxide")) }}',
                            content_css: '{{ asset("js/tinymce/skins/content/" . (($siteSettings?->tinymce_skin ?? "oxide") === "oxide-dark" ? "dark" : "default") . "/content.min.css") }}',
                            branding: false,
                            setup: function(editor) {
                                editor.on('init', function() {
                                    console.log('TinyMCE initialized for: ' + editor.id);
                                });
                                editor.on('error', function(e) {
                                    console.error('TinyMCE error:', e);
                                });
                            }
                        });
                    }, 500);
                }
            }

            // コメント元データタブ（tab-memo）の TinyMCE 初期化
            var memoTabConfig = {
                selector: '#item_memo',
                height: 400,
                menubar: false,
                iframe: true,
                doctype: '<!DOCTYPE html>',
                schema: 'html5',
                forced_root_block: 'p',
                valid_elements: '*[*]',
                plugins: [
                    'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                    'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                    'insertdatetime', 'media', 'table', 'help', 'wordcount'
                ],
                toolbar: 'undo redo | blocks fontsize | ' +
                    'bold italic underline forecolor backcolor | alignleft aligncenter ' +
                    'alignright alignjustify | bullist numlist outdent indent | ' +
                    'removeformat | code | help',
                font_size_formats: '10px 12px 14px 16px 18px 24px 36px 48px',
                content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }',
                skin_url: '{{ asset("js/tinymce/skins/ui/" . ($siteSettings?->tinymce_skin ?? "oxide")) }}',
                content_css: '{{ asset("js/tinymce/skins/content/" . (($siteSettings?->tinymce_skin ?? "oxide") === "oxide-dark" ? "dark" : "default") . "/content.min.css") }}',
                branding: false,
                setup: function(editor) {
                    editor.on('init', function() {
                        console.log('TinyMCE initialized for: ' + editor.id);
                    });
                    editor.on('error', function(e) {
                        console.error('TinyMCE error:', e);
                    });
                }
            };

            var memoTab = document.getElementById('tab-memo');
            if (memoTab) {
                memoTab.addEventListener('change', function() {
                    if (this.checked && tinymce.get('item_memo') === null) {
                        setTimeout(function() {
                            tinymce.init(memoTabConfig);
                        }, 100);
                    }
                });

                if (memoTab.checked) {
                    setTimeout(function() {
                        tinymce.init(memoTabConfig);
                    }, 500);
                }
            }

        } else {
            console.error('TinyMCE not loaded!');
        }
    });
</script>

{{-- ===================================================== --}}
{{-- マイコスチューム・マイオプション（インライン管理）       --}}
{{-- ===================================================== --}}

{{-- マイコスチューム --}}
{{--
<section class="ieo-section" id="MycostumeSection">
    <div class="ieo-section-header">
        <h3 class="ieo-section-title">マイコスチューム</h3>
        <button type="button" class="ieo-add-btn" onclick="openCostumeModal()">＋ 追加</button>
    </div>
    <table class="ieo-table">
        <thead>
            <tr>
                <th style="width:28px"></th>
                <th style="width:48px">画像</th>
                <th>名前</th>
                <th>カテゴリ</th>
                <th>料金</th>
                <th>有効</th>
                <th style="width:150px">操作</th>
            </tr>
        </thead>
        <tbody id="costume-sortable">
            @foreach($mycostumes as $costume)
            <tr data-id="{{ $costume->id }}">
                <td class="ieo-sort-handle">⠿</td>
                <td>
                    @if($costume->filename)
                    <img src="{{ Storage::url('mycostume/' . $costume->filename) }}" class="ieo-thumb" alt="">
                    @endif
                </td>
                <td>{{ $costume->name }}</td>
                <td>{{ $costume->category->name ?? '-' }}</td>
                <td>¥{{ number_format($costume->price) }}</td>
                <td>
                    <button type="button"
                            class="ieo-toggle {{ $costume->active ? 'is-active' : '' }}"
                            data-url="{{ route('admin.items.mycostumes.toggle', [$item->id, $costume->id]) }}">
                        {{ $costume->active ? '有効' : '無効' }}
                    </button>
                </td>
                <td>
                    <button type="button" class="ieo-edit-btn"
                            onclick="openCostumeEditModal({{ json_encode(['id'=>$costume->id,'mycostume_cate_id'=>$costume->mycostume_cate_id,'name'=>$costume->name,'body'=>$costume->body,'price'=>$costume->price,'active'=>$costume->active,'image_url'=>$costume->filename ? Storage::url('mycostume/'.$costume->filename) : '']) }})">編集</button>
                    <button type="button" class="ieo-del-btn"
                            data-url="{{ route('admin.items.mycostumes.destroy', [$item->id, $costume->id]) }}">削除</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</section>
--}}

{{-- マイコスチューム モーダル --}}
{{--
<div id="costumeModal" class="ieo-modal" style="display:none">
    <div class="ieo-modal-inner">
        <div class="ieo-modal-head">
            <h4 id="costumeModalTitle" class="ieo-modal-title">マイコスチューム追加</h4>
            <button type="button" class="ieo-modal-close" onclick="closeCostumeModal()">✕</button>
        </div>
        <form id="costumeForm" enctype="multipart/form-data">
            @csrf
            <input type="hidden" id="costumeActionUrl">
            <div class="ieo-form-row">
                <label class="ieo-label">カテゴリ <span class="ieo-req">*</span></label>
                <select name="mycostume_cate_id" class="ieo-input" required>
                    <option value="">選択してください</option>
                    @foreach($mycostumeCates as $cate)
                    <option value="{{ $cate->id }}">{{ $cate->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="ieo-form-row">
                <label class="ieo-label">名前 <span class="ieo-req">*</span></label>
                <input type="text" name="name" class="ieo-input" required>
            </div>
            <div class="ieo-form-row">
                <label class="ieo-label">説明</label>
                <textarea name="body" class="ieo-input" rows="2"></textarea>
            </div>
            <div class="ieo-form-row">
                <label class="ieo-label">料金</label>
                <input type="number" name="price" class="ieo-input" value="0" min="0">
            </div>
            <div class="ieo-form-row">
                <label class="ieo-label">有効</label>
                <input type="checkbox" name="active" value="1" id="costumeActive" checked>
                <label for="costumeActive" style="margin-left:4px;font-size:12px;">有効にする</label>
            </div>
            <div class="ieo-form-row">
                <label class="ieo-label">画像 <span class="ieo-req" id="costumeImgReq">*</span></label>
                <img id="costumeCurrentImg" src="" style="display:none;width:60px;height:60px;object-fit:cover;border-radius:4px;margin-bottom:6px;">
                <input type="file" name="image" class="ieo-input" id="costumeImageInput" accept="image/*">
            </div>
            <div id="costumeError" class="ieo-error" style="display:none"></div>
            <div class="ieo-modal-footer">
                <button type="button" class="ieo-cancel-btn" onclick="closeCostumeModal()">キャンセル</button>
                <button type="submit" class="ieo-save-btn" id="costumeSaveBtn">保存</button>
            </div>
        </form>
    </div>
</div>
--}}
{{-- マイオプション --}}
{{--
<section class="ieo-section" id="MyoptionSection">
    <div class="ieo-section-header">
        <h3 class="ieo-section-title">マイオプション</h3>
        <button type="button" class="ieo-add-btn" onclick="openOptionModal()">＋ 追加</button>
    </div>
    <table class="ieo-table">
        <thead>
            <tr>
                <th style="width:28px"></th>
                <th style="width:48px">画像</th>
                <th>名前</th>
                <th>カテゴリ</th>
                <th>料金</th>
                <th>有効</th>
                <th style="width:150px">操作</th>
            </tr>
        </thead>
        <tbody id="option-sortable">
            @foreach($myoptions as $option)
            <tr data-id="{{ $option->id }}">
                <td class="ieo-sort-handle">⠿</td>
                <td>
                    @if($option->filename)
                    <img src="{{ Storage::url('myoption/' . $option->filename) }}" class="ieo-thumb" alt="">
                    @endif
                </td>
                <td>{{ $option->name }}</td>
                <td>{{ $option->category->name ?? '-' }}</td>
                <td>¥{{ number_format($option->price) }}</td>
                <td>
                    <button type="button"
                            class="ieo-toggle {{ $option->active ? 'is-active' : '' }}"
                            data-url="{{ route('admin.items.myoptions.toggle', [$item->id, $option->id]) }}">
                        {{ $option->active ? '有効' : '無効' }}
                    </button>
                </td>
                <td>
                    <button type="button" class="ieo-edit-btn"
                            onclick="openOptionEditModal({{ json_encode(['id'=>$option->id,'myoption_cate_id'=>$option->myoption_cate_id,'name'=>$option->name,'body'=>$option->body,'price'=>$option->price,'active'=>$option->active,'image_url'=>$option->filename ? Storage::url('myoption/'.$option->filename) : '']) }})">編集</button>
                    <button type="button" class="ieo-del-btn"
                            data-url="{{ route('admin.items.myoptions.destroy', [$item->id, $option->id]) }}">削除</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</section>
--}}
{{-- マイオプション モーダル --}}
{{--
<div id="optionModal" class="ieo-modal" style="display:none">
    <div class="ieo-modal-inner">
        <div class="ieo-modal-head">
            <h4 id="optionModalTitle" class="ieo-modal-title">マイオプション追加</h4>
            <button type="button" class="ieo-modal-close" onclick="closeOptionModal()">✕</button>
        </div>
        <form id="optionForm" enctype="multipart/form-data">
            @csrf
            <input type="hidden" id="optionActionUrl">
            <div class="ieo-form-row">
                <label class="ieo-label">カテゴリ <span class="ieo-req">*</span></label>
                <select name="myoption_cate_id" class="ieo-input" required>
                    <option value="">選択してください</option>
                    @foreach($myoptionCates as $cate)
                    <option value="{{ $cate->id }}">{{ $cate->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="ieo-form-row">
                <label class="ieo-label">名前 <span class="ieo-req">*</span></label>
                <input type="text" name="name" class="ieo-input" required>
            </div>
            <div class="ieo-form-row">
                <label class="ieo-label">説明</label>
                <textarea name="body" class="ieo-input" rows="2"></textarea>
            </div>
            <div class="ieo-form-row">
                <label class="ieo-label">料金</label>
                <input type="number" name="price" class="ieo-input" value="0" min="0">
            </div>
            <div class="ieo-form-row">
                <label class="ieo-label">有効</label>
                <input type="checkbox" name="active" value="1" id="optionActive" checked>
                <label for="optionActive" style="margin-left:4px;font-size:12px;">有効にする</label>
            </div>
            <div class="ieo-form-row">
                <label class="ieo-label">画像 <span class="ieo-req" id="optionImgReq">*</span></label>
                <img id="optionCurrentImg" src="" style="display:none;width:60px;height:60px;object-fit:cover;border-radius:4px;margin-bottom:6px;">
                <input type="file" name="image" class="ieo-input" id="optionImageInput" accept="image/*">
            </div>
            <div id="optionError" class="ieo-error" style="display:none"></div>
            <div class="ieo-modal-footer">
                <button type="button" class="ieo-cancel-btn" onclick="closeOptionModal()">キャンセル</button>
                <button type="submit" class="ieo-save-btn" id="optionSaveBtn">保存</button>
            </div>
        </form>
    </div>
</div>
--}}
<style>
/* ---- Inline Edit & Order (ieo) styles ---- */
.ieo-section {
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 14px 16px;
    margin-bottom: 18px;
    background: #fff;
}
.ieo-section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}
.ieo-section-title {
    font-size: 14px;
    font-weight: bold;
    color: #333;
    border-bottom: 2px solid #b8d7ff;
    padding-bottom: 4px;
}
.ieo-add-btn {
    font-size: 12px;
    padding: 4px 12px;
    background: #3b82f6;
    color: #fff;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}
.ieo-add-btn:hover { background: #2563eb; }
.ieo-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 12px;
}
.ieo-table th,
.ieo-table td {
    padding: 5px 8px;
    border: 1px solid #e5e7eb;
    vertical-align: middle;
}
.ieo-table th {
    background: #f9fafb;
    font-weight: 600;
    color: #555;
}
.ieo-sort-handle {
    cursor: grab;
    color: #bbb;
    text-align: center;
    font-size: 14px;
    user-select: none;
}
.ieo-thumb {
    width: 38px;
    height: 38px;
    object-fit: cover;
    border-radius: 4px;
    display: block;
}
.ieo-toggle {
    font-size: 11px;
    padding: 2px 8px;
    border-radius: 3px;
    border: 1px solid #ccc;
    cursor: pointer;
    background: #fff;
    color: #888;
}
.ieo-toggle.is-active {
    background: #10b981;
    color: #fff;
    border-color: #10b981;
}
.ieo-edit-btn {
    font-size: 11px;
    padding: 2px 8px;
    border-radius: 3px;
    cursor: pointer;
    border: 1px solid #3b82f6;
    color: #3b82f6;
    background: #fff;
    margin-right: 3px;
}
.ieo-del-btn {
    font-size: 11px;
    padding: 2px 8px;
    border-radius: 3px;
    cursor: pointer;
    border: 1px solid #dc2626;
    color: #dc2626;
    background: #fff;
}
/* Modal */
.ieo-modal {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.5);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
}
.ieo-modal-inner {
    background: #fff;
    border-radius: 8px;
    padding: 20px 24px;
    width: 460px;
    max-height: 90vh;
    overflow-y: auto;
}
.ieo-modal-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 14px;
}
.ieo-modal-title {
    font-size: 14px;
    font-weight: bold;
    color: #333;
}
.ieo-modal-close {
    background: none;
    border: none;
    font-size: 16px;
    cursor: pointer;
    color: #888;
    padding: 0;
}
.ieo-form-row {
    margin-bottom: 10px;
    display: flex;
    flex-direction: column;
    gap: 3px;
}
.ieo-label {
    font-size: 12px;
    font-weight: 600;
    color: #555;
}
.ieo-req { color: #dc2626; }
.ieo-input {
    font-size: 12px;
    border: 1px solid #d1d5db;
    border-radius: 4px;
    padding: 5px 8px;
    width: 100%;
    box-sizing: border-box;
}
.ieo-input:focus { outline: none; border-color: #3b82f6; }
.ieo-error {
    font-size: 12px;
    color: #dc2626;
    background: #fef2f2;
    border: 1px solid #fca5a5;
    border-radius: 4px;
    padding: 6px 10px;
    margin-bottom: 8px;
}
.ieo-modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 8px;
    margin-top: 14px;
}
.ieo-cancel-btn {
    font-size: 12px;
    padding: 6px 14px;
    border: 1px solid #ccc;
    border-radius: 4px;
    cursor: pointer;
    background: #fff;
    color: #555;
}
.ieo-save-btn {
    font-size: 12px;
    padding: 6px 14px;
    background: #3b82f6;
    color: #fff;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}
.ieo-save-btn:hover { background: #2563eb; }
.sortable-ghost-row { opacity: 0.4; background: #eff6ff; }
</style>

<script>
(function() {
    var csrfToken = '{{ csrf_token() }}';

    // ---- Costume Sort ----
    var costumeSortable = document.getElementById('costume-sortable');
    if (costumeSortable && typeof Sortable !== 'undefined') {
        Sortable.create(costumeSortable, {
            handle: '.ieo-sort-handle',
            ghostClass: 'sortable-ghost-row',
            onEnd: function() {
                var order = Array.from(costumeSortable.querySelectorAll('tr')).map(function(tr, i) {
                    return { id: tr.dataset.id, position: i + 1 };
                });
                $.post('{{ route("admin.mycostumes.sort") }}', { _token: csrfToken, order: order });
            }
        });
    }

    // ---- Option Sort ----
    var optionSortable = document.getElementById('option-sortable');
    if (optionSortable && typeof Sortable !== 'undefined') {
        Sortable.create(optionSortable, {
            handle: '.ieo-sort-handle',
            ghostClass: 'sortable-ghost-row',
            onEnd: function() {
                var order = Array.from(optionSortable.querySelectorAll('tr')).map(function(tr, i) {
                    return { id: tr.dataset.id, position: i + 1 };
                });
                $.post('{{ route("admin.myoptions.sort") }}', { _token: csrfToken, order: order });
            }
        });
    }

    // ---- Delete ----
    $(document).on('click', '.ieo-del-btn', function() {
        if (!confirm('削除しますか？')) return;
        var $btn = $(this);
        var url = $btn.data('url');
        $.ajax({
            url: url,
            method: 'DELETE',
            data: { _token: csrfToken },
            success: function() { $btn.closest('tr').remove(); },
            error: function() { alert('削除に失敗しました'); }
        });
    });

    // ---- Toggle Active ----
    $(document).on('click', '.ieo-toggle', function() {
        var $btn = $(this);
        $.post($btn.data('url'), { _token: csrfToken }, function(res) {
            if (res.active) {
                $btn.addClass('is-active').text('有効');
            } else {
                $btn.removeClass('is-active').text('無効');
            }
        });
    });

    // ===================== Costume Modal =====================
    var costumeEditId = null;

    window.openCostumeModal = function() {
        costumeEditId = null;
        $('#costumeModalTitle').text('マイコスチューム追加');
        $('#costumeForm')[0].reset();
        $('#costumeCurrentImg').hide();
        $('#costumeImgReq').show();
        $('#costumeError').hide();
        $('#costumeActionUrl').val('{{ route("admin.items.mycostumes.store", $item->id) }}');
        $('#costumeModal').show();
    };

    window.openCostumeEditModal = function(data) {
        costumeEditId = data.id;
        $('#costumeModalTitle').text('マイコスチューム編集');
        var form = $('#costumeForm')[0];
        form.reset();
        $(form).find('[name=mycostume_cate_id]').val(data.mycostume_cate_id);
        $(form).find('[name=name]').val(data.name);
        $(form).find('[name=body]').val(data.body || '');
        $(form).find('[name=price]').val(data.price);
        $(form).find('#costumeActive').prop('checked', !!data.active);
        if (data.image_url) {
            $('#costumeCurrentImg').attr('src', data.image_url).show();
        }
        $('#costumeImgReq').hide();
        $('#costumeError').hide();
        $('#costumeActionUrl').val('{{ url("/kanrigamen/items/".$item->id."/mycostumes") }}/' + data.id + '/update');
        $('#costumeModal').show();
    };

    window.closeCostumeModal = function() {
        $('#costumeModal').hide();
    };

    $('#costumeForm').on('submit', function(e) {
        e.preventDefault();
        var url = $('#costumeActionUrl').val();
        var formData = new FormData(this);
        $('#costumeSaveBtn').prop('disabled', true).text('保存中…');
        $('#costumeError').hide();
        fetch(url, {
            method: 'POST',
            body: formData,
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
        }).then(function(r) { return r.json(); }).then(function(res) {
            if (res.success) {
                location.reload();
            } else {
                showCostumeErrors(res.errors || res.message || '保存に失敗しました');
            }
        }).catch(function() {
            showCostumeErrors('保存に失敗しました');
        }).finally(function() {
            $('#costumeSaveBtn').prop('disabled', false).text('保存');
        });
    });

    function showCostumeErrors(errors) {
        var msg = typeof errors === 'object' ? Object.values(errors).flat().join('<br>') : errors;
        $('#costumeError').html(msg).show();
    }

    // ===================== Option Modal =====================
    var optionEditId = null;

    window.openOptionModal = function() {
        optionEditId = null;
        $('#optionModalTitle').text('マイオプション追加');
        $('#optionForm')[0].reset();
        $('#optionCurrentImg').hide();
        $('#optionImgReq').show();
        $('#optionError').hide();
        $('#optionActionUrl').val('{{ route("admin.items.myoptions.store", $item->id) }}');
        $('#optionModal').show();
    };

    window.openOptionEditModal = function(data) {
        optionEditId = data.id;
        $('#optionModalTitle').text('マイオプション編集');
        var form = $('#optionForm')[0];
        form.reset();
        $(form).find('[name=myoption_cate_id]').val(data.myoption_cate_id);
        $(form).find('[name=name]').val(data.name);
        $(form).find('[name=body]').val(data.body || '');
        $(form).find('[name=price]').val(data.price);
        $(form).find('#optionActive').prop('checked', !!data.active);
        if (data.image_url) {
            $('#optionCurrentImg').attr('src', data.image_url).show();
        }
        $('#optionImgReq').hide();
        $('#optionError').hide();
        $('#optionActionUrl').val('{{ url("/kanrigamen/items/".$item->id."/myoptions") }}/' + data.id + '/update');
        $('#optionModal').show();
    };

    window.closeOptionModal = function() {
        $('#optionModal').hide();
    };

    $('#optionForm').on('submit', function(e) {
        e.preventDefault();
        var url = $('#optionActionUrl').val();
        var formData = new FormData(this);
        $('#optionSaveBtn').prop('disabled', true).text('保存中…');
        $('#optionError').hide();
        fetch(url, {
            method: 'POST',
            body: formData,
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
        }).then(function(r) { return r.json(); }).then(function(res) {
            if (res.success) {
                location.reload();
            } else {
                showOptionErrors(res.errors || res.message || '保存に失敗しました');
            }
        }).catch(function() {
            showOptionErrors('保存に失敗しました');
        }).finally(function() {
            $('#optionSaveBtn').prop('disabled', false).text('保存');
        });
    });

    function showOptionErrors(errors) {
        var msg = typeof errors === 'object' ? Object.values(errors).flat().join('<br>') : errors;
        $('#optionError').html(msg).show();
    }

    // モーダル外クリックで閉じる
    $('#costumeModal').on('click', function(e) { if (e.target === this) closeCostumeModal(); });
    $('#optionModal').on('click', function(e) { if (e.target === this) closeOptionModal(); });
})();
</script>

{{-- ===================================================== --}}
{{-- チャンネル情報・ポスト                                   --}}
{{-- ===================================================== --}}

<section class="mt-8" id="FanclubSection" style="display:none;">
    <h3 style="font-size:16px;font-weight:bold;margin-bottom:12px;padding-bottom:6px;border-bottom:2px solid #f3a8d6;">
        ファンクラブ
    </h3>

    @if($fanclub)
    {{-- ファンクラブ基本情報 --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:12px;margin-bottom:20px;">
        <div style="background:#fff0f7;border-radius:8px;padding:14px;text-align:center;">
            <div style="font-size:22px;font-weight:bold;color:#e91e8c;">{{ $fanclub->active_memberships_count }}</div>
            <div style="font-size:11px;color:#888;margin-top:4px;">有効会員数</div>
        </div>
        <div style="background:#f5f5f5;border-radius:8px;padding:14px;text-align:center;">
            <div style="font-size:22px;font-weight:bold;color:#555;">{{ $fanclub->memberships_count }}</div>
            <div style="font-size:11px;color:#888;margin-top:4px;">全会員数</div>
        </div>
        <div style="background:#f5f5f5;border-radius:8px;padding:14px;text-align:center;">
            <div style="font-size:22px;font-weight:bold;color:#555;">{{ $fanclubContents->count() }}</div>
            <div style="font-size:11px;color:#888;margin-top:4px;">チャンネル数（最近20件）</div>
        </div>
        <div style="background:#f5f5f5;border-radius:8px;padding:14px;text-align:center;">
            <div style="font-size:13px;font-weight:bold;color:#555;">¥{{ number_format($fanclub->price) }}</div>
            <div style="font-size:11px;color:#888;margin-top:4px;">月額</div>
        </div>
        <div style="background:#f5f5f5;border-radius:8px;padding:14px;text-align:center;">
            @if($fanclub->status === 'active')
                <div style="font-size:13px;font-weight:bold;color:#2e7d32;">公開中</div>
            @elseif($fanclub->status === 'paused')
                <div style="font-size:13px;font-weight:bold;color:#f57c00;">一時停止</div>
            @else
                <div style="font-size:13px;font-weight:bold;color:#c62828;">キャンセル</div>
            @endif
            <div style="font-size:11px;color:#888;margin-top:4px;">ステータス</div>
        </div>
        <div style="background:#f5f5f5;border-radius:8px;padding:14px;text-align:center;">
            <div style="font-size:12px;color:#555;">{{ $fanclub->created_at->format('Y/m/d') }}</div>
            <div style="font-size:11px;color:#888;margin-top:4px;">作成日</div>
        </div>
    </div>

    <div style="margin-bottom:14px;display:flex;gap:10px;flex-wrap:wrap;">
        <a href="{{ route('admin.fanclubs.show', $fanclub) }}"
           style="font-size:12px;padding:5px 14px;background:#e91e8c;color:#fff;border-radius:4px;text-decoration:none;">
            ファンクラブ詳細
        </a>
        <a href="{{ route('admin.fanclubs.edit', $fanclub) }}"
           style="font-size:12px;padding:5px 14px;background:#555;color:#fff;border-radius:4px;text-decoration:none;">
            ファンクラブ編集
        </a>
        <a href="{{ route('admin.fanclubs.memberships.index') }}"
           style="font-size:12px;padding:5px 14px;background:#eee;color:#333;border-radius:4px;text-decoration:none;">
            会員管理
        </a>
    </div>

    {{-- コンテンツ一覧 --}}
    @if($fanclubContents->isNotEmpty())
    <div style="background:#fff;border:1px solid #eee;border-radius:8px;overflow:hidden;">
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead style="background:#fce4f0;">
                <tr>
                    <th style="text-align:left;padding:8px 12px;color:#888;">ID</th>
                    <th style="text-align:left;padding:8px 12px;">タイトル</th>
                    <th style="text-align:center;padding:8px 12px;">有料</th>
                    <th style="text-align:center;padding:8px 12px;">ポスト数</th>
                    <th style="text-align:left;padding:8px 12px;">公開日時</th>
                    <th style="text-align:left;padding:8px 12px;">操作</th>
                </tr>
            </thead>
            <tbody>
                @foreach($fanclubContents as $fc)
                <tr style="border-top:1px solid #f5f5f5;">
                    <td style="padding:7px 12px;color:#aaa;">{{ $fc->id }}</td>
                    <td style="padding:7px 12px;">{{ $fc->title }}</td>
                    <td style="padding:7px 12px;text-align:center;">
                        @if($fc->is_paid)
                            <span style="font-size:11px;background:#fff3cd;color:#856404;padding:2px 6px;border-radius:10px;">有料</span>
                        @else
                            <span style="font-size:11px;color:#aaa;">無料</span>
                        @endif
                    </td>
                    <td style="padding:7px 12px;text-align:center;">{{ $fc->children_count }}</td>
                    <td style="padding:7px 12px;font-size:12px;color:#888;">
                        {{ optional($fc->published_at)->format('Y/m/d H:i') ?? '未公開' }}
                    </td>
                    <td style="padding:7px 12px;">
                        <div style="display:flex;gap:8px;">
                            <a href="{{ route('admin.fanclubs.contents.show', $fc) }}"
                               style="font-size:11px;color:#1976d2;text-decoration:underline;">詳細</a>
                            <a href="{{ route('admin.fanclubs.contents.edit', $fc) }}"
                               style="font-size:11px;color:#555;text-decoration:underline;">編集</a>
                            <a href="{{ route('admin.fanclubs.contents.children.index', $fc) }}"
                               style="font-size:11px;color:#888;text-decoration:underline;">ポスト</a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
        <p style="color:#aaa;font-size:13px;text-align:center;padding:20px;">チャンネルがまだありません</p>
    @endif

    @else
    {{-- ファンクラブ未登録 --}}
    <div style="background:#fff8f8;border:1px dashed #f3a8d6;border-radius:8px;padding:20px;text-align:center;">
        <p style="color:#888;font-size:13px;margin-bottom:12px;">このキャストのファンクラブは未登録です</p>
        <a href="{{ route('admin.fanclubs.create') }}"
           style="font-size:13px;padding:6px 18px;background:#e91e8c;color:#fff;border-radius:4px;text-decoration:none;">
            + ファンクラブを作成する
        </a>
    </div>
    @endif
</section>

</x-admin-layout>

<style>
/* オプションチェックボックスのグリッド */
.option-checkbox-grid {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 0.8rem;
    font-size: 0.775rem;
}
.option-checkbox-grid > label{
    margin:0;
    background:#f6f6f6;
}

/* ドラッグ中のアイテムのスタイル */
.sortable-ghost {
    opacity: 0.4;
    background: #f0f0f0;
}

/* ドラッグ可能なアイテムのスタイル */
.box {
    cursor: move;
}

/* コース設定のスタイル */
.course-settings {
    display: flex;
    flex-direction: column;
    gap: 6px;
    max-width: 500px;
}

.course-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 6px 10px;
    border: 1px solid #e0e0e0;
    border-radius: 6px;
    background-color: #fafafa;
    font-size: 14px;
}

.course-name {
    font-weight: 500;
    color: #333;
    flex: 1;
    margin-right: 15px;
}

.course-status {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 0.35rem;
}

/* 可能コースは form.css の .form_parts input:checked + .radio で選択背景を表示 */
.course-settings .course-status .radio {
    margin-bottom: 0;
}

/* 料金設定のスタイル */
.fee-settings {
    margin-top: 20px;
}

.course-fee-item {
    margin-bottom: 30px;
    padding: 20px;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    background-color: #fafafa;
}

.course-name {
    font-size: 14px;
    font-weight: bold;
    color: #333;
}

.uniform-rate-section {
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 6px;
}

.fee-table {
    overflow-x: auto;
}

.fee-table table {
    min-width: 600px;
}

/* クーポン設定のスタイル */
.coupon-settings {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 15px;
    margin-top: 20px;
}

.coupon-item {
    display: flex;
    align-items: flex-start;
    padding: 15px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.coupon-item:hover {
    border-color: #3b82f6;
    background-color: #f8fafc;
}

.coupon-item input[type="checkbox"] {
    margin-right: 12px;
    margin-top: 2px;
    transform: scale(1.2);
}

.coupon-info {
    flex: 1;
}

.coupon-title {
    font-weight: bold;
    font-size: 16px;
    color: #1f2937;
    margin-bottom: 5px;
}

.coupon-code {
    font-size: 14px;
    color: #6b7280;
    margin-bottom: 3px;
}

.coupon-description {
    font-size: 14px;
    color: #4b5563;
    margin-bottom: 3px;
    line-height: 1.4;
}

.coupon-expires {
    font-size: 12px;
    color: #ef4444;
    font-weight: 500;
}

.coupon-item:has(input:checked) {
    border-color: #10b981;
    background-color: #ecfdf5;
}

.coupon-item:has(input:checked) .coupon-title {
    color: #065f46;
}

.fee-input {
    border: 1px solid #ced4da;
    border-radius: 4px;
    padding: 4px 8px;
    font-size: 14px;
}

.fee-input:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
}

.uniform-rate-input {
    border: 1px solid #ced4da;
    border-radius: 4px;
    padding: 4px 8px;
    font-size: 14px;
}

.uniform-rate-input:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
}
</style>
