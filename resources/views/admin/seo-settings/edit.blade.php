<x-admin-layout>

<x-slot name="header">
SEO設定 - {{ $seoSetting->page_name }}
</x-slot>

<div class="back">
    <a href="{{ route('admin.seo-settings.index') }}" class="btn-primary">← 一覧に戻る</a>
</div>

{{-- 動的変数ヒント --}}
<div style="background:#f0f7ff; border:1px solid #b3d4f5; border-radius:6px; padding:0.9rem 1.2rem; margin-bottom:1.5rem; font-size:0.875rem; line-height:1.8;">
    <strong style="display:block; margin-bottom:0.4rem; color:#1a5fa8;">
        <i class="fas fa-info-circle"></i> 使える変数
    </strong>
    <table style="border-collapse:collapse; width:100%;">
        <tr>
            <td style="padding:2px 12px 2px 0; white-space:nowrap;">
                <code style="background:#e8f0fe; color:#1a5fa8; padding:1px 6px; border-radius:3px; font-size:0.85em;">{cast_name}</code>
            </td>
            <td style="color:#333;">キャスト名（キャスト詳細ページのみ有効）</td>
        </tr>
        <tr>
            <td style="padding:2px 12px 2px 0; white-space:nowrap;">
                <code style="background:#e8f0fe; color:#1a5fa8; padding:1px 6px; border-radius:3px; font-size:0.85em;">{shop_name}</code>
            </td>
            <td style="color:#333;">ショップ名</td>
        </tr>
    </table>
    <p style="margin:0.5rem 0 0; color:#666;">例: <code style="background:#e8f0fe; color:#1a5fa8; padding:1px 6px; border-radius:3px;">{cast_name}のプロフィール | {shop_name}</code></p>
</div>

<section class="MessageArea">
    @if($errors->any())
        <div class="alert alert-danger">
            <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif
</section>

