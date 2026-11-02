<!-- モーダル本体（Bootstrap 不使用） -->
<div class="af-modal-overlay" id="resModal{{ $reservation->id }}" tabindex="-1" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="resModalTitle{{ $reservation->id }}">
    <div class="af-modal">
        <div class="af-modal-header">
            <h5 class="af-modal-title" id="resModalTitle{{ $reservation->id }}">ID：{{ $reservation->id }}</h5>
            <button type="button" class="af-modal-close" data-af-modal-close aria-label="閉じる">&times;</button>
        </div>
        <div class="af-modal-body">
            <style>
            #resModal{{ $reservation->id }} .listTbl {
                width: 100%;
                margin: 0.3rem auto;
            }
            #resModal{{ $reservation->id }} .listTbl th {
                width: 25%;
                background: #fafafa;
            }
            </style>
            @php
                // マイオプション: reservation->myoption は JSON配列(ID) で保存されている
                $__myoptionIds = [];
                if (!empty($reservation->myoption)) {
                    $__myoptionIds = is_array($reservation->myoption)
                        ? $reservation->myoption
                        : (json_decode($reservation->myoption, true) ?? []);
                }
                $myoptions = [];
                if (!empty($__myoptionIds)) {
                    foreach (\App\Models\Myoption::whereIn('id', $__myoptionIds)->get() as $__mo) {
                        $myoptions[] = ['name' => $__mo->name, 'price' => number_format($__mo->price)];
                    }
                }

                // マイコスチューム
                $__mycostumeIds = [];
                if (!empty($reservation->mycostume)) {
                    $__mycostumeIds = is_array($reservation->mycostume)
                        ? $reservation->mycostume
                        : (json_decode($reservation->mycostume, true) ?? []);
                }
                $mycostumes = [];
                if (!empty($__mycostumeIds)) {
                    foreach (\App\Models\Mycostume::whereIn('id', $__mycostumeIds)->get() as $__mc) {
                        $mycostumes[] = ['name' => $__mc->name, 'price' => number_format($__mc->price)];
                    }
                }
            @endphp
                <div class="flex mb-1">
<p style="margin-right:1rem;">登録日：{{ $reservation->created_at ? $reservation->created_at->format('Y/m/d　H:i:s') : '' }}</p>

<p>
登録者：
@foreach ($staffs as $staff)
@if ($staff->id == $reservation->staff_id)
    {{ $staff->name }}
@endif
@endforeach
</p>
</div>

<table class="listTbl">
    <tr>
        <th>予約名</th>
        <td>【 {{ !empty($reservation->member_id) ? '会員ID:'.$reservation->member_id : '非会員' }} 】&emsp;{{ $reservation->name }}&emsp;
        @if( !empty($reservation->member_id) )
        <a href="{{ route('admin.members.edit', ['member' => $reservation->member_id]) }}" target="_blank"><i class="las la-external-link-square-alt"></i></a>
        @endif
        </td>
    </tr>
    <tr>
        <th>メールアドレス</th>
        <td>{{ $reservation->email }}</td>
    </tr>
    <tr>
        <th>電話番号</th>
        <td>{{ $reservation->tel }}</td>
    </tr>
