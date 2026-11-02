<x-admin-layout>
    <x-slot name="header">
        HOME
    </x-slot>

<section class="content">
{{-- 
    <section class="flex gap-8 mb-4 p-6 bg-white rounded-xl ">
        <div>
        <p>
        キャストマイページURL<br>
        <div class="url-copy-row">
            <input id="cast-mypage-url" type="text" readonly value="{{ $shops->shop_url }}/item" class="w-full text-base text-gray-800 bg-gray-50 py-1 px-2 rounded-0">
            <button type="button" class="copy-url-btn af-btn af-btn-cancel af-btn-sm" data-copy-target="cast-mypage-url">コピー</button>
        </div>
        </p>
        </div>
        <div>
        <p>
        会員マイページURL<br>
        <div class="url-copy-row">
            <input id="member-mypage-url" type="text" readonly value="{{ $shops->shop_url }}/member" class="w-full text-base text-gray-800 bg-gray-50 py-1 px-2 rounded-0">
            <button type="button" class="copy-url-btn af-btn af-btn-cancel af-btn-sm" data-copy-target="member-mypage-url">コピー</button>
        </div>
        </p>
        </div>
    </section>
 --}}
    {{-- アクセスログ枠：記録は一時停止。後日 LockerStudio を導入予定 --}}
{{--     
    <section class="mb-4 p-4 bg-white rounded-xl dashboard-access-log">
        <div class="dal-head">
            <h3 class="dal-title">アクセス状況</h3>
        </div>
        <p class="dal-placeholder">LockerStudio 導入予定</p>
    </section>
 --}}
    {{-- <section class="mb-4 p-6 bg-white rounded-xl">
        <h3 class="text-lg font-bold mb-4 text-gray-800">予約一覧</h3>
        @php
            $reservationGroups = [
                [
                    'label' => $today->format('Y/m/d') . '(' . $today->isoFormat('ddd') . ')',
                    'rows' => $todayReservations,
                ],
                [
                    'label' => $tomorrow->format('Y/m/d') . '(' . $tomorrow->isoFormat('ddd') . ')',
                    'rows' => $tomorrowReservations,
                ],
            ];
        @endphp

        @foreach($reservationGroups as $group)
            <div class="mb-4">
                <h4 class="text-base font-semibold mb-2">{{ $group['label'] }}</h4>
                @if($group['rows']->isEmpty())
                    <p class="text-sm text-gray-500">予約はありません。</p>
                @else
                    <div class="reservation-table-wrap">
                        <table class="listTbl reservation-list-table">
                            <thead>
                                <tr>
                                    <th>詳細</th>
                                    <th>種別</th>
                                    <th>日時</th>
                                    <th>予約名</th>
                                    <th>コース</th>
                                    <th>キャスト</th>
                                    <th>メモ</th>
                                    <th>メモ共有</th>
                                    <th>場所</th>
                                    <th>料金</th>
                                    <th>フラグ</th>
                                    <th>アクション</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($group['rows'] as $reservation)
                                    <tr>
                                        <td class="center">
                                            <button type="button" data-af-modal-open="#resModal{{ $reservation->id }}" aria-haspopup="dialog" style="border: none;background:transparent;cursor:pointer;padding:2px;font-size:inherit;">
                                                <i class="las la-comment-dots"></i>
                                            </button>
                                            @include('admin.reservations.detail')
                                        </td>
                                        <td class="center reservation-kind-col"><p>ID:{{ $reservation->id }}</p>{{ $reservation->kind == 1 ? '通常' : 'リクエスト' }}</td>
                                        <td>
                                            <p>{{ $reservation->date ? $reservation->date->isoFormat('YYYY/MM/DD(ddd)') : '' }}</p>
                                            <p>
                                                {{ $reservation->time_start ? $reservation->time_start->format('H:i') : '' }} -
                                                {{ $reservation->time_end ? $reservation->time_end->format('H:i') : '' }}
                                            </p>
                                        </td>
                                        <td class="center">
                                            <p>[ {{ $reservation->member_id }} ]</p>
                                            <p class="whitespace-nowrap">{{ $reservation->name }}</p>
                                        </td>
                                        <td>
                                            @foreach($courses as $course)
                                                <span class="whitespace-nowrap">{{ $course->id == $reservation->course_id ? $course->title : '' }}</span>
                                            @endforeach
                                            {{ $reservation->course_time ? $reservation->course_time : '-' }}分
                                        </td>
                                        <td>
                                            {{ $reservation->item_name }}
                                            @if ($reservation->item_name_other)
                                                <p>{{ $reservation->item_name_other }}</p>
                                            @endif
                                        </td>
                                        <td class="size_15">{!! nl2br(e($reservation->memo)) !!}</td>
                                        <td class="size_15">{!! nl2br(e($reservation->memo_share)) !!}</td>
                                        <td>
                                            @if ($reservation->course_id != 3)
                                                @foreach($equipments as $equipment)
                                                    @php
                                                        $equipmentId = $equipment->external_id ?? $equipment->id;
                                                    @endphp
                                                    <span class="whitespace-nowrap">{{ $equipmentId == $reservation->equipment_id ? $equipment->title : '' }}</span>
                                                @endforeach
                                            @else
                                                {{ $reservation->area }} / {{ $reservation->place }} / {{ $reservation->address }}
                                            @endif
                                        </td>
                                        <td>
                                            <p class="whitespace-nowrap text-base font-semibold">{{ number_format($reservation->fee_total) }}円</p>
                                            <p class="whitespace-nowrap text-xs text-gray-600">税込 {{ number_format($reservation->fee_total * config('tax.intax')) }}円</p>
                                        </td>
                                        <td class="size_6">
                                            <select class="flag-select" data-reservation-id="{{ $reservation->id }}" id="colorSelect" onChange="changeColor(this)">
                                                <option value="0" {{ $reservation->flag == 0 ? 'selected' : '' }}>未設定</option>
                                                <option value="1" {{ $reservation->flag == 1 ? 'selected' : '' }}>確認中</option>
                                                <option value="2" {{ $reservation->flag == 2 ? 'selected' : '' }}>確定</option>
                                                <option value="3" {{ $reservation->flag == 3 ? 'selected' : '' }}>保留</option>
                                                <option value="4" {{ $reservation->flag == 4 ? 'selected' : '' }}>キャンセル</option>
                                            </select>
                                        </td>
                                        <td>
                                            <div class="admin-btn-group">
                                                <a href="{{ route('admin.reservations.edit', $reservation->id) }}" class="admin-btn admin-btn-secondary admin-btn-base">
                                                    <i class="fas fa-edit"></i>編集
                                                </a>
                                                <a href="{{ $reservation->member_id
                                                            ? route('admin.messages.show', $reservation->member_id)
                                                            : route('admin.messages.show.guest', urlencode($reservation->email)) }}"
                                                class="admin-btn admin-btn-secondary admin-btn-base bg-teal-600 border-0">
                                                    <i class="fa-solid fa-message"></i>メッセージ
                                                </a>
                                                <form action="{{ route('admin.reservations.destroy', $reservation->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="admin-btn admin-btn-warning admin-btn-base" onclick="return confirm('本当に削除しますか？')">
                                                        <i class="fas fa-trash"></i>削除
                                                    </button>
                                                </form>
                                                <a href="{{ route('admin.reservations.print') }}?single_id={{ $reservation->id }}"
                                                   target="_blank"
                                                   class="admin-btn admin-btn-base"
                                                   style="background:#2e7d32;color:#fff;border-color:#2e7d32;">
                                                    <i class="fas fa-print"></i>印刷
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        @endforeach
    </section> --}}





    <!-- 共有メモ -->
    <section class="mb-4 p-4 bg-white rounded-xl">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="fw-bold mb-0" style="font-size:1rem;">共有メモ <span class="text-muted fw-normal" style="font-size:0.8rem;">（直近10件）</span></h3>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.shared_memos.create') }}" class="admin-btn admin-btn-success admin-btn-xs">＋ 新規</a>
                <a href="{{ route('admin.shared_memos.index') }}" class="admin-btn admin-btn-secondary admin-btn-xs">一覧</a>
            </div>
        </div>
        @if($sharedMemos->isEmpty())
            <p class="text-muted small">共有メモはありません</p>
        @else
            <table class="admin-table" style="font-size:0.82rem;">
                <thead>
                    <tr>
                        <th style="width:60px;">詳細</th>
                        <th style="width:90px;">日付</th>
                        <th style="width:100px;">カテゴリ</th>
                        <th style="width:80px;">スタッフ</th>
                        <th>タイトル / メモ</th>
                        <th style="width:60px;">操作</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sharedMemos as $memo)
                        <tr>
                            <td>
                                <a href="#" role="button" data-af-modal-open="#sharedMemoModal{{ $memo->id }}" aria-haspopup="dialog">詳細</a>
                            </td>
                            <td class="text-nowrap">{{ $memo->memo_date->format('m/d') }}</td>
                            <td>
                                @if($memo->category)
                                    <span class="badge bg-secondary" style="font-size:0.7rem;">{{ $memo->category->name }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ $memo->staff->name ?? '-' }}</td>
                            <td>
                                @if($memo->title)<strong>{{ $memo->title }}</strong><br>@endif
                                <span class="text-muted">{{ \Str::limit($memo->body, 60) }}</span>
                                @if(preg_match('/https?:\/\/[^\s]+/u', (string) $memo->body))
                                    <i class="fas fa-link text-primary ms-1" title="リンクを含むメモ"></i>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.shared_memos.edit', $memo) }}" class="admin-btn admin-btn-info admin-btn-xs">編集</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @foreach($sharedMemos as $memo)
                <div class="af-modal-overlay" id="sharedMemoModal{{ $memo->id }}" tabindex="-1" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="sharedMemoTitle{{ $memo->id }}">
                    <div class="af-modal af-modal--lg">
                        <div class="af-modal-header">
                            <h5 class="af-modal-title" id="sharedMemoTitle{{ $memo->id }}">共有メモ詳細 #{{ $memo->id }}</h5>
                            <button type="button" class="af-modal-close" data-af-modal-close aria-label="閉じる">&times;</button>
                        </div>
                        <div class="af-modal-body">
                            <table class="af-modal-table">
                                <tbody>
                                    <tr>
                                        <th style="width:140px;">日付</th>
                                        <td>{{ $memo->memo_date->format('Y/m/d') }}</td>
                                    </tr>
                                    <tr>
                                        <th>カテゴリ</th>
                                        <td>{{ $memo->category->name ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>スタッフ</th>
                                        <td>{{ $memo->staff->name ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>タイトル</th>
                                        <td>{{ $memo->title ?: '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>メモ</th>
                                        <td>{!! nl2br(e($memo->body)) !!}</td>
                                    </tr>
                                    <tr>
                                        <th>登録日時</th>
                                        <td>{{ optional($memo->created_at)->format('Y/m/d H:i:s') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="af-modal-footer">
                            <button type="button" class="af-btn af-btn-cancel" data-af-modal-close>閉じる</button>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </section>

    <!-- 新着通知セクション -->
    @if(count($notifications) > 0)
    <section class="mb-4 p-6 bg-white rounded-xl">
        <h3 class="text-lg font-bold mb-4 text-gray-800">新着通知</h3>
        <div class="grid grid-cols-4  gap-3">
            @foreach($notifications as $notification)
                <div class="notification-item p-4 rounded-lg border-l-4 shadow-sm hover:shadow-md transition-shadow duration-200
                    @if($notification['color'] === 'blue') border-blue-500 bg-blue-50 @endif
                    @if($notification['color'] === 'purple') border-purple-500 bg-purple-50 @endif
                    @if($notification['color'] === 'orange') border-orange-500 bg-orange-50 @endif
                    @if($notification['color'] === 'green') border-green-500 bg-green-50 @endif">

                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-sm font-semibold
                                    @if($notification['color'] === 'blue') text-blue-700 @endif
                                    @if($notification['color'] === 'purple') text-purple-700 @endif
                                    @if($notification['color'] === 'orange') text-orange-700 @endif
                                    @if($notification['color'] === 'green') text-green-700 @endif">
                                    {{ $notification['title'] }}
                                </span>
                                <span class="text-xs text-gray-500">{{ $notification['created_at']->format('m/d H:i') }}</span>
                            </div>
                            <p class="text-sm text-gray-700 mb-1">{{ $notification['message'] }}</p>
                            <p class="text-xs text-gray-600">送信者: {{ $notification['sender'] }}</p>
                        </div>

                        <div class="flex gap-2">
                            @if($notification['type'] === 'member_message')
                                <a href="{{ route('admin.messages.show', $notification['id']) }}"
                                   class="text-xs px-2 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors notification-link"
                                   data-notification-id="{{ $notification['id'] }}"
                                   data-notification-type="{{ $notification['type'] }}">
                                    確認
                                </a>
                            @elseif($notification['type'] === 'item_message')
                                <a href="{{ route('admin.message_item.show', $notification['id']) }}"
                                   class="text-xs px-2 py-1 bg-purple-500 text-white rounded hover:bg-purple-600 transition-colors notification-link"
                                   data-notification-id="{{ $notification['id'] }}"
                                   data-notification-type="{{ $notification['type'] }}">
                                    確認
                                </a>
                            @elseif($notification['type'] === 'attendance_request')
                                <a href="{{ route('admin.request_schedules.index') }}"
                                   class="text-xs px-2 py-1 bg-orange-500 text-white rounded hover:bg-orange-600 transition-colors notification-link"
                                   data-notification-id="{{ $notification['id'] }}"
                                   data-notification-type="{{ $notification['type'] }}">
                                    確認
                                </a>
                            @elseif($notification['type'] === 'new_reservation')
                                <a href="{{ route('admin.reservations.index') }}"
                                   class="text-xs px-2 py-1 bg-green-500 text-white rounded hover:bg-green-600 transition-colors notification-link"
                                   data-notification-id="{{ $notification['id'] }}"
                                   data-notification-type="{{ $notification['type'] }}">
                                    確認
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
    @endif

    <section class="contents-explanation mb-4">

    <div class="contents-explanation-section main">
    <div class="contents-explanation-title normal">通常更新管理</div>
    <div class="contents-explanation-flex">
        {{-- <div>
            <h4><a href="{{ route('admin.reservations.index') }}">予約管理</a></h4>
            <p>ウェブからの予約を一覧で表示します。予約の詳細やステータスを確認できます。</p>
        </div> --}}

        <div>
            <h4><a href="{{ route('admin.request_schedules.index') }}">出勤リクエスト</a></h4>
            <p>キャストからの出勤リクエストを確認・承認します。</p>
        </div>

        {{-- <div>
            <h4><a href="{{ route('admin.messages.index') }}">メッセージ（会員）</a></h4>
            <p>会員とのメッセージの送受信を行います。</p>
        </div> --}}

        {{-- <div>
            <h4><a href="{{ route('admin.message_item.index') }}">メッセージ（キャスト）</a></h4>
            <p>キャストとのメッセージの送受信を行います。</p>
        </div> --}}

        {{-- <div>
            <h4><a href="{{ route('admin.members.index') }}">会員管理</a></h4>
            <p>会員情報を一覧で表示します。手入力で会員情報を追加・編集できます。</p>
        </div> --}}

        {{-- <div>
            <h4><a href="{{ route('admin.member_points.index') }}">会員ポイント管理</a></h4>
            <p>会員のポイント情報を一覧で表示します。</p>
        </div> --}}

        {{-- <div>
            <h4><a href="{{ route('admin.histories.index') }}">予約履歴</a></h4>
            <p>ウェブや手入力の予約履歴を一覧で表示します。</p>
        </div> --}}

        {{-- <div>
            <h4><a href="{{ route('admin.contacts.index') }}">お問い合わせ管理</a></h4>
            <p>ウェブからのお問い合わせを一覧で表示します。</p>
        </div> --}}

        {{-- <div>
            <h4><a href="{{ route('admin.newsletter.campaigns.index') }}">メールマガジン管理</a></h4>
            <p>メールマガジンの配信管理を一覧で表示します。</p>
        </div> --}}

        <div>
            <h4><a href="{{ route('admin.items.index') }}">キャスト</a></h4>
            <p>キャストの追加編集、一覧を表示します。</p>
        </div>

        <div>
            <h4><a href="{{ route('admin.schedules.index') }}">日別出勤</a></h4>
            <p>日別出勤情報を一覧で表示します。</p>
        </div>

        <div>
            <h4><a href="{{ route('admin.schedules_month.index') }}">月間出勤</a></h4>
            <p>キャスト別に月間出勤情報を一覧で表示します。</p>
        </div>

        <div>
            <h4><a href="{{ route('admin.special-events.index') }}">特設イベント</a></h4>
            <p>特設イベントの追加編集、一覧を表示します。</p>
        </div>

        <div>
            <h4><a href="{{ route('admin.events.index') }}">イベントバナー</a></h4>
            <p>イベントバナーの追加編集、一覧を表示します。</p>
        </div>

        <div>
            <h4><a href="{{ route('admin.main-visuals.index') }}">メインビジュアル</a></h4>
            <p>メインビジュアルの追加編集、一覧を表示します。</p>
        </div>

        <div>
            <h4><a href="{{ route('admin.news.index') }}">ニュース</a></h4>
            <p>ニュースの追加編集、一覧を表示します。</p>
        </div>

        {{-- <div>
            <h4><a href="{{ route('admin.gallery.index') }}">ギャラリー</a></h4>
            <p>ギャラリーの追加編集、一覧を表示します。</p>
        </div> --}}

        {{-- <div>
            <h4><a href="{{ route('admin.costumes.index') }}">コスチューム</a></h4>
            <p>コスチュームの追加編集、一覧を表示します。</p>
        </div> --}}

        {{-- <div>
            <h4><a href="{{ route('admin.blogs.index') }}">ブログ</a></h4>
            <p>ブログ記事の追加編集、一覧を表示します。</p>
        </div> --}}

        <div>
            <h4><a href="{{ route('admin.ranks.index') }}">ランキング</a></h4>
            <p>ランキング情報を一覧で表示します。</p>
        </div>

        <div>
            <h4><a href="{{ route('admin.pickups.index') }}">ピックアップ</a></h4>
            <p>ピックアップ情報を一覧で表示します。</p>
        </div>

        <div>
            <h4><a href="{{ route('admin.newfaces.index') }}">新人情報</a></h4>
            <p>新人情報を一覧で表示します。</p>
        </div>

        <div>
            <h4><a href="{{ route('admin.links.index') }}">リンク</a></h4>
            <p>外部リンクの追加編集、一覧を表示します。</p>
        </div>
    </div>
    </div>

    <div class="contents-explanation-section sub">
    <div class="contents-explanation-title master">設定管理</div>
    <div class="contents-explanation-flex">
        <div>
            <h4><a href="{{ route('admin.option_cates.index') }}">オプションカテゴリ</a></h4>
            <p>オプションのカテゴリの追加編集、一覧を表示します。</p>
        </div>

        <div>
            <h4><a href="{{ route('admin.options.index') }}">オプション</a></h4>
            <p>オプション（無料）の追加編集、一覧を表示します。</p>
        </div>

        {{-- <div>
            <h4><a href="{{ route('admin.option_pays.index') }}">オプションPAY</a></h4>
            <p>オプション（有料）の追加編集、一覧を表示します。</p>
        </div> --}}

        <div>
            <h4><a href="{{ route('admin.icon_items.index') }}">キャストアイコン</a></h4>
            <p>キャストアイコンの追加編集、一覧を表示します。</p>
        </div>

        <div>
            <h4><a href="{{ route('admin.profiles.index') }}">キャストプロフィール</a></h4>
            <p>キャストページの追加プロフィールの追加編集、一覧を表示します。</p>
        </div>

        <div>
            <h4><a href="{{ route('admin.interviews.index') }}">キャストQ&amp;A</a></h4>
            <p>キャストページのQ&amp;Aを追加編集、一覧を表示します。</p>
        </div>

        <div>
            <h4><a href="{{ route('admin.courses.index') }}">コースカテゴリ</a></h4>
            <p>コースのカテゴリの追加編集、一覧を表示します。</p>
        </div>

        <div>
            <h4><a href="{{ route('admin.courses.index') }}">コース</a></h4>
            <p>コースの追加編集、一覧を表示します。</p>
        </div>

        <div>
            <h4><a href="{{ route('admin.equipment_cates.index') }}">ホテルカテゴリ</a></h4>
            <p>ホテルのカテゴリ（エリア）の追加編集、一覧を表示します。</p>
        </div>

        <div>
            <h4><a href="{{ route('admin.equipments.index') }}">ホテル</a></h4>
            <p>ホテルの追加編集、一覧を表示します。</p>
        </div>

        {{-- <div>
            <h4><a href="{{ route('admin.coupons.index') }}">クーポン</a></h4>
            <p>クーポンの追加編集、一覧を表示します。</p>
        </div> --}}

        {{-- <div>
            <h4><a href="{{ route('admin.mycostumes.index') }}">マイコスチューム</a></h4>
            <p>キャスト独自のマイコスチュームの追加編集、一覧を表示します。</p>
        </div> --}}

        {{-- <div>
            <h4><a href="{{ route('admin.mycostume_cates.index') }}">マイコスチュームカテゴリ</a></h4>
            <p>キャスト独自のマイコスチュームのカテゴリの追加編集、一覧を表示します。</p>
        </div> --}}

        {{-- <div>
            <h4><a href="{{ route('admin.myoptions.index') }}">マイオプション</a></h4>
            <p>キャスト独自のマイオプションの追加編集、一覧を表示します。</p>
        </div> --}}

        {{-- <div>
            <h4><a href="{{ route('admin.myoption_cates.index') }}">マイオプションカテゴリ</a></h4>
            <p>キャスト独自のマイオプションカテゴリの追加編集、一覧を表示します。</p>
        </div> --}}

        <div>
            <h4><a href="{{ route('admin.staff.index') }}">スタッフ管理</a></h4>
            <p>スタッフの追加編集、一覧を表示します。</p>
        </div>

        <div>
            <h4><a href="{{ route('admin.backup.index') }}">バックアップ</a></h4>
            <p>画像、データベースのバックアップを管理します。</p>
        </div>
    </div>
    </div>
    </section>


</section>

<style>
    .url-copy-row {
        display: flex;
        gap: 0.5rem;
        align-items: center;
    }
    .url-copy-row input{
        width:320px;
    }
    .copy-url-btn {
        min-width: 82px;
        padding: 0.35rem 0.65rem;
        border: 1px solid #cbd5e1;
        border-radius: 4px;
        background: #ffffff;
        color: #1f2937;
        font-size: 0.875rem;
        line-height: 1;
        cursor: pointer;
        transition: background-color 0.15s ease, border-color 0.15s ease;
    }
    .copy-url-btn:hover {
        background: #f8fafc;
        border-color: #94a3b8;
    }
    .reservation-table-wrap {
        width: 100%;
        overflow-x: auto;
        overflow-y: hidden;
    }
    .reservation-table-wrap .reservation-list-table {
        min-width: 1220px;
    }
    .reservation-list-table .reservation-kind-col {
        min-width: 96px;
        font-size: 0.92rem;
        font-weight: 600;
    }
    .reservation-list-table .reservation-kind-col p {
        font-size: 0.86rem;
        margin-bottom: 0.15rem;
    }
    .reservation-list-table .admin-btn-group {
        display: flex;
        gap: 0.25rem;
        flex-wrap: wrap;
    }
    .reservation-list-table .admin-btn-group .admin-btn {
        padding: 0.16rem 0.38rem;
        font-size: 0.68rem;
        line-height: 1.05;
        min-height: 22px;
    }
    .reservation-list-table .admin-btn-group .admin-btn i {
        margin-right: 0.15rem;
        font-size: 0.62rem;
    }
    .notification-item {
        position: relative;
    }
    /* ダッシュボード アクセスログサマリー */
    .dashboard-access-log .dal-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.75rem;
    }
    .dashboard-access-log .dal-title {
        font-size: 1rem;
        font-weight: 700;
        margin: 0;
    }
    .dashboard-access-log .dal-placeholder {
        margin: 0;
        padding: 1.5rem 0.25rem 0.5rem;
        font-size: 0.9rem;
        color: #888;
        text-align: center;
    }
    .dashboard-access-log .dal-sub {
        font-size: 0.75rem;
        font-weight: 400;
        color: #888;
    }
    .dashboard-access-log .dal-grid {
        display: grid;
        grid-template-columns: minmax(160px, 0.9fr) 1.3fr 1.3fr;
        gap: 1rem;
        align-items: start;
    }
    .dashboard-access-log .dal-stats {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }
    .dashboard-access-log .dal-stat {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        padding: 0.55rem 0.75rem;
        background: #f7f9fc;
        border-radius: 6px;
    }
    .dashboard-access-log .dal-stat-label {
        font-size: 0.78rem;
        color: #667085;
    }
    .dashboard-access-log .dal-stat-value {
        font-size: 1.4rem;
        font-weight: 700;
        line-height: 1;
        color: #1f2937;
    }
    .dashboard-access-log .dal-stat-value--sm {
        font-size: 1.05rem;
    }
    .dashboard-access-log .dal-block-title {
        font-size: 0.82rem;
        font-weight: 700;
        color: #475467;
        margin: 0 0 0.4rem;
    }
    .dashboard-access-log .dal-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.78rem;
    }
    .dashboard-access-log .dal-table th {
        text-align: left;
        font-weight: 600;
        color: #98a2b3;
        border-bottom: 1px solid #eaecf0;
        padding: 0.25rem 0.4rem;
    }
    .dashboard-access-log .dal-table td {
        padding: 0.28rem 0.4rem;
        border-bottom: 1px solid #f2f4f7;
    }
    .dashboard-access-log .dal-table .dal-num {
        text-align: right;
        white-space: nowrap;
        font-variant-numeric: tabular-nums;
    }
    .dashboard-access-log .dal-host {
        word-break: break-all;
    }
    .dashboard-access-log .dal-unit {
        font-size: 0.65rem;
        color: #98a2b3;
        margin-left: 0.15rem;
    }
    .dashboard-access-log .dal-empty {
        color: #98a2b3;
        padding: 0.6rem 0.4rem;
    }
    @media (max-width: 900px) {
        .dashboard-access-log .dal-grid {
            grid-template-columns: 1fr;
        }
        .dashboard-access-log .dal-stats {
            flex-direction: row;
            flex-wrap: wrap;
        }
        .dashboard-access-log .dal-stat {
            flex: 1;
            min-width: 100px;
        }
    }
    .contents-explanation{
        position: relative;
        padding: 1rem;
        background: #ececec;
        border-radius: 4px;
    }
    .contents-explanation h3{
        margin-bottom:1.5rem;
    }
    .contents-explanation .contents-explanation-section{
        width:100%;
        padding: 0.5rem;
        margin-bottom: 1rem ;
        box-sizing: border-box;
        border-radius: 1px;
    }
    .contents-explanation .contents-explanation-section a::before{
        content: "\00BB";
        margin-right: 0.25rem;
    }
    .contents-explanation .contents-explanation-section .contents-explanation-flex{
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-start;
        align-items: stretch;
        gap: 0.5rem;
        width:100%;
        box-sizing: border-box;
    }
    .contents-explanation .contents-explanation-section a{
        text-decoration: none;
    }
    .contents-explanation .contents-explanation-section a:hover{
        text-decoration: underline;
    }
    .contents-explanation .contents-explanation-section.main{
        background: #fddce6;
    }
    .contents-explanation .contents-explanation-section.main a{
        color: #eb2e2e;
    }
    .contents-explanation .contents-explanation-section.sub{
        background: #dcefff;
    }
    .contents-explanation .contents-explanation-section.sub a{
        color: #2081e2;
    }
    .contents-explanation .contents-explanation-section .contents-explanation-flex > div{
        width: calc((100% - 1.8rem) / 4);
        /* min-width: 260px; */
        padding: 1rem;
        background:rgba(255, 255, 255, 0.85);
        border-radius: 2px;
        box-sizing: border-box;
    }
    .contents-explanation .contents-explanation-section .contents-explanation-flex > div:hover{
        background:rgba(255, 255, 255, 1);
    }
    .contents-explanation .contents-explanation-title{
        margin-bottom: 0.75rem;
        font-size: 1rem;
        font-weight: 600;
    }
    .contents-explanation .contents-explanation-title.normal{
        color: #eb2e2e;
    }
    .contents-explanation .contents-explanation-title.master{
        color: #2081e2;
    }
    .contents-explanation div h4{
        margin-bottom:0.5rem;
        font-size: 0.875rem;
        font-weight: 600;
    }
    .contents-explanation div p{
        margin-top: 0.5rem;
        font-size: 0.75rem;
    }
</style>

<script>
function changeColor(selectObj) {
    let color = "";
    switch (selectObj.value) {
        case "0":
            color = "#f0f0f0";
            break;
        case "1":
            color = "#ffe2e2";
            break;
        case "2":
            color = "#d5ffd5";
            break;
        case "3":
            color = "#eeeeff";
            break;
        case "4":
            color = "#fffac6";
            break;
        default:
            color = "#ffffff";
    }
    selectObj.style.backgroundColor = color;
}

document.addEventListener('DOMContentLoaded', function() {
    // URLコピーボタン
    document.querySelectorAll('.copy-url-btn').forEach(function(button) {
        button.addEventListener('click', async function() {
            const targetId = this.getAttribute('data-copy-target');
            const input = targetId ? document.getElementById(targetId) : null;
            if (!input) {
                return;
            }

            const text = input.value;
            try {
                await navigator.clipboard.writeText(text);
            } catch (error) {
                input.focus();
                input.select();
                const copied = document.execCommand('copy');
                if (!copied) {
                    console.error('Error copying URL:', error);
                    return;
                }
            }

            const originalText = this.textContent;
            this.textContent = 'コピー済み';
            this.disabled = true;
            setTimeout(() => {
                this.textContent = originalText;
                this.disabled = false;
            }, 1200);
        });
    });

    // 予約ステータス更新
    document.querySelectorAll('.flag-select').forEach(function(select) {
        changeColor(select);
        select.addEventListener('change', function() {
            const reservationId = this.getAttribute('data-reservation-id');
            const newFlag = this.value;

            fetch('/kanrigamen/reservation/update-flag', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    reservation_id: reservationId,
                    flag: newFlag
                })
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    alert('ステータスの更新に失敗しました');
                }
            })
            .catch(() => {
                alert('エラーが発生しました');
            });
        });
    });

    // 通知リンクのクリックイベント
    document.querySelectorAll('.notification-link').forEach(function(link) {
        link.addEventListener('click', function(e) {
            const notificationId = this.getAttribute('data-notification-id');
            if (notificationId) {
                // 通知を既読にする
                fetch('{{ route("admin.dashboard.mark-read") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        notification_id: notificationId,
                        notification_type: this.getAttribute('data-notification-type')
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // 通知アイテムをフェードアウト
                        const notificationItem = this.closest('.notification-item');
                        if (notificationItem) {
                            notificationItem.style.transition = 'opacity 0.3s ease';
                            notificationItem.style.opacity = '0';
                            setTimeout(() => {
                                notificationItem.remove();
                            }, 300);
                        }
                    }
                })
                .catch(error => {
                    console.error('Error marking notification as read:', error);
                });
            }
        });
    });
});
</script>

</x-admin-layout>
