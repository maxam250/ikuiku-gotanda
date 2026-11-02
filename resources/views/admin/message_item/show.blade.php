<x-admin-layout>
    <x-slot name="header">{{ $item->item_name }} さんとのメッセージ</x-slot>

    <div class="container py-4">
        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">メッセージ履歴</div>
                    <div class="card-body bg-light p-4">
                        <div class="message-list">
                            @foreach($messages as $message)
                                <div class="mb-5 {{ $message->sender_type === 'admin' ? 'text-end' : '' }}">
                                    <div class="message-content p-4 rounded {{ $message->sender_type === 'admin' ? 'bg-primary text-white' : 'bg-white border' }}"
                                         style="display: inline-block; max-width: 90%; position: relative;">
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
                                        <div class="message-time small"
                                             style="position: absolute; bottom: -25px; {{ $message->sender_type === 'admin' ? 'right: 0;' : 'left: 0;' }}; white-space: nowrap; color: {{ $message->sender_type === 'admin' ? 'rgba(255, 255, 255, 0.7)' : '#555' }};">
                                            <span style="color: #555;">{{ $message->created_at->format('Y年m月d日 H:i') }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>




                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">メッセージ送信</div>
                    <div class="card-body">
                        <form action="{{ route('admin.message_item.store', $item->id) }}" method="POST" enctype="multipart/form-data" class="message-form">
                            @csrf
                            <input type="hidden" name="receiver_id" value="{{ $item->id }}">

                            @if(is_ezweb_email($item->email))
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
                                                        <span class="fw-bold">
                                                            {{ \Carbon\Carbon::parse($reservation['reservation_datetime_parse'] ?? (($reservation['date'] ?? '') . ' ' . ($reservation['time_start'] ?? '')))->format('Y/m/d H:i') }}
                                                        </span>
                                                        <span class="ms-2">{{ $reservation['member_name'] ?? $reservation['customer_name'] ?? '' }}</span>
                                                    </div>
                                                    <div>
                                                        <span class="badge bg-primary">{{ $reservation['course_info'] ?? '' }}</span>
                                                        @if(!empty($reservation['cast_name']))
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

        <!-- 画像モーダル -->
        <div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 hidden flex items-center justify-center z-50" onclick="closeImageModal()">
            <div class="max-w-4xl max-h-[90vh] p-2">
                <img id="modalImage" src="" alt="拡大画像" class="max-w-full max-h-full">
            </div>
        </div>
    </div>

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
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeImageModal();
            }
        });

        // 予約貼り付け
        // document.querySelectorAll('.reservation-item').forEach(function(btn) {
        //     btn.addEventListener('click', function() {
        //         const reservation = JSON.parse(this.dataset.reservation);
        //         let text = '';
        //         if (reservation.reservation_datetime) {
        //             text += '予約日時: ' + reservation.reservation_datetime + '\n';
        //         }
        //         if (reservation.member_name) {
        //             text += '会員名: ' + reservation.member_name + '\n';
        //         }
        //         if (reservation.customer_name) {
        //             text += 'お客様名: ' + reservation.customer_name + '\n';
        //         }
        //         if (reservation.course_info) {
        //             text += 'コース: ' + reservation.course_info + '\n';
        //         }
        //         if (reservation.cast_name) {
        //             text += 'キャスト: ' + reservation.cast_name + '\n';
        //         }
        //         document.getElementById('message').value += text;
        //     });
        // });
    </script>



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
                '{memo}': reservationData.memo || '',
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
})();
</script>
@endpush


<style>
    .message-title {
        white-space: pre-wrap;
        word-break: break-word;
    }
    </style>
</x-admin-layout>
