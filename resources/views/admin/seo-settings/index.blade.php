<x-admin-layout>

<x-slot name="header">
SEO設定
</x-slot>

<section class="MessageArea">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif
</section>

{{-- 新規登録フォーム --}}
<section class="contentArea" style="margin-bottom:2rem;">
    <h3 style="font-size:1rem; font-weight:bold; margin-bottom:1rem; border-left:4px solid #3498db; padding-left:10px;">新規ページ登録</h3>
    <form action="{{ route('admin.seo-settings.store') }}" method="POST" style="display:flex; gap:1rem; align-items:flex-start; flex-wrap:wrap;">
        @csrf
        <div>
            <label style="display:block; font-size:0.85rem; color:#666; margin-bottom:4px;">ルートキー <small style="color:#e74c3c;">*</small></label>
            <input type="text" name="page_key" class="form-control" style="width:220px;"
                   placeholder="例: item.index" value="{{ old('page_key') }}" required>
            <small style="color:#999;">Route::currentRouteName() の値</small>
        </div>
        <div>
            <label style="display:block; font-size:0.85rem; color:#666; margin-bottom:4px;">ページ名 <small style="color:#e74c3c;">*</small></label>
            <input type="text" name="page_name" class="form-control" style="width:200px;"
                   placeholder="例: キャスト一覧" value="{{ old('page_name') }}" required>
        </div>
        <div><label style="display:block; font-size:0.85rem; color:#666; margin-bottom:4px;">&emsp;</label>
            <button type="submit" class="admin-btn admin-btn-submit">
                <i class="fas fa-plus"></i> 登録
            </button>
        </div>
    </form>
</section>

{{-- 一覧テーブル --}}
<section class="contentArea">
    <p style="margin-bottom:1rem; color:#666; font-size:0.9rem;">各ページのtitle・description・OGP・パンくずリスト情報を設定できます。</p>

    <table class="listTbl">
        <thead>
            <tr>
                <th>ページ名</th>
                <th>ルートキー</th>
                <th>titleタグ</th>
                <th>description</th>
                <th>パンくずラベル</th>
                <th>親ページキー</th>
                <th>noindex</th>
                <th class="action">アクション</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($seoSettings as $seo)
            <tr>
                <td><strong>{{ $seo->page_name }}</strong></td>
                <td><code style="font-size:0.8em; color:#888;">{{ $seo->page_key }}</code></td>
                <td>
                    @if($seo->meta_title)
                        <span style="color:#333;">{{ Str::limit($seo->meta_title, 30) }}</span>
                        <small style="color:{{ strlen($seo->meta_title) > 60 ? 'red' : '#999' }};">
                            ({{ strlen($seo->meta_title) }}字)
                        </small>
                    @else
                        <span style="color:#bbb;">未設定</span>
                    @endif
                </td>
                <td>
                    @if($seo->meta_description)
                        <span style="color:#333;">{{ Str::limit($seo->meta_description, 30) }}</span>
                    @else
                        <span style="color:#bbb;">未設定</span>
                    @endif
                </td>
                <td>
                    @if($seo->breadcrumb_label)
                        <span style="color:#27ae60;">{{ $seo->breadcrumb_label }}</span>
                    @else
                        <span style="color:#bbb;">（ページ名を使用）</span>
                    @endif
                </td>
                <td>
                    @if($seo->breadcrumb_parent_key)
                        <code style="font-size:0.8em; color:#666;">{{ $seo->breadcrumb_parent_key }}</code>
                    @else
                        <span style="color:#bbb;">—</span>
                    @endif
                </td>
                <td>
                    @if($seo->noindex)
                        <span style="background:#e74c3c; color:#fff; padding:2px 8px; border-radius:3px; font-size:0.8em;">noindex</span>
                    @else
                        <span style="color:#bbb;">index</span>
                    @endif
                </td>
                <td class="action">
                    <div class="admin-btn-group">
                        <a href="{{ route('admin.seo-settings.edit', $seo) }}" class="admin-btn admin-btn-secondary admin-btn-sm">
                            <i class="fas fa-edit"></i>編集
                        </a>
                        <form action="{{ route('admin.seo-settings.destroy', $seo) }}" method="POST"
                              onsubmit="return confirm('「{{ $seo->page_name }}」を削除しますか？');" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="admin-btn admin-btn-warning admin-btn-sm">
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

</x-admin-layout>
