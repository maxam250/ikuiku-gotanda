<x-admin-layout>
    <x-slot name="header">
        メールマガジンテンプレート編集
    </x-slot>

    <x-newsletter-menu />

    <div style="background:#fff; box-shadow:0 2px 8px rgba(0,0,0,0.1); border-radius:8px;">
        <div style="padding:1.5rem;">
            <h2 style="font-size:1.125rem; font-weight:600; color:#1f2937; margin-bottom:1.5rem;">テンプレート編集</h2>

            <form action="{{ route('admin.newsletter.templates.update', $template->id) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- テンプレート名 --}}
                <div style="margin-bottom:1rem;">
                    <label for="name" style="display:block; font-size:0.875rem; font-weight:500; color:#374151; margin-bottom:0.25rem;">テンプレート名 <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name', $template->name) }}" class="form-control" required>
                    @error('name')<p style="margin-top:0.25rem; font-size:0.8rem; color:#dc2626;">{{ $message }}</p>@enderror
                </div>

                {{-- 件名 --}}
                <div style="margin-bottom:1rem;">
                    <label for="subject" style="display:block; font-size:0.875rem; font-weight:500; color:#374151; margin-bottom:0.25rem;">件名 <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="subject" id="subject" value="{{ old('subject', $template->subject) }}" class="form-control" required>
                    @error('subject')<p style="margin-top:0.25rem; font-size:0.8rem; color:#dc2626;">{{ $message }}</p>@enderror
                </div>

                {{-- プレーンテキスト本文 --}}
                <div style="margin-bottom:1.5rem;">
                    <label for="body_text" style="display:block; font-size:0.875rem; font-weight:500; color:#374151; margin-bottom:0.25rem;">プレーンテキスト本文</label>
                    <textarea name="body_text" id="body_text" rows="10" class="form-control">{{ old('body_text', $template->body_text) }}</textarea>
                    @error('body_text')<p style="margin-top:0.25rem; font-size:0.8rem; color:#dc2626;">{{ $message }}</p>@enderror
                    <div style="margin-top:0.5rem;">
                        <button type="button" onclick="openTextPreview()" class="btn btn-secondary btn-sm">スマホプレビュー</button>
                    </div>
                </div>

                {{-- HTML本文 --}}
                <div style="margin-bottom:1.5rem;">
                    <div style="display:flex; align-items:center; gap:0.75rem; margin-bottom:0.5rem;">
                        <label style="font-size:0.875rem; font-weight:500; color:#374151;">HTML本文</label>
                        <button type="button" onclick="openSourceModal()" class="btn btn-secondary btn-sm">ソース</button>
                    </div>
                    {{-- TinyMCEが管理するtextarea（blockクラスを付けないこと） --}}
                    <textarea name="body_html" id="body_html">{{ old('body_html', $template->body_html) }}</textarea>
                    @error('body_html')<p style="margin-top:0.25rem; font-size:0.8rem; color:#dc2626;">{{ $message }}</p>@enderror
                    <div style="margin-top:0.5rem;">
                        <button type="button" onclick="openHtmlPreview()" class="btn btn-secondary btn-sm">スマホプレビュー</button>
                    </div>
                </div>

                <div style="display:flex; justify-content:flex-end; gap:0.75rem;">
                    <a href="{{ route('admin.newsletter.templates.index') }}" class="af-btn af-btn-cancel">キャンセル</a>
                    <button type="submit" class="af-btn af-btn-primary">更新</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ソースモーダル --}}
    <div id="source-modal" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; z-index:1000; background:rgba(0,0,0,0.6); overflow-y:auto;">
        <div style="background:#fff; width:90%; max-width:900px; margin:2rem auto; border-radius:8px; overflow:hidden; box-shadow:0 20px 60px rgba(0,0,0,0.3);">
            <div style="padding:1rem 1.5rem; border-bottom:1px solid #e5e7eb; display:flex; justify-content:space-between; align-items:center; background:#f9fafb;">
                <h3 style="font-size:1rem; font-weight:600; margin:0;">HTMLソース</h3>
                <button onclick="closeSourceModal()" style="background:none; border:none; font-size:1.5rem; cursor:pointer; color:#6b7280; line-height:1;">&times;</button>
            </div>
            <div style="padding:1.25rem;">
                <textarea id="source-editor" rows="22" style="width:100%; font-family:'Courier New',monospace; font-size:0.8rem; border:1px solid #d1d5db; border-radius:4px; padding:0.75rem; resize:vertical; box-sizing:border-box;"></textarea>
            </div>
            <div style="padding:0.75rem 1.5rem; border-top:1px solid #e5e7eb; display:flex; justify-content:flex-end; gap:0.75rem; background:#f9fafb;">
                <button onclick="closeSourceModal()" class="btn btn-secondary">キャンセル</button>
                <button onclick="applySource()" class="btn btn-primary">TinyMCEに反映</button>
            </div>
        </div>
    </div>

    {{-- プレーンテキスト プレビューモーダル --}}
    <div id="text-preview-modal" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; z-index:1000; background:rgba(0,0,0,0.6); overflow-y:auto;">
        <div style="display:flex; justify-content:center; padding:2rem;">
            <div style="background:#fff; width:480px; border-radius:8px; overflow:hidden; box-shadow:0 20px 60px rgba(0,0,0,0.3);">
                <div style="padding:1rem 1.5rem; border-bottom:1px solid #e5e7eb; display:flex; justify-content:space-between; align-items:center; background:#f9fafb;">
                    <h3 style="font-size:1rem; font-weight:600; margin:0;">プレビュー（スマホ）</h3>
                    <button onclick="closeTextPreview()" style="background:none; border:none; font-size:1.5rem; cursor:pointer; color:#6b7280; line-height:1;">&times;</button>
                </div>
                <div style="display:flex; justify-content:center; padding:1.5rem; background:#e5e7eb;">
                    <div style="width:390px; border:12px solid #1f2937; border-radius:44px; overflow:hidden; background:#fff; box-shadow:0 20px 40px rgba(0,0,0,0.4);">
                        <div style="background:#1f2937; height:28px; display:flex; justify-content:center; align-items:center;">
                            <div style="width:70px; height:5px; background:#4b5563; border-radius:3px;"></div>
                        </div>
                        <div id="text-preview-content" style="height:580px; overflow-y:auto; padding:1rem; font-size:0.8rem; font-family:sans-serif; white-space:pre-wrap; word-break:break-all; line-height:1.6;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- HTML プレビューモーダル --}}
    <div id="html-preview-modal" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; z-index:1000; background:rgba(0,0,0,0.6); overflow-y:auto;">
        <div style="display:flex; justify-content:center; padding:2rem;">
            <div style="background:#fff; width:480px; border-radius:8px; overflow:hidden; box-shadow:0 20px 60px rgba(0,0,0,0.3);">
                <div style="padding:1rem 1.5rem; border-bottom:1px solid #e5e7eb; display:flex; justify-content:space-between; align-items:center; background:#f9fafb;">
                    <h3 style="font-size:1rem; font-weight:600; margin:0;">プレビュー（スマホ）</h3>
                    <button onclick="closeHtmlPreview()" style="background:none; border:none; font-size:1.5rem; cursor:pointer; color:#6b7280; line-height:1;">&times;</button>
                </div>
                <div style="display:flex; justify-content:center; padding:1.5rem; background:#e5e7eb;">
                    <div style="width:390px; border:12px solid #1f2937; border-radius:44px; overflow:hidden; background:#fff; box-shadow:0 20px 40px rgba(0,0,0,0.4);">
                        <div style="background:#1f2937; height:28px; display:flex; justify-content:center; align-items:center;">
                            <div style="width:70px; height:5px; background:#4b5563; border-radius:3px;"></div>
                        </div>
                        <iframe id="html-preview-frame" style="width:100%; height:580px; border:none; display:block;"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="{{ asset('js/tinymce/tinymce.min.js') }}"></script>
    <script src="{{ asset('js/admin-tinymce.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            initAdminTinyMCE(
                '#body_html',
                '{{ asset("js/tinymce/skins/ui/" . ($siteSettings?->tinymce_skin ?? "oxide")) }}',
                '{{ asset("js/tinymce/skins/content/" . (($siteSettings?->tinymce_skin ?? "oxide") === "oxide-dark" ? "dark" : "default") . "/content.min.css") }}'
            );
        });

        /* ---- ソースモーダル ---- */
        function openSourceModal() {
            const ed = tinymce.get('body_html');
            const content = ed ? ed.getContent() : document.getElementById('body_html').value;
            document.getElementById('source-editor').value = content;
            document.getElementById('source-modal').style.display = 'block';
        }
        function closeSourceModal() {
            document.getElementById('source-modal').style.display = 'none';
        }
        function applySource() {
            const html = document.getElementById('source-editor').value;
            const ed = tinymce.get('body_html');
            if (ed) {
                ed.setContent(html);
            } else {
                document.getElementById('body_html').value = html;
            }
            closeSourceModal();
        }

        /* ---- プレーンテキストプレビュー ---- */
        function openTextPreview() {
            const text = document.getElementById('body_text').value;
            document.getElementById('text-preview-content').textContent = text;
            document.getElementById('text-preview-modal').style.display = 'block';
        }
        function closeTextPreview() {
            document.getElementById('text-preview-modal').style.display = 'none';
        }

        /* ---- HTMLプレビュー ---- */
        function openHtmlPreview() {
            const ed = tinymce.get('body_html');
            const html = ed ? ed.getContent() : document.getElementById('body_html').value;
            const frame = document.getElementById('html-preview-frame');
            frame.srcdoc = '<!DOCTYPE html><html><head><meta name="viewport" content="width=device-width,initial-scale=1"><style>body{margin:0;padding:12px;font-family:sans-serif;font-size:14px;line-height:1.6;word-break:break-word;}</style></head><body>' + html + '</body></html>';
            document.getElementById('html-preview-modal').style.display = 'block';
        }
        function closeHtmlPreview() {
            document.getElementById('html-preview-modal').style.display = 'none';
        }

        /* ---- モーダル外クリックで閉じる ---- */
        ['source-modal', 'text-preview-modal', 'html-preview-modal'].forEach(function (id) {
            document.getElementById(id).addEventListener('click', function (e) {
                if (e.target === this) this.style.display = 'none';
            });
        });
    </script>
    @endpush
</x-admin-layout>
