<x-item-layout>
    <x-slot name="header">
        ご予約履歴
    </x-slot>

    <!-- 検索フォームエリア -->
    <section class="mb-8">
        <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-search text-blue-500 mr-2"></i>
                検索条件
            </h3>
            
            <form method="GET" action="{{ route('item.reservation-history') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- 日付範囲 -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">開始日</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">終了日</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <!-- ステータス -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">ステータス</label>
                        <select name="flag" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">すべて</option>
                            <option value="0" {{ request('flag') == '0' ? 'selected' : '' }}>受付処理中</option>
                            <option value="1" {{ request('flag') == '1' ? 'selected' : '' }}>確認中</option>
                            <option value="2" {{ request('flag') == '2' ? 'selected' : '' }}>確定</option>
                            <option value="3" {{ request('flag') == '3' ? 'selected' : '' }}>保留</option>
                            <option value="4" {{ request('flag') == '4' ? 'selected' : '' }}>キャンセル</option>
                        </select>
                    </div>
                    
                    <!-- ボタン -->
                    <div class="af-btn-row">
                        <button type="submit" class="af-btn af-btn-search">検索</button>
                        <a href="{{ route('item.reservation-history') }}" class="af-btn af-btn-reset">リセット</a>
                    </div>
                </div>
            </form>
        </div>
    </section>

    <!-- 予約履歴エリア -->
    <section>
        <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-history text-blue-500 mr-2"></i>
                ご予約履歴
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
                                    <span class="text-sm font-semibold text-gray-900">{{ number_format($reservation->fee_total) }} 円</span>
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
                                    <span class="text-sm">
                                    @foreach($equipments as $equipment)                                  
                                        @if($equipment->id == $reservation->equipment_id)
                                            {{ $equipment->title }}
                                        @endif
                                    @endforeach</span>
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
                        
                        <!-- ショップメモ -->
                        @if($reservation->memo_share)
                        <div class="mt-3 pt-3 border-t border-gray-200">
                            <div class="flex items-start">
                                <span class="text-sm font-medium text-gray-600 w-24 mt-1">メモ:</span>
                                <span class="text-sm text-gray-900 flex-1">{{ $reservation->memo_share }}</span>
                            </div>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
                
                <!-- ページネーション -->
                <div class="mt-6">
                    {{ $reservations->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-history text-4xl mb-4"></i>
                    <p>ご予約履歴はありません</p>
                </div>
            @endif
        </div>
    </section>
</x-item-layout> 