<section class="contentArea">

    <form action="{{ route('admin.seo-settings.update', $seoSetting) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- ページ基本情報 --}}
        <h3 style="border-left:4px solid #2ecc71; padding-left:10px; margin:0 0 1rem; font-size:1rem;">ページ基本情報</h3>
        <div style="display:flex; gap:1.5rem; flex-wrap:wrap; margin-bottom:2rem;">
            <div class="form-group" style="flex:1; min-width:200px;">
                <label>ルートキー <small style="color:#999; font-weight:normal;">（変更不可）</small></label>
                <input type="text" class="form-control" value="{{ $seoSetting->page_key }}" readonly
                       style="background:#f8f8f8; color:#888; cursor:not-allowed;">
            </div>
            <div class="form-group" style="flex:1; min-width:200px;">
                <label for="page_name">ページ名 <span style="color:#e74c3c;">*</span></label>
                <input type="text" id="page_name" name="page_name" class="form-control"
                       value="{{ old('page_name', $seoSetting->page_name) }}" required>
            </div>
        </div>

        {{-- パンくずリスト設定 --}}
        <h3 style="border-left:4px solid #9b59b6; padding-left:10px; margin:0 0 1rem; font-size:1rem;">パンくずリスト設定</h3>
        <div style="display:flex; gap:1.5rem; flex-wrap:wrap; margin-bottom:2rem;">
            <div class="form-group" style="flex:1; min-width:200px;">
                <label for="breadcrumb_label">
                    パンくずラベル
                    <small style="color:#888; font-weight:normal;">（空欄の場合はページ名を使用）</small>
                </label>
                <input type="text" id="breadcrumb_label" name="breadcrumb_label" class="form-control"
                       value="{{ old('breadcrumb_label', $seoSetting->breadcrumb_label) }}"
                       placeholder="例: キャスト一覧">
            </div>
            <div class="form-group" style="flex:1; min-width:200px;">
                <label for="breadcrumb_parent_key">
                    親ページ
                    <small style="color:#888; font-weight:normal;">（1つ上の階層）</small>
                </label>
                <select id="breadcrumb_parent_key" name="breadcrumb_parent_key" class="form-control">
                    <option value="">— なし（ホームの直下）</option>
                    @foreach($allPages as $page)
                        @if($page->id !== $seoSetting->id)
                            <option value="{{ $page->page_key }}"
                                {{ old('breadcrumb_parent_key', $seoSetting->breadcrumb_parent_key) === $page->page_key ? 'selected' : '' }}>
                                {{ $page->page_name }}（{{ $page->page_key }}）
                            </option>
                        @endif
                    @endforeach
                </select>
                <small style="color:#999;">設定例: キャスト詳細ページの親に「キャスト一覧」を選択</small>
            </div>
        </div>

        {{-- パンくずプレビュー --}}
        <div style="background:#f8f9fa; border:1px solid #e9ecef; border-radius:6px; padding:0.8rem 1rem; margin-bottom:2rem; font-size:0.875rem; color:#495057;">
            <strong style="color:#6c757d;">プレビュー：</strong>
            <span id="bc-preview">ホーム &rsaquo; <span style="color:#333;">このページ</span></span>
        </div>

        {{-- Google検索プレビュー --}}
        <div class="form-group" style="margin-bottom:2rem;">
            <label style="font-weight:bold; color:#333;">Googleプレビュー</label>
            <div id="google-preview" style="border:1px solid #ddd; border-radius:8px; padding:1rem 1.2rem; background:#fff; max-width:600px; margin-top:0.5rem;">
                <div id="preview-url" style="color:#202124; font-size:12px; margin-bottom:2px;">{{ url('/') }}</div>
                <div id="preview-title" style="color:#1a0dab; font-size:18px; font-weight:normal; cursor:pointer; line-height:1.3; overflow:hidden; white-space:nowrap; text-overflow:ellipsis;">
                    {{ $seoSetting->meta_title ?: $seoSetting->page_name }}
                </div>
                <div id="preview-description" style="color:#4d5156; font-size:13px; line-height:1.5; margin-top:4px; overflow:hidden; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical;">
                    {{ $seoSetting->meta_description ?: 'descriptionが設定されていません。' }}
                </div>
            </div>
        </div>

        {{-- Basic SEO --}}
        <h3 style="border-left:4px solid #3498db; padding-left:10px; margin:1.5rem 0 1rem; font-size:1rem;">基本SEO設定</h3>

        <div class="form-group">
            <label for="meta_title">
                titleタグ
                <small style="color:#888; font-weight:normal;">（推奨: 30〜60文字）</small>
            </label>
            <input type="text" id="meta_title" name="meta_title" class="form-control"
                value="{{ old('meta_title', $seoSetting->meta_title) }}"
                placeholder="例: キャスト一覧 | {{ $shops->shop_name ?? 'サイト名' }}"
                maxlength="60">
            <div style="text-align:right; margin-top:4px;">
                <span id="title-count" style="font-size:0.85em; color:#888;">0</span>/60文字
                <span id="title-status" style="font-size:0.8em; margin-left:8px;"></span>
            </div>
        </div>

        <div class="form-group">
            <label for="meta_description">
                meta description
                <small style="color:#888; font-weight:normal;">（推奨: 70〜120文字）</small>
            </label>
            <textarea id="meta_description" name="meta_description" class="form-control" rows="3"
                placeholder="ページの内容を簡潔に説明する文章を入力してください。"
                maxlength="160">{{ old('meta_description', $seoSetting->meta_description) }}</textarea>
            <div style="text-align:right; margin-top:4px;">
                <span id="desc-count" style="font-size:0.85em; color:#888;">0</span>/160文字
                <span id="desc-status" style="font-size:0.8em; margin-left:8px;"></span>
            </div>
        </div>

        <div class="form-group">
            <label for="meta_keywords">
                キーワード（meta keywords）
                <small style="color:#888; font-weight:normal;">（カンマ区切り / Googleでは参考程度）</small>
            </label>
            <input type="text" id="meta_keywords" name="meta_keywords" class="form-control"
                value="{{ old('meta_keywords', $seoSetting->meta_keywords) }}"
                placeholder="撮影会, キャスト, 東京">
        </div>

        {{-- Robots設定 --}}
        <div class="form-group">
            <label>インデックス設定</label>
            <div class="unit" style="margin-top:4px;">
                <label style="display:flex; align-items:center; gap:8px; cursor:pointer; font-weight:normal;">
                    <input type="checkbox" name="noindex" value="1" {{ old('noindex', $seoSetting->noindex) ? 'checked' : '' }}
                        style="width:16px; height:16px;">
                    <span>noindex（検索エンジンにインデックスさせない）</span>
                </label>
                <p style="color:#e74c3c; font-size:0.85em; margin-top:4px;">※ チェックすると検索結果に表示されなくなります</p>
            </div>
        </div>

        <div class="form-group">
            <label for="canonical_url">
                canonical URL
                <small style="color:#888; font-weight:normal;">（空欄の場合は現在のURLが自動設定されます）</small>
            </label>
            <input type="url" id="canonical_url" name="canonical_url" class="form-control"
                value="{{ old('canonical_url', $seoSetting->canonical_url) }}"
                placeholder="https://example.com/page">
        </div>

        {{-- OGP設定 --}}
        <h3 style="border-left:4px solid #e67e22; padding-left:10px; margin:2rem 0 1rem; font-size:1rem;">OGP設定（SNSシェア用）</h3>

        <div class="form-group">
            <label for="og_title">
                OGPタイトル
                <small style="color:#888; font-weight:normal;">（空欄の場合はtitleタグを使用）</small>
            </label>
            <input type="text" id="og_title" name="og_title" class="form-control"
                value="{{ old('og_title', $seoSetting->og_title) }}"
                placeholder="SNSでシェアされた時に表示されるタイトル"
                maxlength="60">
        </div>

        <div class="form-group">
            <label for="og_description">
                OGP説明文
                <small style="color:#888; font-weight:normal;">（空欄の場合はmeta descriptionを使用）</small>
            </label>
            <textarea id="og_description" name="og_description" class="form-control" rows="2"
                placeholder="SNSでシェアされた時に表示される説明文"
                maxlength="200">{{ old('og_description', $seoSetting->og_description) }}</textarea>
        </div>

        <div class="form-group">
            <label for="og_image">
                OGP画像URL
                <small style="color:#888; font-weight:normal;">（推奨: 1200×630px / 空欄の場合はデフォルト画像を使用）</small>
            </label>
            <input type="text" id="og_image" name="og_image" class="form-control"
                value="{{ old('og_image', $seoSetting->og_image) }}"
                placeholder="/images/ogp-image.jpg または https://...">
            @if($seoSetting->og_image)
                <div style="margin-top:8px;">
                    <img src="{{ $seoSetting->og_image }}" alt="OGP preview"
                        style="max-width:300px; border:1px solid #ddd; border-radius:4px;"
                        onerror="this.style.display='none'">
                </div>
            @endif
        </div>

        <div style="margin-top:2rem; padding-top:1.5rem; border-top:1px solid #eee; display:flex; gap:1rem; align-items:center;">
            <button type="submit" class="admin-btn admin-btn-submit">
                <i class="fas fa-save"></i> 保存する
            </button>
            <a href="{{ route('admin.seo-settings.index') }}" style="color:#888;">キャンセル</a>
        </div>

    </form>
