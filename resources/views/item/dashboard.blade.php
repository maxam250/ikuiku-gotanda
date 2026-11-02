<x-item-layout>
    <x-slot name="header">
        ホーム
    </x-slot>

    {{-- 新着会員メッセージ通知（リアルタイムメッセージ上部） --}}
    {{--
    @if(($fanclubUnreadCount ?? 0) > 0)
    <section class="mb-4">
        <div class="rounded-lg border border-amber-300 bg-amber-50 px-4 py-3 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <div class="flex items-center gap-2">
                    <i class="fas fa-envelope text-amber-600"></i>
                    <span class="font-semibold text-amber-800">会員からの新着メッセージが {{ $fanclubUnreadCount }} 件あります</span>
                </div>
                <a href="{{ route('item.fanclub.messages.member-list') }}" class="af-btn af-btn-primary af-btn-sm">確認する</a>
            </div>
            <div class="mt-2 space-y-1">
                @foreach($fanclubLatestMessages->where('is_read', false)->take(3) as $msg)
                <a href="{{ route('item.fanclub.messages.show', $msg) }}"
                   class="block rounded-lg border border-amber-100 bg-white px-3 py-1.5 text-sm hover:bg-amber-50">
                    <div class="flex items-center justify-between gap-2">
                        <span class="font-medium text-gray-800">{{ $msg->member->nickname ?? '-' }}</span>
                        <span class="text-xs text-gray-500">{{ $msg->created_at->format('m/d H:i') }}</span>
                    </div>
                    <p class="truncate text-gray-600">{{ \Str::limit($msg->message, 60) }}</p>
                </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif
    --}}
    <!-- リアルタイムメッセージエリア -->
    {{--
    <section class="mb-8">
        <div class="max-w-full sm:max-w-7xl mx-auto px-1 sm:px-6 lg:px-8 pb-8 mt-12">
            <form method="POST" action="{{ route('item.updateRealtimeComment', $item->id) }}">
                @csrf
                @method('patch')

                <div class="bg-gradient-to-br from-pink-100 via-purple-50 to-indigo-100 rounded-lg py-4 px-2 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 opacity-10">
                        <svg viewBox="0 0 100 100" class="w-full h-full fill-current text-pink-300">
                            <path d="M50 20c16.569 0 30 13.431 30 30s-13.431 30-30 30-30-13.431-30-30S33.431 20 50 20zm0 45c8.284 0 15-6.716 15-15s-6.716-15-15-15-15 6.716-15 15 6.716 15 15 15z"/>
                        </svg>
                    </div>

                    <div class="relative">
                        <div class="flex items-center mb-4">
                            <div class="w-3 h-3 bg-pink-400 rounded-full mr-2 animate-pulse"></div>
                            <h3 class="text-xl font-bold text-gray-900">リアルタイムメッセージ</h3>
                            <i class="fas fa-heart text-pink-400 ml-2 animate-pulse"></i>
                        </div>

                        <div class="bg-white/80 backdrop-blur-sm rounded-xl p-1 mb-3">
                            <textarea id="item_p_comment" name="item_p_comment" class="w-full border-0 text-gray-900 min-h-64 leading-relaxed h_8rem">{{ old('item_p_comment', $item->item_p_comment) }}</textarea>
                        </div>

                        @if($item->item_p_comment_date)
                        <p class="text-sm text-gray-600 flex items-center">
                            最終更新日：<i class="far fa-clock mr-1"></i>
                            {{ $item->item_p_comment_date }}
                        </p>
                        @endif
                    </div>
                    <button type="submit" class="af-btn af-btn-primary">更新する</button>
                </div>
            </form>
        </div>
    </section>
    --}}

    <!-- ファンクラブ概要 -->
    {{--
    <section class="mb-8">
        <div class="rounded-xl border border-yellow-200 bg-yellow-50 p-6 shadow-sm">
            <div class="mb-5 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <h3 class="flex items-center text-lg font-semibold text-gray-800">
                    <i class="fas fa-users text-purple-500 mr-2"></i>
                    ファンクラブ概要
                </h3>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('item.fanclub.edit') }}" class="af-btn af-btn-cancel af-btn-sm">設定</a>
                    <a href="{{ route('item.fanclub.messages.member-list') }}" class="af-btn af-btn-primary af-btn-sm">会員メッセージ一覧</a>
                </div>
            </div>

            @if($fanclub)
                <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-3">
                    <div class="rounded-xl border border-purple-100 bg-white p-4 shadow-sm">
                        <p class="mb-1 text-xs tracking-wide text-gray-500">ファンクラブ名</p>
                        <p class="line-clamp-2 text-lg font-bold text-gray-900">{{ $fanclub->title }}</p>
                    </div>
                    <div class="rounded-xl border border-purple-100 bg-white p-4 shadow-sm">
                        <p class="mb-1 text-xs tracking-wide text-gray-500">チャンネル数</p>
                        <p class="text-2xl font-bold text-purple-700">{{ $fanclubChannelCount }}</p>
                    </div>
                    <div class="rounded-xl border border-purple-100 bg-white p-4 shadow-sm">
                        <p class="mb-1 text-xs tracking-wide text-gray-500">ポスト投稿数</p>
                        <p class="text-2xl font-bold text-pink-700">{{ $fanclubPostCount }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <div>
                        <div class="mb-3 flex items-center justify-between">
                            <h4 class="font-semibold text-gray-800">
                                登録者
                                <span class="ml-1 inline-flex rounded-full bg-purple-100 px-2 py-0.5 text-xs text-purple-700">{{ $fanclubMembersCount }}名</span>
                            </h4>
                            <a href="{{ route('item.fanclub.members') }}" class="text-sm text-blue-600 hover:underline">一覧を見る</a>
                        </div>
                        @if($fanclubMembers->count() > 0)
                            <div class="max-h-80 space-y-2 overflow-auto pr-1">
                                @foreach($fanclubMembers as $membership)
                                    <div class="flex items-center justify-between rounded-xl border border-gray-100 bg-white p-3 shadow-sm">
                                        <span class="font-medium text-gray-900">{{ $membership->member->nickname ?? '-' }}</span>
                                        <span class="text-xs text-gray-500">
                                            登録日 {{ optional($membership->started_at ?? $membership->created_at)->format('Y/m/d') }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-500">登録者はいません。</p>
                        @endif
                    </div>

                    <div>
                        <div class="mb-3 flex items-center justify-between">
                            <h4 class="font-semibold text-gray-800">
                                会員メッセージ新着
                                @if(($fanclubUnreadCount ?? 0) > 0)
                                    <span class="ml-1 inline-flex rounded-full bg-red-100 px-2 py-0.5 text-xs text-red-700">
                                        NEW {{ $fanclubUnreadCount }}
                                    </span>
                                @endif
                            </h4>
                            <a href="{{ route('item.fanclub.messages.member-list') }}" class="text-sm text-blue-600 hover:underline">一覧を見る</a>
                        </div>
                        @if($fanclubLatestMessages->count() > 0)
                            <div class="max-h-80 space-y-2 overflow-auto pr-1">
                                @foreach($fanclubLatestMessages as $message)
                                    <a href="{{ route('item.fanclub.messages.show', $message) }}" class="block rounded-xl border border-gray-100 bg-white p-3 shadow-sm transition hover:-translate-y-0.5 hover:border-purple-200">
                                        <div class="mb-1 flex items-center justify-between">
                                            <span class="font-medium text-gray-900">{{ $message->member->nickname ?? '-' }}</span>
                                            <span class="text-xs text-gray-500">{{ $message->created_at->format('m/d H:i') }}</span>
                                        </div>
                                        @if(!$message->is_read)
                                            <span class="mb-1 inline-flex rounded-full bg-red-100 px-2 py-0.5 text-xs text-red-700">NEW</span>
                                        @endif
                                        <p class="text-sm leading-relaxed text-gray-700">{{ \Str::limit($message->message, 80) }}</p>
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-500">新着メッセージはありません。</p>
                        @endif
                    </div>
                </div>
            @else
                <div class="rounded-xl border border-dashed border-gray-300 bg-white p-6 text-center">
                    <p class="mb-3 text-sm text-gray-600">ファンクラブは未開設です。</p>
                    <a href="{{ route('item.fanclub.edit') }}" class="af-btn af-btn-primary af-btn-sm">ファンクラブを開設する</a>
                </div>
            @endif
        </div>
    </section>
    --}}
    <!-- 統計情報エリア -->
    {{--
    <section class="mb-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- 前月の予約数 -->
            <div class="bg-white rounded-lg p-6 border border-gray-200">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-gray-100 text-gray-600">
                        <i class="fas fa-calendar-alt text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">前月の予約数</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $lastMonthReservations }}件</p>
                    </div>
                </div>
            </div>

            <!-- 今月の予約数 -->
            <div class="bg-white rounded-lg p-6 border border-gray-200">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <i class="fas fa-calendar text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">今月の予約数</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $currentMonthReservations }}件</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    --}}
    <!-- 最近の予約エリア -->
    {{--
    <section>
        <div class="bg-white rounded-lg p-6 border border-gray-200">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-history text-blue-500 mr-2"></i>
                    予約リスト
                </h3>
                <a href="{{ route('item.reservation-history') }}" class="af-btn af-btn-cancel af-btn-sm">
                    過去の履歴を見る →
                </a>
            </div>

            @if($recentReservations->count() > 0)
                <div class="space-y-4">
                    @foreach ($recentReservations as $reservation)
                    <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- 基本情報 -->
                            <div class="space-y-2">

                                <div class="flex items-center">
                                    <span class="text-sm font-medium text-gray-600 w-24">種別:</span>
                                    <span class="text-sm">{{ $reservation->kind == 1 ? '通常予約' : 'リクエスト予約' }}</span>
                                </div>

                                <div class="flex items-center">
                                    <span class="text-sm font-medium text-gray-600 w-24">日時:</span>
                                    <span class="text-sm">{{ $reservation->date->format('Y/m/d').'('. $reservation->date->isoFormat('ddd'). ')' }}</span>
                                </div>

                                <div class="flex items-center">
                                    <span class="text-sm font-medium text-gray-600 w-24">時間:</span>
                                    <span class="text-sm">
                                        {{ $reservation->time_start ? $reservation->time_start->format('g:i') : ''}} -
                                        {{ $reservation->time_end ? $reservation->time_end->format('g:i') : '' }}
                                    </span>
                                </div>

                                <div class="flex items-center">
                                    <span class="text-sm font-medium text-gray-600 w-24">お客様:</span>
                                    <span class="text-sm">{{ $reservation->name }}{{ $reservation->nickname ? '('.$reservation->nickname.')' : '' }}</span>
                                </div>
                            </div>

                            <!-- 詳細情報 -->
                            <div class="space-y-2">
                                <div class="flex items-center">
                                    <span class="text-sm font-medium text-gray-600 w-24">コース:</span>
                                    <span class="text-sm">
                                        @foreach($courses as $course)
                                            {{ $course->id == $reservation->course_id ? $course->title : '' }}
                                        @endforeach
                                        @foreach($course_fees as $course_fee)
                                            {{ $course_fee->id == $reservation->course_time ? $course_fee->course : '' }}
                                        @endforeach
                                        ({{ $reservation->course_time }}分)
                                    </span>
                                </div>

                                <div class="flex items-center">
                                    <span class="text-sm font-medium text-gray-600 w-24">料金:</span>
                                    <span class="text-sm font-semibold text-gray-900">{{ $reservation->fee_total }} 円</span>
                                </div>

                                <div class="flex items-center">
                                    <span class="text-sm font-medium text-gray-600 w-24">ステータス:</span>
                                    <span class="text-sm px-2 py-1 rounded-full text-xs font-medium
                                        @if($reservation->flag == 0) bg-yellow-100 text-yellow-800
                                        @elseif($reservation->flag == 1) bg-red-500 text-white
                                        @elseif($reservation->flag == 2) bg-green-100 text-green-800
                                        @elseif($reservation->flag == 3) bg-gray-100 text-gray-800
                                        @elseif($reservation->flag == 4) bg-red-100 text-red-800
                                        @endif">
                                        @if($reservation->flag == 0) 受付処理中
                                        @elseif($reservation->flag == 1) お返事待ち
                                        @elseif($reservation->flag == 2) 確定
                                        @elseif($reservation->flag == 3) 保留中
                                        @elseif($reservation->flag == 4) キャンセル
                                        @endif
                                    </span>
                                </div>

                                <div class="flex items-center">
                                    <span class="text-sm font-medium text-gray-600 w-24">スタジオ:</span>
                                    <span class="text-sm px-2 py-1 rounded-full font-medium">
                                    @foreach($equipments as $equipment)
                                        @if($equipment->id == $reservation->equipment_id)
                                            {{ $equipment->title }}
                                        @endif
                                    @endforeach
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- オプション情報 -->
                        @if(!empty($reservation->option))
                        <div class="mt-3 pt-3 border-t border-gray-200">
                            <div class="flex items-center">
                                <span class="text-sm font-medium text-gray-600 w-24">オプション:</span>
                                <div class="flex flex-wrap gap-1">
                                    @php
                                        $option_ar = json_decode($reservation->option, true);
                                    @endphp
                                    @foreach($options as $option)
                                        @foreach($option_ar ?? [] as $res_option)
                                            @if($option->id == $res_option)
                                                <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">{{ $option->title }}</span>
                                            @endif
                                        @endforeach
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- マイオプション情報 -->
                        @if(!empty($reservation->myoption))
                        <div class="mt-3 pt-3 border-t border-gray-200">
                            <div class="flex items-center">
                                <span class="text-sm font-medium text-gray-600 w-24">マイオプ:</span>
                                <div class="flex flex-wrap gap-1">
                                    @php
                                        $myoption_ar = json_decode($reservation->myoption, true);
                                    @endphp
                                    @foreach($items as $item)
                                        @if($item->id == $reservation->item_id)
                                            @foreach($item->myOptions as $myOption)
                                                @if(in_array($myOption->id, $myoption_ar ?? []))
                                                    <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">{{ $myOption->name }}</span>
                                                @endif
                                            @endforeach
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- マイコスチューム情報 -->
                        @if(!empty($reservation->mycostume))
                        <div class="mt-3 pt-3 border-t border-gray-200">
                            <div class="flex items-center">
                                <span class="text-sm font-medium text-gray-600 w-24">マイコス:</span>
                                <div class="flex flex-wrap gap-1">
                                    @php
                                        $mycostume_ar = json_decode($reservation->mycostume, true);
                                    @endphp
                                    @foreach($items as $item)
                                        @if($item->id == $reservation->item_id)
                                            @foreach($item->myCostumes as $myCostume)
                                                @if(in_array($myCostume->id, $mycostume_ar ?? []))
                                                    <span class="text-xs bg-purple-100 text-purple-800 px-2 py-1 rounded">{{ $myCostume->name }}</span>
                                                @endif
                                            @endforeach
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- コスチューム情報 -->
                        @if(!empty($reservation->costume))
                        <div class="mt-3 pt-3 border-t border-gray-200">
                            <div class="flex items-center">
                                <span class="text-sm font-medium text-gray-600 w-24">コスチューム:</span>
                                <div class="flex flex-wrap gap-1">
                                    @php
                                        $costume_ar = json_decode($reservation->costume, true);
                                    @endphp
                                    @foreach($costumes as $costume)
                                        @foreach($costume_ar ?? [] as $res_costume)
                                            @if($costume->id == $res_costume)
                                                <span class="text-xs bg-pink-100 text-pink-800 px-2 py-1 rounded">{{ $costume->name }}</span>
                                            @endif
                                        @endforeach
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endif


                        <!-- エリア情報 -->
                        @if(!empty($reservation->area))
                        <div class="mt-3 pt-3 border-t border-gray-200">
                            <div class="flex items-center">
                                <span class="text-sm font-medium text-gray-600 w-24">利用エリア:</span>
                                <span class="text-sm">{{ $reservation->area }}</span>
                            </div>
                        </div>
                        @endif

                        @if(!empty($reservation->place))
                        <div class="mt-3 pt-3 border-t border-gray-200">
                            <div class="flex items-center">
                                <span class="text-sm font-medium text-gray-600 w-24">具体的な場所:</span>
                                <span class="text-sm">{{ $reservation->place }}</span>
                            </div>
                        </div>
                        @endif

                        @if(!empty($reservation->address))
                        <div class="mt-3 pt-3 border-t border-gray-200">
                            <div class="flex items-center">
                                <span class="text-sm font-medium text-gray-600 w-24">住所:</span>
                                <span class="text-sm">{{ $reservation->address }}</span>
                            </div>
                        </div>
                        @endif

                        @if(!empty($reservation->meet))
                        <div class="mt-3 pt-3 border-t border-gray-200">
                            <div class="flex items-center">
                                <span class="text-sm font-medium text-gray-600 w-24">待ち合わせ方法:</span>
                                <span class="text-sm">{{ $reservation->meet }}</span>
                            </div>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-calendar text-4xl mb-4"></i>
                    <p>最近の予約はありません</p>
                </div>
            @endif
        </div>
    </section>
    --}}
    <script src="{{ asset('js/tinymce/tinymce.min.js') }}"></script>
    <script src="{{ asset('js/admin-tinymce.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            initAdminTinyMCE(
                '#item_p_comment',
                '{{ asset("js/tinymce/skins/ui/oxide") }}',
                '{{ asset("js/tinymce/skins/content/default/content.min.css") }}',
                { height: 320 }
            );

            // フォーム送信時にTinyMCEの内容をtextareaに同期
            const form = document.querySelector('form[action*="updateRealtimeComment"]');
            if (form && typeof tinymce !== 'undefined') {
                form.addEventListener('submit', function() {
                    const editor = tinymce.get('item_p_comment');
                    if (editor) {
                        editor.save();
                    }
                });
            }
        });
    </script>
</x-item-layout>
