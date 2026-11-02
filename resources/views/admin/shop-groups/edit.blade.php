<x-admin-layout>

<x-slot name="header">
系列店 編集
</x-slot>

<div class="back">
    <a href="{{ route('admin.shop-groups.index') }}" class="btn-primary">戻る</a>
</div>

@if(session('success'))
    <div class="alert alert-success" style="padding:0.75rem 1rem;margin-bottom:1rem;background:#d4edda;color:#155724;border:1px solid #c3e6cb;border-radius:4px;">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger" style="padding:0.75rem 1rem;margin-bottom:1rem;background:#f8d7da;color:#721c24;border:1px solid #f5c6cb;border-radius:4px;">
        <ul style="margin:0;padding-left:1.2rem;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<section class="contentArea">
    <form action="{{ route('admin.shop-groups.update', $shopGroup->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <table class="listTbl">
            <thead>
                <tr><th>項目</th><th>内容</th></tr>
            </thead>
            <tbody>
                <tr>
                    <th>公開</th>
                    <td>
                        <input type="checkbox" id="is_publish" name="is_publish" value="1" {{ $shopGroup->is_publish ? 'checked' : '' }}>
                        <label for="is_publish">公開する</label>
                    </td>
                </tr>
                <tr>
                    <th>店舗名 <span style="color:#dc2626;">*</span></th>
                    <td>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $shopGroup->name) }}" required>
                    </td>
                </tr>
                <tr>
                    <th>URL</th>
                    <td>
                        <input type="url" name="url" class="form-control" value="{{ old('url', $shopGroup->url) }}" placeholder="https://example.com">
                    </td>
                </tr>
                <tr>
                    <th>並び順</th>
                    <td>
                        <input type="number" name="sort" class="form-control" value="{{ old('sort', $shopGroup->sort) }}" style="width:80px;">
                    </td>
                </tr>
                <tr>
                    <th>現在の画像</th>
                    <td>
                        @if ($shopGroup->filename)
                            <img src="{{ Storage::url($shopGroup->filename) }}" alt="{{ $shopGroup->name }}" style="max-width:200px;border-radius:4px;display:block;margin-bottom:0.5rem;">
                            <p class="form-hint">新しい画像をアップロードすると差し替えられます</p>
                        @elseif ($shopGroup->image_url)
                            <img src="{{ $shopGroup->image_url }}" alt="{{ $shopGroup->name }}" style="max-width:200px;border-radius:4px;display:block;margin-bottom:0.5rem;">
                            <p class="form-hint">直リンク画像を使用中</p>
                        @else
                            <span style="color:#999;">画像なし</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>画像（アップロード）</th>
                    <td>
                        <input type="file" name="filename" accept="image/*">
                        <p class="form-hint">JPEG/PNG/GIF/WebP 5MB以内</p>
                    </td>
                </tr>
                <tr>
                    <th>画像（直リンクURL）</th>
                    <td>
                        <input type="url" name="image_url" class="form-control" value="{{ old('image_url', $shopGroup->image_url) }}" placeholder="https://example.com/image.jpg">
                        <p class="form-hint">アップロード画像がある場合はそちらが優先されます</p>
                    </td>
                </tr>
            </tbody>
        </table>

        <div style="text-align:center;margin-top:2rem;">
            <button type="submit" class="admin-btn admin-btn-submit">
                <i class="fas fa-save"></i> 更新
            </button>
        </div>
    </form>
</section>

</x-admin-layout>
