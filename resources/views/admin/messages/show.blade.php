<x-admin-layout>

<x-slot name="header">
メッセージ管理
</x-slot>

<section>
    <div class="container-fluid px-4">
        <div class="row">
            <!-- メッセージ履歴 -->
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="card-title mb-0">{{ $member->name }} さんとのメッセージ</h2>
                            <small class="text-muted">会員ID: {{ $member->member_no }}</small>
                        </div>
                        <a href="{{ route('admin.messages.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> 一覧に戻る
                        </a>
                    </div>
                    <div class="card-body chat-container bg-light p-4">
                        <div class="message-container">
                            @foreach($messages as $message)
                                <div class="message mb-5 {{ $message->sender_type === 'admin' ? 'text-end' : '' }}">
                                    <div class="message-content p-4 rounded {{ $message->sender_type === 'admin' ? 'bg-primary text-white' : 'bg-white border' }}"
                                         style="display: inline-block; max-width: 90%; position: relative;">
                                        <div class="message-body-wrap">
                                            @if($message->title)
                                                <div class="message-title text-lg font-semibold mb-4" style="font-size: 16px; line-height: 1.6; white-space: pre-wrap; text-align:left;">{{ $message->title }}</div>
                                            @endif
                                            <div class="message-text" style="font-size: 14px; line-height: 1.6; white-space: pre-wrap;text-align:left;">{{ $message->message }}</div>
                                            @if($message->image_path)
                                            <div class="mt-2">
                                                <img src="{{ Storage::url($message->image_path) }}"
                                                    alt="添付画像"
                                                    class="max-w-full h-auto rounded cursor-pointer hover:opacity-90"
                                                    onclick="openImageModal(this.src)"
                                                    style="max-height: 200px;">
                                            </div>
                                            @endif
                                        </div>
                                        <button type="button" class="msg-toggle-btn af-btn af-btn-cancel af-btn-sm" onclick="toggleMessageBody(this)" hidden>もっと見る ▼</button>
                                        <div class="message-time small"
                                             style="position: absolute; bottom: -25px; {{ $message->sender_type === 'admin' ? 'right: 0;' : 'left: 0;' }}; white-space: nowrap; color: {{ $message->sender_type === 'admin' ? 'rgba(255, 255, 255, 0.7)' : '#555' }};">
                                            <span style="color: #555;">{{ $message->created_at->format('Y年m月d日 H:i') }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="d-flex justify-content-center mt-4">
                            {{ $messages->links() }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- メッセージ送信フォーム -->
            <div class="col-md-4">
                <!-- 会員情報 -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h3 class="card-title mb-0">会員情報</h3>
                    </div>
                    <div class="card-body">
                        <dl class="row mb-0">
                            <dt class="col-sm-4">会員番号</dt>
                            <dd class="col-sm-8">{{ $member->member_no }}</dd>

                            <dt class="col-sm-4">名前</dt>
                            <dd class="col-sm-8">{{ $member->name }}</dd>

                            <dt class="col-sm-4">ハンドルネーム</dt>
                            <dd class="col-sm-8">{{ $member->nickname }}</dd>

                            <dt class="col-sm-4">登録日</dt>
                            <dd class="col-sm-8">{{ $member->created_at->format('Y年m月d日') }}</dd>
                        </dl>
                    </div>
                </div>

                <!-- メッセージフォーム -->
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.messages.store') }}" method="POST" enctype="multipart/form-data"  class="message-form">
                            @csrf
                            <input type="hidden" name="receiver_id" value="{{ $member->id }}">

                            @if(is_ezweb_email($member->email))
                            <div class="alert alert-warning d-flex align-items-center gap-2 py-2 mb-3 small" role="alert">
                                <span class="badge bg-secondary rounded-pill" title="EZweb"><i class="fas fa-mobile-alt me-1" aria-hidden="true"></i>EZweb</span>
                                <span class="flex-grow-1">EZwebのためメールを直接確認してください</span>
                            </div>
                            @endif

                            <!-- テンプレート管理リンク -->
                            <div class="mb-3">
                                <a href="{{ route('admin.message-templates.index') }}" class="text-decoration-none">
                                    <i class="fas fa-cog"></i> テンプレート管理&nbsp;&raquo;
                                </a>
                            </div>

                            <!-- テンプレート選択 -->
                            <div class="mb-3">
                                <div class="d-flex gap-2 align-items-center">
                                    <select class="form-select" id="template" name="template" style="max-width: 300px;">
                                        <option value="">テンプレートを選択</option>
                                        @foreach($templates as $template)
                                            <option value="{{ $template->id }}" data-title="{{ $template->title }}" data-content="{{ $template->content }}">{{ $template->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- 予約リスト -->
                            <div class="mb-3">
                                <label class="form-label">予約情報を選択</label>
                                <div class="reservation-list border rounded p-2" style="max-height: 200px; overflow-y: auto;">
                                    <div class="list-group list-group-flush">
                                        @foreach($reservations as $reservation)
                                            <button type="button" class="list-group-item list-group-item-action reservation-item"
                                                data-reservation="{{ json_encode($reservation) }}">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <span class="fw-bold">{{ \Carbon\Carbon::parse($reservation['reservation_datetime_parse'])->format('Y/m/d H:i') }}</span>
                                                        <span class="ms-2">{{ $reservation['member_name'] }}</span>
                                                    </div>
                                                    <div>
                                                        <span class="badge bg-primary">{{ $reservation['course_info'] }}</span>
                                                        @if($reservation['cast_name'])
                                                            <span class="badge bg-info">{{ $reservation['cast_name'] }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            </div>



                            <!-- 件名入力欄 -->
                            <div class="mb-3">
                                <input class="form-control" id="title" name="title" rows="30"
                                    style="width:100%;resize: none; font-size: 14px; line-height: 1.6;">
                            </div>

                            <script>
                                document.getElementById('template').addEventListener('change', function() {
                                    const selectedOption = this.options[this.selectedIndex];
                                    if (this.value) {
                                        document.getElementById('title').value = selectedOption.dataset.title || '';
                                        document.getElementById('message').value = selectedOption.dataset.content || '';
                                    } else {
                                        // テンプレートが選択されていない場合は入力欄をクリア
                                        document.getElementById('title').value = '';
                                        document.getElementById('message').value = '';
                                    }
                                });
                            </script>

                            <!-- メッセージ入力欄 -->
                            <div class="mb-3">
                                <textarea class="form-control" id="message" name="message" rows="30" required
                                    style="width:100%;resize: none; font-size: 14px; line-height: 1.6;"></textarea>
                            </div>
                            <!-- 画像アップロード欄 -->
                            <div class="mb-3">
                                <label for="image" class="form-label">画像ファイル（jpg, png, gif, webp）</label>
                                <input type="file" name="image" id="image" accept="image/jpeg,image/png,image/gif,image/webp" class="form-control">
                            </div>
                            <!-- 担当スタッフ -->
                            <div class="mb-3">
                                <label class="form-label fw-bold" style="font-size:0.85rem;">担当スタッフ</label>
                                <select name="staff_id" class="form-select form-select-sm">
                                    <option value="">-- 未選択 --</option>
                                    @foreach($staffs as $staff)
                                        <option value="{{ $staff->id }}"
                                            {{ session('selected_staff.id') == $staff->id ? 'selected' : '' }}>
                                            {{ $staff->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- 送信ボタン -->
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-paper-plane"></i> 送信
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<script>
    function openImageModal(src) {
        document.getElementById('modalImage').src = src;
        document.getElementById('imageModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeImageModal() {
        document.getElementById('imageModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // ESCキーでモーダルを閉じる
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeImageModal();
        }
    });

    // メッセージ表示エリアを最下部にスクロール
    // document.addEventListener('DOMContentLoaded', function() {
    //     const messageArea = document.querySelector('.bg-gray-100');
    //     messageArea.scrollTop = messageArea.scrollHeight;
    // });
</script>

<!-- 画像モーダル -->
<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 hidden flex items-center justify-center z-50" onclick="closeImageModal()">
    <div class="max-w-4xl max-h-[90vh] p-2">
        <img id="modalImage" src="" alt="拡大画像" class="max-w-full max-h-full">
    </div>
</div>

@push('styles')
<style>
.chat-container {
    background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23000000' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
}

.message-content {
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.message-title {
    white-space: pre-wrap;
    word-break: break-word;
}
.message-text {
    white-space: pre-wrap;
    word-break: break-word;
}

.message-body-wrap.is-collapsed {
    max-height: 500px;
    overflow: hidden;
}

.msg-toggle-btn {
    display: none;
    font-size: 12px;
    cursor: pointer;
    margin-top: 6px;
    color: inherit;
    background: none;
    border: 1px solid currentColor;
    border-radius: 4px;
    padding: 2px 8px;
    opacity: 0.85;
}
.msg-toggle-btn:hover {
    opacity: 1;
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid rgba(0,0,0,0.125);
}

.form-control:focus {
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
}

.btn-primary {
    transition: all 0.2s;
}

.btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

#template {
    min-width: 200px;
}

textarea#message {
    width: 100% !important;
}

.pagination {
    margin-bottom: 0;
}

.page-link {
    color: #555;
    background-color: #fff;
    border: 1px solid #dee2e6;
    padding: 0.5rem 0.75rem;
    margin: 0 2px;
    border-radius: 4px;
}

.page-link:hover {
    color: #0056b3;
    background-color: #e9ecef;
    border-color: #dee2e6;
}

.page-item.active .page-link {
    background-color: #007bff;
    border-color: #007bff;
}

.reservation-list .list-group-item {
    cursor: pointer;
    transition: background-color 0.2s;
}

.reservation-list .list-group-item:hover {
    background-color: #f8f9fa;
}

.reservation-list .list-group-item.selected {
    background-color: #e9ecef;
}
</style>
@endpush

@push('scripts')
<script>
// 即時実行関数でスコープを分離
(function() {
    // 要素の取得
    const templateSelect = document.getElementById('template');
    const messageTextarea = document.getElementById('message');

    // テンプレート選択の処理
    if (templateSelect && messageTextarea) {
        templateSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const content = selectedOption.getAttribute('data-content');
            const title = selectedOption.getAttribute('data-title');

            if (content) {
                messageTextarea.value = content;
                titleinput.value = title;
            } else {
                messageTextarea.value = '';
                titleinput.value = '';
            }
        });
    }

    // 予約情報クリック時の処理
    document.querySelectorAll('.reservation-item').forEach(item => {
        item.addEventListener('click', function() {
            // 選択状態の更新
            document.querySelectorAll('.reservation-item').forEach(el => {
                el.classList.remove('selected');
            });
            this.classList.add('selected');

            const reservationData = JSON.parse(this.getAttribute('data-reservation'));
            const titleInput = document.getElementById('title');
            let messageText = messageTextarea.value;
            let titleText = titleInput.value;

            // 変数の置換
            const replacements = {
                '{member_name}': reservationData.member_name,
                '{member_email}': reservationData.member_email,
                '{reservation_datetime}': reservationData.reservation_datetime,
                '{reservation_datetime_parse}': reservationData.reservation_datetime_parse,
                '{reservation_date_mdw}': reservationData.reservation_date_mdw,
                '{reservation_date}': reservationData.reservation_date,
                '{reservation_time}': reservationData.reservation_time,
                '{course_info}': reservationData.course_info,
                '{course_time}': reservationData.course_time,
                '{course_name}': reservationData.course_name,
                '{cast_name}': reservationData.cast_name,
                '{options}': reservationData.options,
                '{option_pays}': reservationData.option_pays,
                '{studio}': reservationData.studio,
                '{costumes}': reservationData.costumes,
                '{area}': reservationData.area,
                '{place}': reservationData.place,
                '{meet}': reservationData.meet,
                '{address}': reservationData.address,
                '{comment}': reservationData.comment || '',
                '{memo_share}': reservationData.memo_share || '',
                '{myoptions}': reservationData.myoptions || '',
                '{mycostumes}': reservationData.mycostumes || '',
                '{fee_course}': reservationData.fee_course || '',
                '{fee_option}': reservationData.fee_option || '',
                '{fee_myoption}': reservationData.fee_myoption || '',
                '{fee_mycostume}': reservationData.fee_mycostume || '',
                '{fee_adjust}': reservationData.fee_adjust || '',
                '{fee_adjust_memo}': reservationData.fee_adjust_memo || '',
                '{fee_total}': reservationData.fee_total.toString(),
                '{fee_total_tax}': reservationData.fee_total_tax || ''
            };

            Object.entries(replacements).forEach(([key, value]) => {
                // myoptions と mycostumes の場合、改行文字を実際の改行に変換
                if (key === '{myoptions}' || key === '{mycostumes}') {
                    value = (value || '').replace(/\\n/g, '\n');
                }
                messageText = messageText.replace(new RegExp(key, 'g'), value || '');
                titleText = titleText.replace(new RegExp(key, 'g'), value || '');
            });

            messageTextarea.value = messageText;
            titleInput.value = titleText;
        });
    });

    // メッセージ送信時のアニメーション
    const form = document.querySelector('.message-form');
    if (form) {
        const submitButton = form.querySelector('button[type="submit"]');
        form.addEventListener('submit', function() {
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> 送信中...';
        });
    }

    // メッセージ本文が長い場合は折りたたみ（500px 超）
    document.querySelectorAll('.message-body-wrap').forEach(function(wrap) {
        if (wrap.scrollHeight > 500) {
            wrap.classList.add('is-collapsed');
            const btn = wrap.nextElementSibling;
            if (btn && btn.classList.contains('msg-toggle-btn')) {
                btn.style.display = 'inline-block';
            }
        }
    });
})();

function toggleMessageBody(btn) {
    const wrap = btn.previousElementSibling;
    if (!wrap || !wrap.classList.contains('message-body-wrap')) return;
    if (wrap.classList.contains('is-collapsed')) {
        wrap.classList.remove('is-collapsed');
        btn.textContent = '閉じる ▲';
    } else {
        wrap.classList.add('is-collapsed');
        btn.textContent = 'もっと見る ▼';
    }
}
</script>
@endpush

</x-admin-layout>
