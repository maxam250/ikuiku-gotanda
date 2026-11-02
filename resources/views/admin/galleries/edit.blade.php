<x-admin-layout>

    <x-slot name="header">
    ギャラリー編集
    </x-slot>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1>ギャラリー編集</h1>
                <a href="{{ route('admin.gallery.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> 一覧に戻る
                </a>
            </div>

            <div class="link mt-3">
                <a href="{{ route('gallery.show', $gallery->id) }}" target="_blank">{{ $gallery->title }}のギャラリーページ&nbsp;&raquo;</a>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <div class="row">
                <!-- 左カラム：画像・動画一覧 -->
                <div class="col-lg-8">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                画像・動画一覧 <small class="text-muted">（ドラッグで並び替え）</small>
                                @php $videoCount = $gallery->images->where('type', 'video')->count(); @endphp
                                <span class="badge bg-secondary ms-2">動画 {{ $videoCount }}/3</span>
                            </h5>
                        </div>
                        <div class="card-body">
                            <div id="sortable-images" style="display:flex; flex-wrap:wrap; gap:12px; padding:0; margin:0;">
                                @foreach ($gallery->images as $image)
                                <div data-id="{{ $image->id }}" data-type="{{ $image->type }}" class="gallery-card-item {{ $image->type === 'video' ? 'gallery-card-video' : 'gallery-card-image' }}" style="width:240px; border:1px solid #dee2e6; border-radius:6px; padding:10px; background:#fff;">

                                    @if ($image->type === 'video')
                                        {{-- 動画カード --}}
                                        <div style="position:relative; margin-bottom:8px;">
                                            <video src="{{ Storage::disk('public')->url($image->filename) }}" controls style="width:100%; height:160px; object-fit:contain; background:#000; border-radius:4px; display:block;"></video>
                                            <div style="position:absolute; top:6px; left:6px; background:rgba(0,0,0,0.6); color:#fff; font-size:11px; padding:2px 6px; border-radius:4px;">
                                                <i class="fas fa-film"></i> 動画
                                            </div>
                                        </div>
                                        <form action="{{ route('admin.gallery.image.delete', ['galleryId' => $gallery->id, 'imageId' => $image->id]) }}" method="POST" onsubmit="return confirm('この動画を削除してもよろしいですか？')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger btn-sm w-100"><i class="fas fa-trash"></i> 削除</button>
                                        </form>
                                    @else
                                        {{-- 画像カード --}}
                                        <img src="{{ Storage::url('gallery/' . $image->filename) }}" style="width:100%; height:160px; object-fit:cover; border-radius:4px; display:block;" class="mb-2">
                                        <form action="{{ route('admin.gallery.image.delete', ['galleryId' => $gallery->id, 'imageId' => $image->id]) }}" method="POST" class="mb-2" onsubmit="return confirm('この画像を削除してもよろしいですか？')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger btn-sm w-100">削除</button>
                                        </form>
                                        <form action="{{ route('admin.gallery.image.update', $image->id) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <input type="text" name="caption" value="{{ old('caption', $image->caption) }}" placeholder="キャプション" class="form-control form-control-sm mb-1">
                                            <button class="btn btn-primary btn-sm w-100">保存</button>
                                        </form>
                                    @endif

                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 右カラム：設定・アップロード -->
                <div class="col-lg-4">

                    <!-- 表示設定 -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">表示設定</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.gallery.update', $gallery->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="title" value="{{ $gallery->title }}">
                                <input type="hidden" name="member_id" value="{{ $gallery->member_id }}">
                                <input type="hidden" name="item_id" value="{{ $gallery->item_id }}">
                                <input type="hidden" name="description" value="{{ $gallery->description }}">

                                <div class="mb-3">
                                    <label class="form-label">表示</label>
                                    <select name="active" class="form-control">
                                        <option value="0" {{ old('active', $gallery->active) == 0 ? 'selected' : '' }}>非表示</option>
                                        <option value="1" {{ old('active', $gallery->active) == 1 ? 'selected' : '' }}>表示</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">公開範囲</label>
                                    <select name="flag" class="form-control">
                                        <option value="0" {{ old('flag', $gallery->flag) == 0 ? 'selected' : '' }}>全員</option>
                                        <option value="1" {{ old('flag', $gallery->flag) == 1 ? 'selected' : '' }}>ログインユーザー</option>
                                        <option value="2" {{ old('flag', $gallery->flag) == 2 ? 'selected' : '' }}>限定公開</option>
                                    </select>
                                </div>

                                <button type="submit" class="af-btn af-btn-primary af-btn-full">
                                    <i class="fas fa-save"></i> 設定を更新
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- 画像アップロード -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-image"></i> 画像アップロード</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.gallery.image.upload', $gallery->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-3">
                                    <input type="file" name="images[]" multiple class="form-control">
                                </div>
                                <button class="btn btn-primary">アップロード</button>
                            </form>
                        </div>
                    </div>

                    <!-- 動画アップロード -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-film"></i> 動画アップロード</h5>
                            <small class="text-muted">MP4 / MOV / WEBM &nbsp;最大15MB &nbsp;※自動圧縮 / 最大3件</small>
                        </div>
                        <div class="card-body">
                            @php $videoCount = $gallery->images->where('type', 'video')->count(); @endphp
                            <p class="mb-2 text-sm">登録済み: <strong>{{ $videoCount }} / 3件</strong></p>

                            @if ($videoCount < 3)
                            <form id="galleryVideoCardForm" enctype="multipart/form-data"
                                data-upload-url="{{ route('admin.gallery.video-card.upload', $gallery->id) }}"
                                data-video-count="{{ $videoCount }}">
                                <div class="mb-3">
                                    <input type="file" name="video" id="galleryVideoCardInput" accept="video/mp4,video/quicktime,video/webm" class="form-control">
                                </div>
                                <button type="submit" class="btn btn-primary">アップロード</button>
                                <span id="galleryVideoCardUploading" style="display:none; margin-left:0.5rem; color:#888; font-size:0.85rem;">アップロード・圧縮中...</span>
                            </form>
                            @else
                            <p class="text-muted mb-0"><i class="fas fa-lock"></i> 上限（3件）に達しました。動画を削除してから追加できます。</p>
                            @endif
                        </div>
                    </div>

                    <!-- ギャラリー情報編集 -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">ギャラリー情報編集</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.gallery.update', $gallery->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="mb-3">
                                    <label class="form-label">タイトル</label>
                                    <input type="text" name="title" value="{{ old('title', $gallery->title) }}" class="form-control">
                                </div>

                                {{--
                                <div class="mb-3">
                                    <label class="form-label">投稿者</label>
                                    <select name="member_id" class="form-control">
                                        <option value="0">会員選択</option>
                                        @foreach($members as $member)
                                            <option value="{{ $member->id }}" {{ old('member_id', $gallery->member_id) == $member->id ? 'selected' : '' }}>{{ $member->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                --}}

                                <div class="mb-3">
                                    <label class="form-label">キャスト</label>
                                    <select name="item_id" class="form-control">
                                        <option value="0">キャスト選択</option>
                                        @foreach($items as $item)
                                            <option value="{{ $item->id }}" {{ old('item_id', $gallery->item_id) == $item->id ? 'selected' : '' }}>{{ $item->item_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">コメント</label>
                                    <textarea name="description" class="form-control" rows="5">{{ old('description', $gallery->description) }}</textarea>
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> 更新
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    // 画像のみドラッグ対象（動画カードはsortableから除外）
    new Sortable(document.getElementById('sortable-images'), {
        animation: 150,
        filter: '[data-type="video"]',
        onEnd: function () {
            const order = Array.from(document.querySelectorAll('#sortable-images [data-id]')).map((el, index) => ({
                id: el.dataset.id,
                sort: index + 1
            }));
            fetch('{{ route("admin.gallery.image.sort", $gallery->id) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ order })
            });
        }
    });

    // 動画カードアップロード（AJAX → ページリロード）
    var galleryVideoForm = document.getElementById('galleryVideoCardForm');
    if (galleryVideoForm) {
        var galleryVideoUploadUrl = galleryVideoForm.dataset.uploadUrl;
        var galleryVideoCount    = parseInt(galleryVideoForm.dataset.videoCount, 10);
        galleryVideoForm.addEventListener('submit', function(e) {
            e.preventDefault();

            var fileInput = document.getElementById('galleryVideoCardInput');
            if (!fileInput.files.length) {
                alert('動画ファイルを選択してください');
                return;
            }

            var formData = new FormData(this);
            formData.append('_token', '{{ csrf_token() }}');

            var uploadingMsg = document.getElementById('galleryVideoCardUploading');
            uploadingMsg.style.display = 'inline';
            this.querySelector('button[type=submit]').disabled = true;

            fetch(galleryVideoUploadUrl, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(function(response) {
                return response.text().then(function(text) {
                    var cleaned = text.replace(/^\uFEFF+/, '').trim();
                    try { return JSON.parse(cleaned); } catch(e) { return { success: false, message: 'エラー' }; }
                });
            })
            .then(function(data) {
                if (data && data.success) {
                    window.location.reload();
                } else {
                    uploadingMsg.style.display = 'none';
                    galleryVideoForm.querySelector('button[type=submit]').disabled = false;
                    alert(data ? (data.message || '動画のアップロードに失敗しました') : 'エラーが発生しました');
                }
            })
            .catch(function(error) {
                uploadingMsg.style.display = 'none';
                galleryVideoForm.querySelector('button[type=submit]').disabled = false;
                alert(error.message || 'エラーが発生しました');
            });
        });
    }
</script>
@endpush

</x-admin-layout>
