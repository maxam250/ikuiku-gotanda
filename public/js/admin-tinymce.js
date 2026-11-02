/**
 * 管理画面共通 TinyMCE 初期化
 * ツールバー・プラグイン等を変更する場合はこのファイルのみ修正してください。
 *
 * @param {string} selector  - TinyMCEを適用するCSSセレクタ (例: '#editor')
 * @param {string} skinUrl   - スキンのURL (Blade側で asset() を使って渡す)
 * @param {string} contentCss - content.min.css の URL (同上)
 * @param {object} [extraOptions] - ページ固有の上書きオプション (任意)
 */
function initAdminTinyMCE(selector, skinUrl, contentCss, extraOptions) {
    if (typeof tinymce === 'undefined') return;

    var config = {
        selector: selector,
        height: 500,
        menubar: false,
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
        language: 'ja',
        license_key: 'gpl',
        branding: false,
        skin_url: skinUrl,
        content_css: contentCss
    };

    if (extraOptions) {
        Object.assign(config, extraOptions);
    }

    tinymce.init(config);
}
