<!-- モーダル本体 -->
<div class="modal fade" id="resModal{{ $reservation->id }}" tabindex="-1" aria-labelledby="resModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="resModalLabel">ID：{{ $reservation->id }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="flex mb-1">
<p class="mr-3">登録日：{{ $reservation->created_at ? $reservation->created_at->format('Y/m/d　H:i:s') : '' }}</p>

<p>
登録者：{{ $reservation->name }}
</p>
</div>

<table class="listTbl">
    <tr>
        <th>予約名</th>
        <td>【 {{ !empty($reservation->member_id) ? '会員ID:'.$reservation->member_id : '非会員' }} 】&emsp;{{ $reservation->name }}&emsp;
        @if( !empty($reservation->member_id) )
        <a href="{{ route('item.members.edit', ['member' => $reservation->member_id]) }}" target="_blank"><i class="las la-external-link-square-alt"></i></a>
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

        @foreach($course_fees as $fee)
        {{ $reservation->course_time == $fee->id ? $fee->course : '' }}
        @endforeach
        </td>
    </tr>
    <tr>
        <th>キャスト</th>
        <td>
        @foreach($items as $item)
        {{ $reservation->item_id == $item->id ? $item->item_name : '' }}
        @endforeach

        <p class="text-xs">(予約時のキャスト名：{{ $reservation->item_name }})</p>
        </td>
    </tr>
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
        <th>オプションPAY</th>
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
        <td>{{ number_format($reservation->fee_total) }} 円</td>
    </tr>
</table>




            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">閉じる</button>
            </div>
        </div>
    </div>
</div>
</div>
