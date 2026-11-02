@extends('layouts.app')

@section('breadcrumb_label', 'ご予約内容の確認')

@section('content')
<div class="titleArea">
    <h2>RESERVATION</h2>
    <div class="title">ご予約内容の確認</div>
</div>

<div class="reservation-confirm-page">
    <div class="reservation-confirm-wrap">
        <!-- タイトルとフォームのコンテナ -->
        <div class="w-full">
            <!-- タイトルセクション -->
            @if(Auth::guard('member')->check())
            <div class="w-full mb-4">
                <div class="reservation-card">

                    <!-- 会員情報表示 -->
                    <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                        <p class="text-blue-800 font-semibold text-sm">
                            [ 会員番号:{{ Auth::guard('member')->user()->member_no }} ]
                        </p>
                        <p class="text-blue-800 font-semibold">
                            {{ Auth::guard('member')->user()->name }} 様
                        </p>
                    </div>
                </div>
            </div>
            @endif

            <!-- 確認フォームセクション -->
            <div class="w-full">
                <div class="reservation-card">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">予約内容確認</h2>

                    @if($errors->any())
                        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg" role="alert">
                            <p class="text-red-800 font-medium text-sm mb-2">送信できませんでした。内容をご確認ください。</p>
                            <ul class="text-red-700 text-sm list-disc list-inside space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ url('/reservation') }}" method="POST" id="reservationForm">
                        @csrf

                        <div class="reservation-confirm-stack">
                            <div class="reservation-confirm-row">
                                <div class="reservation-confirm-row__title">予約形態:</div>
                                <div class="reservation-confirm-row__body">{{ $confirms['kind'] == 1 ? '通常予約' : 'リクエスト予約'}}</div>
                                <input type="hidden" name="kind" value="{{ $confirms['kind'] }}">
                            </div>

                            <div class="reservation-confirm-row">
                                <div class="reservation-confirm-row__title">日時:</div>
                                <div class="reservation-confirm-row__body">{{ \Carbon\Carbon::parse($confirms['date'])->isoFormat('YYYY年MM月DD日(ddd)') }} {{ \Carbon\Carbon::parse($confirms['time_start'])->format('H:i') }}</div>
                                <input type="hidden" name="date" value="{{ $confirms['date'] }}">
                                <input type="hidden" name="time_start" value="{{ $confirms['time_start'] }}">
                                <input type="hidden" name="time_end" value="{{ $confirms['time_end'] }}">
                            </div>

                            <div class="reservation-confirm-row">
                                <div class="reservation-confirm-row__title">コース:</div>
                                <div class="reservation-confirm-row__body">
                                    @foreach($courses as $course)
                                        @if($course->id == $confirms['course_id'])
                                            {{ $course->title }} / {{ $confirms['course_time'] }}分
                                        @endif
                                    @endforeach
                                </div>
                                <input type="hidden" name="course_id" value="{{ $confirms['course_id'] }}">
                                <input type="hidden" name="course_time" value="{{ $confirms['course_time'] }}">
                            </div>

                            <div class="reservation-confirm-row reservation-confirm-row--top">
                                <div class="reservation-confirm-row__title">キャスト:</div>
                                <div class="reservation-confirm-row__body">
                                    <div class="reservation-confirm-white-inset text-center">
                                        @if (isset($confirms['item_id']))
                                            @foreach($items as $item)
                                                @if ($item->id == $confirms['item_id'])
                                                <p class="font-medium">{{ $item->item_name }}</p>
                                                    @if($item->ItemImage)
                                                        <img src="{{ Storage::url('' . $item->ItemImage->filename) }}" class="w-24 h-24 object-cover rounded-lg mt-2">
                                                    @endif
                                                @endif
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                                @if(isset($confirms['item_name_other']) && !empty($confirms['item_name_other']))
                                <div class="reservation-confirm-row__title">キャストその他:</div>
                                <div class="reservation-confirm-row__body">
                                    <div class="reservation-confirm-white-inset">
                                        {{ $confirms['item_name_other'] }}
                                    </div>
                                </div>
                                @endif
                                <input type="hidden" name="item_id" value="{{ $confirms['item_id'] }}">
                                <input type="hidden" name="item_name_other" value="{{ $confirms['item_name_other'] }}">
                            </div>


                            @if(isset($confirms['myoption']) && !empty($confirms['myoption']))
                            <div class="reservation-confirm-row reservation-confirm-row--top">
                                <div class="reservation-confirm-row__title">マイオプション:</div>
                                <div class="reservation-confirm-row__body">
                                    <div class="reservation-confirm-white-inset">
                                        @if(isset($confirms['myoption']))
                                            @foreach($items as $item)
                                                @if($item->id == $confirms['item_id'])
                                                    @foreach($item->myOptions as $myOption)
                                                        @if(in_array($myOption->id, (array)$confirms['myoption']))
                                                            <div class="flex items-center gap-2 mb-2">
                                                                @if($myOption->filename)
                                                                    <img src="{{ Storage::url('myoption/' . $myOption->filename) }}" alt="{{ $myOption->name }}" class="w-12 h-12 object-cover rounded">
                                                                @endif
                                                                <div class="text-sm text-gray-600">{{ $myOption->name }}</div>
                                                            </div>
                                                            <input type="hidden" name="myoption[]" value="{{ $myOption->id }}">
                                                        @endif
                                                    @endforeach
                                                @endif
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endif



                            @if(isset($confirms['mycostume']) && !empty($confirms['mycostume']))
                            <div class="reservation-confirm-row reservation-confirm-row--top">
                                <div class="reservation-confirm-row__title">マイコスチューム:</div>
                                <div class="reservation-confirm-row__body">
                                    <div class="reservation-confirm-white-inset">
                                        @if(isset($confirms['mycostume']))
                                            @foreach($items as $item)
                                                @if($item->id == $confirms['item_id'])
                                                    @foreach($item->myCostumes as $myCostume)
                                                        @if(in_array($myCostume->id, (array)$confirms['mycostume']))
                                                            <div class="flex items-center gap-2 mb-2">
                                                                @if($myCostume->filename)
                                                                    <img src="{{ Storage::url('mycostume/' . $myCostume->filename) }}" alt="{{ $myCostume->name }}" class="w-12 h-12 object-cover rounded">
                                                                @endif
                                                                <div class="text-sm text-gray-600">{{ $myCostume->name }}</div>
                                                            </div>
                                                            <input type="hidden" name="mycostume" value="{{ $myCostume->id }}">
                                                        @endif
                                                    @endforeach
                                                @endif
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endif


                            <div class="reservation-confirm-row reservation-confirm-row--top">
                                <div class="reservation-confirm-row__title">オプション:</div>
                                <div class="reservation-confirm-row__body">
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

                            <div class="reservation-confirm-row reservation-confirm-row--top">
                                <div class="reservation-confirm-row__title">有料オプション:</div>
                                <div class="reservation-confirm-row__body">
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

                            <div class="reservation-confirm-row reservation-confirm-row--top">
                                <div class="reservation-confirm-row__title">コスチューム:</div>
                                <div class="reservation-confirm-row__body reservation-confirm-costume-preview">
                                    <div class="reservation-confirm-white-inset">
                                        @if (isset($confirms['costume1']) && !empty($confirms['costume1']))
                                            @foreach($costumes as $costume)
                                                @if ($costume->id == $confirms['costume1'])
                                                <p class="font-medium">{{ $costume->name }}</p>
                                                @if($costume->filename)
                                                    <img src="{{ Storage::url('costume/' . $costume->filename) }}" class="w-32 h-32 object-cover rounded-lg mt-2" alt="">
                                                @endif
                                                <input type="hidden" name="costume1" value="{{ $costume->id }}">
                                                @endif
                                            @endforeach
                                        @else
                                            <p class="text-gray-400 text-sm">未選択</p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" name="equipment_id" value="{{ $confirms['equipment_id'] ?? '' }}">

                            <div class="reservation-confirm-row">
                                <div class="reservation-confirm-row__title">ご利用エリア:</div>
                                <div class="reservation-confirm-row__body">{{ $confirms['area'] ?? '' }}</div>
                                <input type="hidden" name="area" value="{{ $confirms['area'] ?? '' }}">
                            </div>

                            <div class="reservation-confirm-row">
                                <div class="reservation-confirm-row__title">具体的な場所:</div>
                                <div class="reservation-confirm-row__body">{{ $confirms['place'] ?? '' }}</div>
                                <input type="hidden" name="place" value="{{ $confirms['place'] ?? '' }}">
                            </div>

                            <div class="reservation-confirm-row">
                                <div class="reservation-confirm-row__title">待ち合わせ方法:</div>
                                <div class="reservation-confirm-row__body">{{ $confirms['meet'] ?? '' }}</div>
                                <input type="hidden" name="meet" value="{{ $confirms['meet'] ?? '' }}">
                            </div>

                            <div class="reservation-confirm-row">
                                <div class="reservation-confirm-row__title">住所:</div>
                                <div class="reservation-confirm-row__body">{{ $confirms['address'] ?? '' }}</div>
                                <input type="hidden" name="address" value="{{ $confirms['address'] ?? '' }}">
                            </div>

                            <div class="reservation-confirm-row">
                                <div class="reservation-confirm-row__title">名前:</div>
                                <div class="reservation-confirm-row__body">{{ $confirms['name'] }}</div>
                                <input type="hidden" name="name" value="{{ $confirms['name'] }}">
                            </div>

                            <div class="reservation-confirm-row">
                                <div class="reservation-confirm-row__title">メールアドレス:</div>
                                <div class="reservation-confirm-row__body">{{ $confirms['email'] }}</div>
                                <input type="hidden" name="email" value="{{ $confirms['email'] }}">
                            </div>

                            <div class="reservation-confirm-row">
                                <div class="reservation-confirm-row__title">電話番号:</div>
                                <div class="reservation-confirm-row__body">{{ $confirms['tel'] }}</div>
                                <input type="hidden" name="tel" value="{{ $confirms['tel'] }}">
                            </div>

                            <div class="reservation-confirm-row reservation-confirm-row--top">
                                <div class="reservation-confirm-row__title">備考:</div>
                                <div class="reservation-confirm-row__body reservation-confirm-row__body--pre">{!! nl2br(e( $confirms['comment'] )) !!}</div>
                                <input type="hidden" name="comment" value="{{ $confirms['comment'] }}">
                            </div>
                        </div>

                        <!-- 料金セクション -->

                        @if(isset($confirms['item_id']))
                        <div class="reservation-fee-box">
                            <h3>料金</h3>
                            <div class="reservation-fee-stack">
                                <div class="flex justify-between items-center">
                                    <span class="font-medium text-gray-700">コース料金</span>
                                    <span class="font-bold text-gray-900">
                                        {{ number_format($courseFee) }}円
                                    </span>
                                </div>

                                <div class="flex justify-between items-start">
                                    <span class="font-medium text-gray-700">有料オプション料金</span>
                                    <div class="text-right">
                                        @if(isset($confirms['option_pay']))
                                            @foreach($option_pays as $option_pay)
                                                @if(in_array($option_pay->id, (array)$confirms['option_pay']))
                                                    <div class="text-sm text-gray-600">{{ $option_pay->title }}: {{ number_format($option_pay->price) }}円</div>
                                                @endif
                                            @endforeach
                                        @endif
                                        <div class="font-bold text-gray-900 mt-1 text-sm">合計: {{ number_format($optionFee) }}円</div>
                                    </div>
                                </div>

                                @if(isset($confirms['myoption']) && !empty($confirms['myoption']))
                                <div class="flex justify-between items-start">
                                    <span class="font-medium text-gray-700">マイオプション料金</span>
                                    <div class="text-right">
                                        @if(isset($confirms['myoption']))
                                            @foreach($items as $item)
                                                @if($item->id == $confirms['item_id'])
                                                    @foreach($item->myOptions as $myOption)
                                                        @if(in_array($myOption->id, (array)$confirms['myoption']))
                                                            <div class="flex items-center gap-2 mb-2">
                                                                @if($myOption->filename)
                                                                    <img src="{{ Storage::url('myoption/' . $myOption->filename) }}" alt="{{ $myOption->name }}" class="w-12 h-12 object-cover rounded">
                                                                @endif
                                                                <div class="text-sm text-gray-600">{{ $myOption->name }}
                                                                    @if ($myOption->price) {{ number_format($myOption->price) }}円 @endif</div>
                                                            </div>
                                                            <input type="hidden" name="myoption[]" value="{{ $myOption->id }}">
                                                        @endif
                                                    @endforeach
                                                @endif
                                            @endforeach
                                        @endif
                                        <div class="font-bold text-gray-900 mt-1 text-sm">合計: {{ number_format($myoptionFee) }}円</div>
                                    </div>
                                </div>
                                @endif


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
                                    <span class="font-bold text-blue-700 text-xl">
                                        {{ number_format($total) }}円
                                    </span>
                                </div>
                            </div>

                            <input type="hidden" name="fee_course" value="{{ $courseFee }}">
                            <input type="hidden" name="fee_option" value="{{ $optionFee }}">
                            <input type="hidden" name="fee_myoption" value="{{ $myoptionFee }}">
                            <input type="hidden" name="fee_mycostume" value="{{ $mycostumeFee }}">
                            <input type="hidden" name="fee_adjust" value="0">
                            <input type="hidden" name="fee_total" value="{{ $subtotal }}">



                        </div>
                        @else
                        <div class="reservation-fee-box">
                            <h3>料金</h3>
                            <div class="reservation-fee-stack">
                                <div class="flex justify-between items-center">
                                    <span class="font-medium text-gray-700">料金はキャストにより異なるため追ってご連絡いたします</span>

                                </div>
                            </div>

                            <input type="hidden" name="fee_course" value="{{ $courseFee }}">
                            <input type="hidden" name="fee_option" value="{{ $optionFee }}">
                            <input type="hidden" name="fee_myoption" value="{{ $myoptionFee }}">
                            <input type="hidden" name="fee_mycostume" value="{{ $mycostumeFee }}">
                            <input type="hidden" name="fee_adjust" value="0">
                            <input type="hidden" name="fee_total" value="{{ $courseFee + $optionFee + $myoptionFee + $mycostumeFee }}">
                        </div>
                        @endif

                        <!-- ボタン -->
                        <div class="flex flex-col sm:flex-row gap-4 mt-5">
                            <a href="{{ route('reservation.index') }}"
                                class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-3 px-6 rounded-lg text-center transition duration-200">
                                <i class="fas fa-arrow-left mr-2"></i>
                                戻る
                            </a>
                            <button type="submit"
                                class="flex-1 bg-blue-400 hover:bg-blue-700 text-slate-900 font-medium py-3 px-6 rounded-lg transition duration-200 transform hover:scale-105">
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


<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('reservationForm');
    const submitButton = form.querySelector('button[type="submit"]');

    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('Form submission started');

            // 送信ボタンを無効にする
            submitButton.disabled = true;
            submitButton.textContent = '送信中...';

            // フォームを送信
            this.submit();
        });
    }
});
</script>

@endsection