</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // パンくずプレビュー
    const bcLabelInput  = document.getElementById('breadcrumb_label');
    const bcParentSel   = document.getElementById('breadcrumb_parent_key');
    const bcPreview     = document.getElementById('bc-preview');
    const pageName      = '{{ $seoSetting->page_name }}';

    function updateBcPreview() {
        const label  = bcLabelInput.value.trim() || pageName;
        const opt    = bcParentSel.options[bcParentSel.selectedIndex];
        const parent = opt.value ? opt.text.replace(/（.*）/, '').trim() : '';
        let html = 'ホーム';
        if (parent) html += ' &rsaquo; ' + parent;
        html += ' &rsaquo; <strong>' + label + '</strong>';
        bcPreview.innerHTML = html;
    }
    bcLabelInput.addEventListener('input', updateBcPreview);
    bcParentSel.addEventListener('change', updateBcPreview);
    updateBcPreview();

    // SEOタイトル・description カウンター
    const titleInput = document.getElementById('meta_title');
    const descInput  = document.getElementById('meta_description');
    const titleCount = document.getElementById('title-count');
    const descCount  = document.getElementById('desc-count');
    const titleStatus = document.getElementById('title-status');
    const descStatus  = document.getElementById('desc-status');

    const previewTitle = document.getElementById('preview-title');
    const previewDesc  = document.getElementById('preview-description');

    function updateTitleCount() {
        const len = titleInput.value.length;
        titleCount.textContent = len;
        if (len === 0) {
            titleStatus.textContent = '';
            titleStatus.style.color = '#888';
        } else if (len < 30) {
            titleStatus.textContent = '短め';
            titleStatus.style.color = '#e67e22';
        } else if (len <= 60) {
            titleStatus.textContent = '適切';
            titleStatus.style.color = '#27ae60';
        } else {
            titleStatus.textContent = '長すぎ';
            titleStatus.style.color = '#e74c3c';
        }
        previewTitle.textContent = titleInput.value || '（titleが未設定）';
    }

    function updateDescCount() {
        const len = descInput.value.length;
        descCount.textContent = len;
        if (len === 0) {
            descStatus.textContent = '';
            descStatus.style.color = '#888';
        } else if (len < 70) {
            descStatus.textContent = '短め';
            descStatus.style.color = '#e67e22';
        } else if (len <= 120) {
            descStatus.textContent = '適切';
            descStatus.style.color = '#27ae60';
        } else if (len <= 160) {
            descStatus.textContent = '上限に近い';
            descStatus.style.color = '#e67e22';
        } else {
            descStatus.textContent = '長すぎ';
            descStatus.style.color = '#e74c3c';
        }
        previewDesc.textContent = descInput.value || '（descriptionが未設定）';
    }

    titleInput.addEventListener('input', updateTitleCount);
    descInput.addEventListener('input', updateDescCount);

    // 初期値
    updateTitleCount();
    updateDescCount();
});
</script>
@endpush

</x-admin-layout>