</table>
<table class="listTbl">
    <tr>
        <th>ステータス</th>
        <td>
            @switch($reservation->kind)
                @case('0')
                通常予約
                @break
                @case('1')
                確認中
                @break
                @case('2')
                確定
                @break
                @case('3')
                保留
                @break
                @default
                未設定
            @endswitch
        </td>
    </tr>
    <tr>
        <th>種別</th>
        <td>{{ $reservation->kind == 1 ? '通常予約' : 'リクエスト予約' }}</td>
    </tr>
    <tr>
        <th>日時</th>
        <td><p>{{ $reservation->date->format('Y/m/d').'('. $reservation->date->isoFormat('ddd'). ')' }}
        </p>
       <p>
       {{ $reservation->time_start ? $reservation->time_start->format('H:i') : ''}} -
       {{ $reservation->time_end ? $reservation->time_end->format('H:i') : '' }}
       </p></td>
    </tr>
    <tr>
        <th>コース</th>
        <td>
        @foreach($courses as $course)
        {{ $reservation->course_id == $course->id ? $course->title  : '' }}
        @endforeach
        
        @isset($reservation->course_time)
        <span class="mx-2">/</span>{{ $reservation->course_time }}分
        @endisset

        @foreach($course_fees as $fee)
        {{ $reservation->course_time == $fee->id ? $fee->course : '' }}
        @endforeach
        </td>
    </tr>
    @isset($reservation->equipment_id)
    <tr>
        <th>スタジオ</th>
        <td>
        @foreach($equipments as $equipment)
        @php
            $equipmentId = $equipment->external_id ?? $equipment->id;
        @endphp
        {{ $reservation->equipment_id == $equipmentId ? $equipment->title  : '' }}
        @endforeach
        </td>
    </tr>
    @endisset
    <tr>
        <th>キャスト</th>
        <td>
        @foreach($items as $item)
        {{ $reservation->item_id == $item->id ? $item->item_name : '' }}
        @endforeach

        <p class="text-xs">(予約時のキャスト名：{{ $reservation->item_name }})</p>
        </td>
    </tr>
    @isset($reservation->item_name_other)
    <tr>
        <th>キャストその他</th>
        <td>
        {{ $reservation->item_name_other }}
        </td>
    </tr>
    @endisset
    @isset($reservation->option)
    <tr>
        <th>オプション</th>
        <td class="options">
        @php
        $setOption = $reservation->option;
        $optionVals = is_array($setOption) ? $setOption : explode('-', $setOption);
        @endphp

        @foreach($options as $option)
        <div class="px-1">{{ in_array($option->id, $optionVals) ? '★'.$option->title : '' }}</div>
        @endforeach
        </td>
    </tr>
    @endisset
    @isset($reservation->option_pay)
    <tr>
        <th>有料オプション</th>
        <td class="options">
        @php
        $setOptionPay = $reservation->option_pay;
        $optionPayVals = is_array($setOptionPay) ? $setOptionPay : explode('-', $setOptionPay);
        @endphp

        @foreach($option_pays as $option_pay)
        {{ in_array($option_pay->id, $optionPayVals) ? '★'.$option_pay->title : '' }}
        @endforeach
        </td>
    </tr>
    @endisset
    @if(!empty($myoptions))
    <tr>
        <th>マイオプション</th>
        <td class="options">
        @foreach($myoptions as $mo)
            <div class="px-1">★{{ $mo['name'] }}<span class="text-xs ml-2 text-gray-500">{{ $mo['price'] }}円</span></div>
        @endforeach
        </td>
    </tr>
    @endif
    @if(!empty($mycostumes))
    <tr>
        <th>マイコスチューム</th>
        <td class="options">
        @foreach($mycostumes as $mc)
            <div class="px-1">★{{ $mc['name'] }}</div>
        @endforeach
        </td>
    </tr>
    @endif

    @isset($reservation->area)
    <tr>
        <th>ご利用エリア</th>
        <td>{{ $reservation->area }}</td>
    </tr>
    @endisset
    @isset($reservation->place)
    <tr>
        <th>具体的な場所</th>
        <td>{{ $reservation->place }}</td>
    </tr>
    @endisset
    @isset($reservation->meet)
    <tr>
        <th>待ち合わせ方法</th>
        <td>{{ $reservation->meet }}</td>
    </tr>
    @endisset
    @isset($reservation->address)
    <tr>
        <th>住所</th>
        <td>{{ $reservation->address }}</td>
    </tr>
    @endisset
    @isset($reservation->comment)
    <tr>
        <th>備考</th>
        <td>{!! nl2br(e($reservation->comment)) !!}</td>
    </tr>
    @endisset
    @isset($reservation->memo)
    <tr>
        <th>メモ（管理者のみ）</th>
        <td>{!! nl2br(e($reservation->memo)) !!}</td>
    </tr>
    @endisset
    @isset($reservation->memo_share)
    <tr>
        <th>メモ（キャスト共有）</th>
        <td>{!! nl2br(e($reservation->memo_share)) !!}</td>
    </tr>
    @endisset
</table>


<table class="listTbl">
    <tr>
        <th>コース料金</th>
        <td>{{ number_format($reservation->fee_course) }} 円</td>
    </tr>
    <tr>
        <th>オプション料金</th>
        <td>{{ number_format($reservation->fee_option) }} 円</td>
    </tr>
    <tr>
        <th>調整金</th>
        <td>{{ number_format($reservation->fee_adjust) }} 円</td>
    </tr>
    <tr>
        <th>調整金メモ</th>
        <td>{{ $reservation->fee_adjust_memo }}</td>
    </tr>
    <tr>
        <th>総　額</th>
        <td>{{ number_format($reservation->fee_total) }} 円<span class="text-xs ml-6">（税込 {{ number_format($reservation->fee_total * config('tax.intax')) }} 円）</span></td>
    </tr>
</table>




        </div>
        <div class="af-modal-footer">
            <button type="button" class="af-btn af-btn-cancel" data-af-modal-close>閉じる</button>
        </div>
    </div>
</div>
