<x-member-layout>
    <x-slot name="header">
        利用履歴
    </x-slot>

    <!-- 利用履歴エリア -->
    <section>
        <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-history text-blue-500 mr-2"></i>
                利用履歴
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
                                @if($reservation->item_name_other )
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
                                        @elseif($reservation->flag == 4) bg-red-100 text-red-800
                                        @endif">
                                        @if($reservation->flag == 0) 受付処理中
                                        @elseif($reservation->flag == 1) 予定確認中
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
                                    @foreach($options as $option)
                                        @foreach($reservation->option as $res_option)
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
                                <span class="text-sm font-medium text-gray-600 w-24">マイコスチューム:</span>
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
            @else
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-history text-4xl mb-4"></i>
                    <p>利用履歴はありません</p>
                </div>
            @endif
        </div>
    </section>
</x-member-layout>
