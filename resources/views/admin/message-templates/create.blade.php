<x-admin-layout>
    <x-slot name="header">
            テンプレ作成
    </x-slot>

    @push('styles')
    <style>
        .reservation-list {
            max-height: 300px;
            overflow-y: auto;
        }
        .reservation-card {
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .reservation-card:hover {
            background-color: #f3f4f6;
        }
        .reservation-card.selected {
            background-color: #e5e7eb;
        }
        .template-vars-panel {
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            background-color: #f9fafb;
            padding: 1rem 1.25rem;
        }
        .template-vars-panel ul.template-var-list {

        }
        .template-vars-panel code.template-var-token {
            cursor: copy; /* クリップボードへコピーする操作の標準カーソル */
            user-select: none;
            transition: background-color 0.15s ease, box-shadow 0.15s ease;
        }
        .template-vars-panel code.template-var-token:hover {
            cursor: copy;
            background-color: #dbeafe !important;
            box-shadow: 0 0 0 1px #93c5fd;
        }
        .template-vars-panel code.template-var-token.is-copied {
            background-color: #bbf7d0 !important;
            box-shadow: 0 0 0 1px #4ade80;
        }
    </style>
    @endpush

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('admin.message-templates.store') }}" class="template-form">
                        @csrf

                        <div class="flex flex-col lg:flex-row gap-8 lg:gap-10 lg:items-start">
                            <!-- 左：テンプレート内容（変数一覧 ＋ 本文） -->
                            <div class="w-full lg:w-1/4 min-w-0 space-y-4">
                                <h2 class="text-lg font-medium text-gray-700 border-b border-gray-200 pb-2">テンプレート内容</h2>
                                <div class="template-vars-panel">
                                    <p class="text-sm font-semibold text-gray-800 mb-1">利用可能な変数</p>
                                    <p class="text-xs text-gray-500 mb-2">灰色の変数名をクリックでコピーできます。</p>
                                    <ul class="template-var-list list-disc list-inside text-sm text-gray-600 space-y-3 pl-0.5">
                                        <li><code class="template-var-token text-xs bg-gray-200 px-1 rounded" title="クリックでコピー">{member_name}</code> 会員名</li>
                                        <li><code class="template-var-token text-xs bg-gray-200 px-1 rounded" title="クリックでコピー">{member_email}</code> 会員メールアドレス</li>
                                        <li><code class="template-var-token text-xs bg-gray-200 px-1 rounded" title="クリックでコピー">{reservation_datetime}</code> 予約日時</li>
                                        <li><code class="template-var-token text-xs bg-gray-200 px-1 rounded" title="クリックでコピー">{reservation_date_mdw}</code> 月日(曜)</li>
                                        <li><code class="template-var-token text-xs bg-gray-200 px-1 rounded" title="クリックでコピー">{reservation_date}</code> 年月日(曜)</li>
                                        <li><code class="template-var-token text-xs bg-gray-200 px-1 rounded" title="クリックでコピー">{reservation_time}</code> ○○:○○</li>
                                        <li><code class="template-var-token text-xs bg-gray-200 px-1 rounded" title="クリックでコピー">{course_info}</code> コース名（○○分）</li>
                                        <li><code class="template-var-token text-xs bg-gray-200 px-1 rounded" title="クリックでコピー">{course_name}</code> コース名</li>
                                        <li><code class="template-var-token text-xs bg-gray-200 px-1 rounded" title="クリックでコピー">{course_time}</code> コース時間</li>
                                        <li><code class="template-var-token text-xs bg-gray-200 px-1 rounded" title="クリックでコピー">{cast_name}</code> キャスト名</li>
                                        <li><code class="template-var-token text-xs bg-gray-200 px-1 rounded" title="クリックでコピー">{options}</code> オプション</li>
                                        <li><code class="template-var-token text-xs bg-gray-200 px-1 rounded" title="クリックでコピー">{option_pays}</code> オプションPAY</li>
                                        <li><code class="template-var-token text-xs bg-gray-200 px-1 rounded" title="クリックでコピー">{studio}</code> スタジオ</li>
                                        <li><code class="template-var-token text-xs bg-gray-200 px-1 rounded" title="クリックでコピー">{costumes}</code> コスチューム</li>
                                        <li><code class="template-var-token text-xs bg-gray-200 px-1 rounded" title="クリックでコピー">{area}</code> 利用エリア</li>
                                        <li><code class="template-var-token text-xs bg-gray-200 px-1 rounded" title="クリックでコピー">{place}</code> 具体的な場所</li>
                                        <li><code class="template-var-token text-xs bg-gray-200 px-1 rounded" title="クリックでコピー">{meet}</code> 待ち合わせ方法</li>
                                        <li><code class="template-var-token text-xs bg-gray-200 px-1 rounded" title="クリックでコピー">{address}</code> 住所</li>
                                        <li><code class="template-var-token text-xs bg-gray-200 px-1 rounded" title="クリックでコピー">{comment}</code> 予約備考</li>
                                        <li><code class="template-var-token text-xs bg-gray-200 px-1 rounded" title="クリックでコピー">{memo_share}</code> メモ（共有用）</li>
                                        <li><code class="template-var-token text-xs bg-gray-200 px-1 rounded" title="クリックでコピー">{mycostumes}</code> マイコスチューム</li>
                                        <li><code class="template-var-token text-xs bg-gray-200 px-1 rounded" title="クリックでコピー">{myoptions}</code> マイオプション</li>
                                        <li><code class="template-var-token text-xs bg-gray-200 px-1 rounded" title="クリックでコピー">{fee_course}</code> コース料金</li>
                                        <li><code class="template-var-token text-xs bg-gray-200 px-1 rounded" title="クリックでコピー">{fee_option}</code> オプション料金</li>
                                        <li><code class="template-var-token text-xs bg-gray-200 px-1 rounded" title="クリックでコピー">{fee_adjust}</code> 調整金額</li>
                                        <li><code class="template-var-token text-xs bg-gray-200 px-1 rounded" title="クリックでコピー">{fee_adjust_memo}</code> 調整金額メモ</li>
                                        <li><code class="template-var-token text-xs bg-gray-200 px-1 rounded" title="クリックでコピー">{fee_total}</code> 合計金額</li>
                                        <li><code class="template-var-token text-xs bg-gray-200 px-1 rounded" title="クリックでコピー">{fee_total_tax}</code> 合計金額（税込）</li>
                                    </ul>
                                </div>

                            </div>

                            <!-- 右：フォーム（メタ情報・送信） -->
                            <div class="w-full lg:w-3/4 min-w-0 space-y-6">
                                <div class="space-y-2">
                                    <label for="title" class="block text-lg font-medium text-gray-700">タイトル</label>
                                    <input type="text" name="title" id="title"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        value="{{ old('title') }}" required>
                                    @error('title')
                                        <p class="text-red-600 text-sm">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="space-y-2">
                                    <label for="type" class="block text-lg font-medium text-gray-700">種別</label>
                                    <select name="type" id="type"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="member" {{ old('type') == 'member' ? 'selected' : '' }}>会員向け</option>
                                        <option value="cast" {{ old('type') == 'cast' ? 'selected' : '' }}>キャスト向け</option>
                                        <option value="common" {{ old('type') == 'common' ? 'selected' : '' }}>共通</option>
                                    </select>
                                    @error('type')
                                        <p class="text-red-600 text-sm">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="space-y-2">
                                    <label for="description" class="block text-lg font-medium text-gray-700">説明</label>
                                    <textarea name="description" id="description" rows="3"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description') }}</textarea>
                                    @error('description')
                                        <p class="text-red-600 text-sm">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="space-y-2">
                                    <label for="content" class="block text-sm font-medium text-gray-700">本文</label>
                                    <textarea name="content" id="content" rows="22"
                                        class="template-content-field block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 resize-y">{{ old('content') }}</textarea>
                                    @error('content')
                                        <p class="text-red-600 text-sm">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="pt-2 flex flex-wrap justify-center gap-6">
                                    <a href="{{ route('admin.message-templates.index') }}"
                                        class="btn btn-secondary px-6 py-3 text-lg font-medium rounded-lg">
                                        戻る
                                    </a>
                                    <button type="submit"
                                        class="btn btn-primary px-6 py-3 text-lg font-medium rounded-lg">
                                        保存
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {
            function fallbackCopyText(text) {
                var ta = document.createElement('textarea');
                ta.value = text;
                ta.setAttribute('readonly', '');
                ta.style.cssText = 'position:fixed;left:-9999px;top:0';
                document.body.appendChild(ta);
                ta.select();
                try { document.execCommand('copy'); } catch (e) {}
                document.body.removeChild(ta);
            }
            $(document).on('click', '.template-vars-panel code.template-var-token', function(e) {
                e.preventDefault();
                var text = $(this).text().trim();
                if (!text) return;
                var $code = $(this);
                var done = function() {
                    $code.addClass('is-copied');
                    clearTimeout($code.data('copyTimer'));
                    $code.data('copyTimer', setTimeout(function() {
                        $code.removeClass('is-copied');
                    }, 1200));
                };
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(text).then(done).catch(function() {
                        fallbackCopyText(text);
                        done();
                    });
                } else {
                    fallbackCopyText(text);
                    done();
                }
            });

            // 予約リストを取得
            function loadReservations() {
                $.get("{{ route('admin.reservations.future') }}", function(data) {
                    const reservationList = $('#reservationList');
                    reservationList.empty();

                    data.forEach(function(reservation) {
                        const card = $(`
                            <div class="reservation-card p-4" data-id="${reservation.id}">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <div class="font-medium">${reservation.date} ${reservation.time}</div>
                                        <div class="text-sm text-gray-600">
                                            ${reservation.cast_name} / ${reservation.course_name}
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-medium">${reservation.fee_total}</div>
                                        <div class="text-sm text-gray-600">${reservation.status}</div>
                                    </div>
                                </div>
                            </div>
                        `);
                        reservationList.append(card);
                    });
                });
            }

            // 予約カードクリック時の処理
            $(document).on('click', '.reservation-card', function() {
                const reservationId = $(this).data('id');
                $('.reservation-card').removeClass('selected');
                $(this).addClass('selected');

                // 予約詳細を取得してテンプレートに反映（プレースホルダーを実際のIDに置換）
                const detailsUrl = "{{ route('admin.reservations.get-details', ['id' => '__ID__']) }}".replace('__ID__', reservationId);
                $.get(detailsUrl, function(data) {
                    const template = `■予約日時
${data.datetime}

■コース
${data.course}

■キャスト
${data.cast_name}

■オプション
${data.options}

■オプションPAY
${data.option_pays}

■スタジオ
${data.studio}

■コスチューム
${data.costumes}

■マイオプション
${data.myoptions.map(opt => `${opt.name}: ${opt.price}円`).join('\n')}

■マイコスチューム
${data.mycostumes.map(cost => `${cost.name}: ${cost.price}円`).join('\n')}

■ご利用エリア
${data.area}

■具体的な場所
${data.place}

■待ち合わせ方法
${data.meet}

■住所
${data.address}

■料金
コース料金: ${data.fees.course}円
${data.fees.option ? `オプション料金: ${data.fees.option}円\n` : ''}${data.fees.myoption ? `マイオプション料金: ${data.fees.myoption}円\n` : ''}${data.fees.mycostume ? `マイコスチューム料金: ${data.fees.mycostume}円\n` : ''}
合計: ${data.fees.total}円`;

                    $('#content').val(template);
                });
            });

            // 初期読み込み
            loadReservations();
        });
    </script>
    @endpush
</x-admin-layout>
