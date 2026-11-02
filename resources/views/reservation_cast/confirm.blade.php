@extends('layouts.app')

@section('content')

<div class="titleArea">
    <h2>RESERVATION</h2>
    <div class="title">ご予約内容の確認</div>
</div>

<div class="min-h-screen bg-gray-50">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- タイトルとフォームのコンテナ -->
        <div class="flex flex-col lg:flex-row lg:gap-8">
            <!-- タイトルセクション -->
            <div class="w-full lg:w-1/3 mb-8 lg:mb-0">
                <div class="bg-white rounded-lg shadow-md p-6 lg:p-8">
                    <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 mb-4">RESERVATION</h1>
                    <h2 class="text-xl lg:text-2xl font-semibold text-gray-800 mb-4">ご予約内容の確認</h2>
                </div>
            </div>

            <!-- 確認フォームセクション -->
            <div class="w-full lg:w-2/3">
                <div class="bg-white rounded-lg shadow-md p-6 lg:p-8">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">予約内容確認</h2>
                    
                    <form action="{{ route('reservation.store') }}" method="POST">
                        @csrf

                        <div class="space-y-6">
                            <div class="flex flex-col md:flex-row md:items-center gap-4 p-4 bg-gray-50 rounded-lg">
                                <div class="font-medium text-gray-700 min-w-[120px] bg-pink-50 px-3 py-2 rounded-lg">予約形態:</div>
                                <div class="text-gray-900">{{ $confirms['kind'] == 1 ? '通常予約' : 'リクエスト予約'}}</div>
                                <input type="hidden" name="kind" value="{{ $confirms['kind'] }}">
                            </div>

                            <div class="flex flex-col md:flex-row md:items-center gap-4 p-4 bg-gray-50 rounded-lg">
                                <div class="font-medium text-gray-700 min-w-[120px] bg-pink-50 px-3 py-2 rounded-lg">日時:</div>
                                <div class="text-gray-900">{{ \Carbon\Carbon::parse($confirms['date'])->isoFormat('YYYY年MM月DD日(ddd)') }} {{ \Carbon\Carbon::parse($confirms['time_start'])->format('H:i') }}</div>
                                <input type="hidden" name="date" value="{{ $confirms['date'] }}">
                                <input type="hidden" name="time_start" value="{{ $confirms['time_start'] }}">
                                <input type="hidden" name="time_end" value="{{ $confirms['time_end'] }}">
                            </div>

                            <div class="flex flex-col md:flex-row md:items-center gap-4 p-4 bg-gray-50 rounded-lg">
                                <div class="font-medium text-gray-700 min-w-[120px] bg-pink-50 px-3 py-2 rounded-lg">コース:</div>
                                <div class="text-gray-900">
                                    @foreach($courses as $course)
                                        {{ $course->id == $confirms['course_id'] ? $course->title : '' }}
                                    @endforeach
                                    @foreach($course_fees as $course_fee)
                                        {{ $course_fee->id == $confirms['course_time'] ? $course_fee->course . ' / '. $course_fee->fee. '円' : '' }}
                                    @endforeach
                                </div>
                                <input type="hidden" name="course_id" value="{{ $confirms['course_id'] }}">
                                <input type="hidden" name="course_time" value="{{ $confirms['course_time'] }}">
                            </div>

                            <div class="flex flex-col md:flex-row md:items-start gap-4 p-4 bg-gray-50 rounded-lg">
                                <div class="font-medium text-gray-700 min-w-[120px] bg-pink-50 px-3 py-2 rounded-lg">キャスト:</div>
                                <div class="text-gray-900">
                                    <div class="bg-white p-4 rounded-lg border">
                                        <p class="font-medium">{{ $confirms['item_name'] }}</p>
                                        <p class="mt-2">
                                        @if (isset($confirms['item_id']))
                                            @foreach($items as $item)
                                                @if ($item->id == $confirms['item_id'])
                                                @if($item->ItemImage)
                                                    <img src="{{ Storage::url('' . $item->ItemImage->filename) }}" class="w-24 h-24 object-cover rounded-lg mt-2">
                                                @endif
                                                @endif
                                            @endforeach
                                        @endif
                                        </p>
                                    </div>
                                </div>
                                <input type="hidden" name="item_id" value="{{ $confirms['item_id'] }}">
                                <input type="hidden" name="item_name" value="{{ $confirms['item_name'] }}">
                            </div>

                            <div class="flex flex-col md:flex-row md:items-start gap-4 p-4 bg-gray-50 rounded-lg">
                                <div class="font-medium text-gray-700 min-w-[120px] bg-pink-50 px-3 py-2 rounded-lg">オプション:</div>
                                <div class="text-gray-900">
                                    @if (isset($confirms['option']))
                                        @foreach($options as $option)
                                            @foreach($confirms['option'] as $key => $val)
                                                @if ($option->id == $val)
                                                <span class="inline-block bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm mr-2 mb-2">{{$option->title}}</span>
                                                <input type="hidden" name="option[]" value="{{ $val }}">
                                                @endif
                                            @endforeach
                                        @endforeach
                                    @endif
                                </div>
                            </div>

                            <div class="flex flex-col md:flex-row md:items-start gap-4 p-4 bg-gray-50 rounded-lg">
                                <div class="font-medium text-gray-700 min-w-[120px] bg-pink-50 px-3 py-2 rounded-lg">オプションPAY:</div>
                                <div class="text-gray-900">
                                    @if (isset($confirms['option_pay']))
                                        @foreach($option_pays as $option_pay)
                                            @foreach($confirms['option_pay'] as $key => $val)
                                                @if ($option_pay->id == $val)
                                                <span class="inline-block bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm mr-2 mb-2">{{$option_pay->title}}</span>
                                                <input type="hidden" name="option_pay[]" value="{{ $val }}">
                                                @endif
                                            @endforeach
                                        @endforeach
                                    @endif
                                </div>
                            </div>

                            <div class="flex flex-col md:flex-row md:items-center gap-4 p-4 bg-gray-50 rounded-lg">
                                <div class="font-medium text-gray-700 min-w-[120px] bg-pink-50 px-3 py-2 rounded-lg">名前:</div>
                                <div class="text-gray-900">{{ $confirms['name'] }}</div>
                                <input type="hidden" name="name" value="{{ $confirms['name'] }}">
                            </div>

                            <div class="flex flex-col md:flex-row md:items-center gap-4 p-4 bg-gray-50 rounded-lg">
                                <div class="font-medium text-gray-700 min-w-[120px] bg-pink-50 px-3 py-2 rounded-lg">メールアドレス:</div>
                                <div class="text-gray-900">{{ $confirms['email'] }}</div>
                                <input type="hidden" name="email" value="{{ $confirms['email'] }}">
                            </div>

                            <div class="flex flex-col md:flex-row md:items-center gap-4 p-4 bg-gray-50 rounded-lg">
                                <div class="font-medium text-gray-700 min-w-[120px] bg-pink-50 px-3 py-2 rounded-lg">電話番号:</div>
                                <div class="text-gray-900">{{ $confirms['tel'] }}</div>
                                <input type="hidden" name="tel" value="{{ $confirms['tel'] }}">
                            </div>

                            @if($confirms['course_id'] != 3)
                            <div class="flex flex-col md:flex-row md:items-start gap-4 p-4 bg-gray-50 rounded-lg">
                                <div class="font-medium text-gray-700 min-w-[120px] bg-pink-50 px-3 py-2 rounded-lg">スタジオ:</div>
                                <div class="text-gray-900">
                                    <div class="bg-white p-4 rounded-lg border">
                                        @if (isset($confirms['equipment_id']))
                                            @foreach($equipments as $equipment)
                                                @php
                                                    $equipmentId = $equipment->external_id ?? $equipment->id;
                                                @endphp
                                                @if ($equipmentId == $confirms['equipment_id'])
                                                <p class="font-medium">{{ $equipment->title }}</p>
                                                @if($equipment->filename)
                                                    <img src="{{ Storage::url('equipment/' . $equipment->filename) }}" class="w-24 h-24 object-cover rounded-lg mt-2">
                                                @endif
                                                @endif
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                                <input type="hidden" name="equipment_id" value="{{ $confirms['equipment_id'] }}">
                            </div>

                            <div class="flex flex-col md:flex-row md:items-start gap-4 p-4 bg-gray-50 rounded-lg">
                                <div class="font-medium text-gray-700 min-w-[120px] bg-pink-50 px-3 py-2 rounded-lg">コスチューム:</div>
                                <div class="text-gray-900 space-y-4">
                                    <div class="bg-white p-4 rounded-lg border">
                                        <p class="font-medium text-sm text-gray-600 mb-2">コスチューム①:</p>
                                        @if (isset($confirms['costume1']))
                                            @foreach($costumes as $costume)
                                                @if ($costume->id == $confirms['costume1'])
                                                <p class="font-medium">{{ $costume->name }}</p>
                                                @if($costume->filename)
                                                    <img src="{{ Storage::url('costume/' . $costume->filename) }}" class="w-24 h-24 object-cover rounded-lg mt-2">
                                                @endif
                                                <input type="hidden" name="costume1" value="{{ $costume->id }}">
                                                @endif
                                            @endforeach
                                        @endif
                                    </div>

                                    <div class="bg-white p-4 rounded-lg border">
                                        <p class="font-medium text-sm text-gray-600 mb-2">コスチューム②:</p>
                                        @if (isset($confirms['costume2']))
                                            @foreach($costumes as $costume)
                                                @if ($costume->id == $confirms['costume2'])
                                                <p class="font-medium">{{ $costume->name }}</p>
                                                @if($costume->filename)
                                                    <img src="{{ Storage::url('costume/' . $costume->filename) }}" class="w-24 h-24 object-cover rounded-lg mt-2">
                                                @endif
                                                <input type="hidden" name="costume2" value="{{ $costume->id }}">
                                                @endif
                                            @endforeach
                                        @endif
                                    </div>

                                    <div class="bg-white p-4 rounded-lg border">
                                        <p class="font-medium text-sm text-gray-600 mb-2">コスチューム③:</p>
                                        @if (isset($confirms['costume3']))
                                            @foreach($costumes as $costume)
                                                @if ($costume->id == $confirms['costume3'])
                                                <p class="font-medium">{{ $costume->name }}</p>
                                                @if($costume->filename)
                                                    <img src="{{ Storage::url('costume/' . $costume->filename) }}" class="w-24 h-24 object-cover rounded-lg mt-2">
                                                @endif
                                                <input type="hidden" name="costume3" value="{{ $costume->id }}">
                                                @endif
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                                <input type="hidden" name="costume1" value="{{ $confirms['costume1'] }}">
                                <input type="hidden" name="costume2" value="{{ $confirms['costume2'] }}">
                                <input type="hidden" name="costume3" value="{{ $confirms['costume3'] }}">
                            </div>
                            @endif

                            @if($confirms['course_id'] == 3)
                            <div class="flex flex-col md:flex-row md:items-center gap-4 p-4 bg-gray-50 rounded-lg">
                                <div class="font-medium text-gray-700 min-w-[120px] bg-pink-50 px-3 py-2 rounded-lg">ご利用エリア:</div>
                                <div class="text-gray-900">{{ $confirms['area'] }}</div>
                                <input type="hidden" name="area" value="{{ $confirms['area'] }}">
                            </div>

                            <div class="flex flex-col md:flex-row md:items-center gap-4 p-4 bg-gray-50 rounded-lg">
                                <div class="font-medium text-gray-700 min-w-[120px] bg-pink-50 px-3 py-2 rounded-lg">具体的な場所:</div>
                                <div class="text-gray-900">{{ $confirms['place'] }}</div>
                                <input type="hidden" name="place" value="{{ $confirms['place'] }}">
                            </div>

                            <div class="flex flex-col md:flex-row md:items-center gap-4 p-4 bg-gray-50 rounded-lg">
                                <div class="font-medium text-gray-700 min-w-[120px] bg-pink-50 px-3 py-2 rounded-lg">待ち合わせ方法:</div>
                                <div class="text-gray-900">{{ $confirms['meet'] }}</div>
                                <input type="hidden" name="meet" value="{{ $confirms['meet'] }}">
                            </div>

                            <div class="flex flex-col md:flex-row md:items-center gap-4 p-4 bg-gray-50 rounded-lg">
                                <div class="font-medium text-gray-700 min-w-[120px] bg-pink-50 px-3 py-2 rounded-lg">住所:</div>
                                <div class="text-gray-900">{{ $confirms['address'] }}</div>
                                <input type="hidden" name="address" value="{{ $confirms['address'] }}">
                            </div>
                            @endif

                            <div class="flex flex-col md:flex-row md:items-start gap-4 p-4 bg-gray-50 rounded-lg">
                                <div class="font-medium text-gray-700 min-w-[120px] bg-pink-50 px-3 py-2 rounded-lg">備考:</div>
                                <div class="text-gray-900 whitespace-pre-wrap">{!! nl2br(e( $confirms['comment'] )) !!}</div>
                                <input type="hidden" name="comment" value="{{ $confirms['comment'] }}">
                            </div>
                        </div>

                        <!-- 料金セクション -->
                        <div class="mt-8 bg-blue-50 rounded-lg p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">料金</h3>
                            <div class="space-y-4">
                                <div class="flex justify-between items-center">
                                    <span class="font-medium text-gray-700">コース料金</span>
                                    <span class="font-bold text-gray-900">
                                        @foreach($course_fees as $course_fee)
                                            @if($course_fee->id == $confirms['course_time'])
                                                {{ number_format($course_fee->fee) }}円
                                                @php $courseFee = $course_fee->fee; @endphp
                                            @endif
                                        @endforeach
                                    </span>
                                </div>

                                <div class="flex justify-between items-start">
                                    <span class="font-medium text-gray-700">有料オプション料金</span>
                                    <div class="text-right">
                                        @php $optionFee = 0; @endphp
                                        @if(isset($confirms['option_pay']))
                                            @foreach($option_pays as $option_pay)
                                                @if(in_array($option_pay->id, (array)$confirms['option_pay']))
                                                    <div class="text-sm text-gray-600">{{ $option_pay->title }}: {{ number_format($option_pay->price) }}円</div>
                                                    @php $optionFee += $option_pay->price; @endphp
                                                @endif
                                            @endforeach
                                        @endif
                                        <div class="font-bold text-gray-900 mt-1">合計: {{ number_format($optionFee) }}円</div>
                                    </div>
                                </div>

                                <div class="flex justify-between items-start">
                                    <span class="font-medium text-gray-700">マイオプション料金</span>
                                    <div class="text-right">
                                        @php $myoptionFee = 0; @endphp
                                        @if(isset($confirms['myoption']))
                                            @foreach($items as $item)
                                                @if($item->id == $confirms['item_id'])
                                                    @foreach($item->myOptions as $myOption)
                                                        @if(in_array($myOption->id, (array)$confirms['myoption']))
                                                            <div class="flex items-center gap-2 mb-2">
                                                                @if($myOption->filename)
                                                                    <img src="{{ Storage::url('myoption/' . $myOption->filename) }}" alt="{{ $myOption->name }}" class="w-12 h-12 object-cover rounded">
                                                                @endif
                                                                <div class="text-sm text-gray-600">{{ $myOption->name }}: {{ number_format($myOption->price) }}円</div>
                                                            </div>
                                                            @php $myoptionFee += $myOption->price; @endphp
                                                            <input type="hidden" name="myoption[]" value="{{ $myOption->id }}">
                                                        @endif
                                                    @endforeach
                                                @endif
                                            @endforeach
                                        @endif
                                        <div class="font-bold text-gray-900 mt-1">合計: {{ number_format($myoptionFee) }}円</div>
                                    </div>
                                </div>

                                <div class="flex justify-between items-start">
                                    <span class="font-medium text-gray-700">マイコスチューム料金</span>
                                    <div class="text-right">
                                        @php $mycostumeFee = 0; @endphp
                                        @if(isset($confirms['mycostume']))
                                            @foreach($items as $item)
                                                @if($item->id == $confirms['item_id'])
                                                    @foreach($item->myCostumes as $myCostume)
                                                        @if(in_array($myCostume->id, (array)$confirms['mycostume']))
                                                            <div class="flex items-center gap-2 mb-2">
                                                                @if($myCostume->filename)
                                                                    <img src="{{ Storage::url('mycostume/' . $myCostume->filename) }}" alt="{{ $myCostume->name }}" class="w-12 h-12 object-cover rounded">
                                                                @endif
                                                                <div class="text-sm text-gray-600">{{ $myCostume->name }}: {{ number_format($myCostume->price) }}円</div>
                                                            </div>
                                                            @php $mycostumeFee += $myCostume->price; @endphp
                                                            <input type="hidden" name="mycostume[]" value="{{ $myCostume->id }}">
                                                        @endif
                                                    @endforeach
                                                @endif
                                            @endforeach
                                        @endif
                                        <div class="font-bold text-gray-900 mt-1">合計: {{ number_format($mycostumeFee) }}円</div>
                                    </div>
                                </div>

                                <hr class="border-gray-300">

                                <div class="flex justify-between items-center">
                                    <span class="font-medium text-gray-700">小　計</span>
                                    <span class="font-bold text-gray-900">
                                        @php
                                            $subtotal = $courseFee + $optionFee + $myoptionFee + $mycostumeFee;
                                        @endphp
                                        {{ number_format($subtotal) }}円
                                    </span>
                                </div>

                                <div class="flex justify-between items-center">
                                    <span class="font-medium text-gray-700">消費税</span>
                                    <span class="font-bold text-gray-900">
                                        @php
                                            $tax = floor($subtotal * 0.1);
                                        @endphp
                                        {{ number_format($tax) }}円
                                    </span>
                                </div>

                                <hr class="border-gray-300">

                                <div class="flex justify-between items-center text-lg">
                                    <span class="font-bold text-gray-900">合　計</span>
                                    <span class="font-bold text-blue-600 text-xl">
                                        @php
                                            $total = $subtotal + $tax;
                                        @endphp
                                        {{ number_format($total) }}円
                                    </span>
                                </div>
                            </div>

                            <input type="hidden" name="fee_course" value="{{ $courseFee }}">
                            <input type="hidden" name="fee_option" value="{{ $optionFee }}">
                            <input type="hidden" name="fee_myoption" value="{{ $myoptionFee }}">
                            <input type="hidden" name="fee_mycostume" value="{{ $mycostumeFee }}">
                            <input type="hidden" name="fee_total" value="{{ $total }}">

                            @if($confirms['course_id'] == 3)
                            <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                                <p class="text-yellow-800 text-sm">
                                    ※移動費、交通費は集計してショップからのメールにてお知らせいたします。
                                </p>
                            </div>
                            @endif
                        </div>

                        <!-- ボタン -->
                        <div class="flex flex-col sm:flex-row gap-4 mt-8">
                            <a href="{{ route('reservation.index') }}" 
                                class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-3 px-6 rounded-lg text-center transition duration-200">
                                <i class="fas fa-arrow-left mr-2"></i>
                                戻る
                            </a>
                            <button type="submit" 
                                class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-6 rounded-lg transition duration-200 transform hover:scale-105">
                                <i class="fas fa-check mr-2"></i>
                                予約を確定する
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
