<x-member-layout>
    <x-slot name="header">
    @php
        $now = date('Y-m-d H:i:s');
    @endphp
HOME
    </x-slot>


    <!-- 新着メッセージ通知エリア -->
    @if($unreadMessagesCount > 0)
    <section class="mb-8 px-4">
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 shadow-md">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-envelope text-blue-600 text-xl mr-3"></i>
                    <div>
                        <h3 class="text-lg font-semibold text-blue-800">
                            新着メッセージがあります
                        </h3>
                        <p class="text-blue-600 text-sm">
                            管理者からの未読メッセージが {{ $unreadMessagesCount }} 件あります
                        </p>
                    </div>
                </div>
                <a href="{{ route('member.messages.index') }}"
                   class="af-btn af-btn-primary">
                    メッセージを確認
                </a>
            </div>
        </div>
    </section>
    @endif

    <!-- ファンクラブ返信の新着通知エリア -->
    @if(($unreadFanclubReplyCount ?? 0) > 0)
    <section class="mb-8 px-4">
        <div class="bg-purple-50 border border-purple-200 rounded-lg p-6 shadow-md">
            <div class="flex items-center justify-between flex-wrap gap-3">
                <div class="flex items-center">
                    <i class="fas fa-reply text-purple-600 text-xl mr-3"></i>
                    <div>
                        <h3 class="text-lg font-semibold text-purple-800">
                            ファンクラブの新着メッセージがあります
                        </h3>
                        <p class="text-purple-600 text-sm">
                            キャストからの未読返信が {{ $unreadFanclubReplyCount }} 件あります
                        </p>
                    </div>
                </div>
            </div>

            <div class="mt-4 space-y-2">
                @foreach($unreadFanclubReplies as $reply)
                    <a href="{{ route('member.fanclubs.messages.index', $reply->fanclub_id) }}"
                       class="block rounded-lg border border-purple-100 bg-white px-3 py-2 hover:bg-purple-50">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-sm font-medium text-gray-800">
                                {{ $reply->fanclub->title ?? 'ファンクラブ' }}
                            </span>
                            <span class="text-xs text-gray-500">{{ $reply->created_at->format('Y/m/d H:i') }}</span>
                        </div>
                        <p class="text-sm text-gray-600 mt-1">{{ \Str::limit($reply->message, 80) }}</p>
                    </a>
                @endforeach
            </div>
            <p class="text-xs text-gray-500 mt-3">※メッセージ画面を開くと既読になり、この表示は消えます。</p>
        </div>
    </section>
    @endif

    <!-- 予約エリア -->
    <section class="mb-8 px-4">
        <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-calendar-alt text-green-500 mr-2"></i>
                ご予約情報
            </h3>

            @if($reservations->count() > 0)
                <div class="space-y-4">
                    @foreach ($reservations as $reservation)
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
                                    <span class="text-sm font-medium text-gray-600 w-24">キャスト:</span>
                                    <span class="text-sm">{{ $reservation->item_name }}</span>
                                </div>
                                @if( $reservation->item_name_other )
                                <div class="flex items-center">
                                    <span class="text-sm font-medium text-gray-600 w-24">キャストその他:</span>
                                    <span class="text-sm">{{ $reservation->item_name_other }}</span>
                                </div>
                                @endif
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
                                    <span class="text-sm font-semibold text-gray-900">{{ number_format($reservation->fee_total * config('tax.intax')) }} 円</span>
                                </div>

                                <div class="flex items-center">
                                    <span class="text-sm font-medium text-gray-600 w-24">ステータス:</span>
                                    <span class="text-sm px-2 py-1 rounded-full text-xs font-medium
                                        @if($reservation->flag == 0) bg-yellow-100 text-yellow-800
                                        @elseif($reservation->flag == 1) bg-blue-100 text-blue-800
                                        @elseif($reservation->flag == 2) bg-green-100 text-green-800
                                        @elseif($reservation->flag == 3) bg-gray-100 text-gray-800
                                        @elseif($reservation->flag == 4) bg-gray-100 text-gray-800
                                        @endif">
                                        @if($reservation->flag == 0) 受付処理中
                                        @elseif($reservation->flag == 1) 確認中
                                        @elseif($reservation->flag == 2) 確定
                                        @elseif($reservation->flag == 3) 保留
                                        @elseif($reservation->flag == 4) キャンセル
                                        @endif
                                    </span>
                                </div>

                                <!-- スタジオ情報 -->
                                @if($reservation->equipment_id)
                                <div class="flex items-center">
                                    <span class="text-sm font-medium text-gray-600 w-24">スタジオ:</span>
                                    <span class="text-sm font-semibold text-gray-900">
                                    @foreach($equipments as $equipment)
                                        @php
                                            $equipmentId = $equipment->external_id ?? $equipment->id;
                                        @endphp
                                        @if($equipmentId == $reservation->equipment_id)
                                            {{ $equipment->title }}
                                        @endif
                                    @endforeach
                                    </span>
                                </div>
                                @endif

                            </div>
                        </div>

                        <!-- オプション情報 -->
                        @if(!empty($reservation->option))
                        <div class="mt-3 pt-3 border-t border-gray-200">
                            <div class="flex items-center">
                                <span class="text-sm font-medium text-gray-600 w-24">オプション:</span>
                                <div class="flex flex-wrap gap-1">
                                    @php
                                        // 文字列ならjson_decode、配列ならそのまま使う
                                        $option_ar = is_string($reservation->option)
                                            ? json_decode($reservation->option, true)
                                            : ($reservation->option ?? []);
                                    @endphp

                                    @foreach($options as $option)
                                        @foreach($option_ar as $res_option)
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
                    <i class="fas fa-calendar-times text-4xl mb-4"></i>
                    <p>現在予約はありません</p>
                </div>
            @endif
        </div>
    </section>
    <!-- ポイントエリア -->
    <section class="mb-8 px-4">
        <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-coins text-yellow-500 mr-2"></i>
                ポイント情報
            </h3>

            <div class="flex items-center justify-between flex-wrap">
                <div class="flex items-center">
                    <span class="text-gray-600 mr-2">現在のポイント</span>
                    <span class="text-3xl font-bold text-blue-600">{{ number_format($member->point) }}</span>
                    <span class="text-gray-600 ml-1">ポイント</span>
                </div>

                <div class="text-sm text-gray-500">
                    最終更新日: {{ $member->updated_at->format('Y/m/d') }}
                </div>
            </div>

            <div class="mt-4">
                <a href="{{ route('member.point-history') }}" class="af-btn af-btn-cancel af-btn-sm">
                    ポイント履歴 〉
                </a>
            </div>
            <div class="mt-6 p-4 bg-gray-100 rouded-sm">
                <p class="mb-4">
                <strong>■ポイントのご利用について</strong><br>
                ポイントの貯まり方<br>
                ポイントはご利用60分につき1ポイントが貯まります。
                </p>
                <p class="mb-4">
                <strong>■ポイントの使い方</strong><br>
                ポイントのご利用はご利用お申し込み時お伝えください。 ポイントは1回のご利用につき1回までご利用いただけます。
                </p>
                <p>
                <strong>■ポイント特典</strong><br>
                40pt 延長15分サービス<br>
                ※動画オプションは適用外となります。<br>
                20pt 1,000円割引<br>
                10pt オプションサービス
                </p>
            </div>


        </div>
    </section>
    <!-- ポイントエリア -->

    {{--
    <section class="mb-8 px-4">
        <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
        <h3 class="font-semibold mb-4">カバーフォトについて</h3>
            当ウェブサイトのトップに掲載される月刊コンテンツのカバーフォトを募集しております。<br>
            画像をお送りいただく際にはこちらのメールアドレスよりお送りくださいませ。<br>
            応募の際には【①お名前】、【②PC、スマホの種別】をご記載ください。お名前はハンドルネームで掲載させて頂いております。<br>
            <a href="mailto:{{ $shops->shop_mail }}?subject=カバーフォト応募" class="inline-block w-44 py-2 px-8 my-4 rounded-xl bg-blue-300 hover:bg-blue-500 text-white">メール送信</a>
            <p>
推奨サイズ（ピクセル）：PC　横1920ｘ縦1080 / スマホ　横1080ｘ縦1920 <br>
ファイルサイズは2メガバイト以内に圧縮してください。<br>
<span class="text-sm">（公開にあたり運営側でサイズを調整する場合もございますのでご了承ください）</span>
            </p>
        </div>
    </section>
    --}}

</x-member-layout>
