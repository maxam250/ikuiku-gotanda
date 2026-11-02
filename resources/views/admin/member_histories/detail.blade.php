<!-- モーダル本体 -->
<div class="modal fade" id="resModal{{ $history->id }}" tabindex="-1" aria-labelledby="resModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="resModalLabel">ID：{{ $history->id }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="flex mb-1">
<p style="margin-right:1rem;">登録日：{{ $history->created_at ? $history->created_at->format('Y/m/d　H:i:s') : '' }}</p>

<p>
登録者：●●●●●
</p>
</div>

<table class="listTbl">
    <tr>
        <th>予約名</th>
        <td>【 {{ !empty($history->member_id) ? '会員ID:'.$history->member_id : '非会員' }} 】&emsp;{{ $history->name }}&emsp;
        @if( !empty($history->member_id) )
        <a href="{{ route('admin.members.edit', ['member' => $history->member_id]) }}" target="_blank"><i class="las la-external-link-square-alt"></i></a>
        @endif
        </td>
    </tr>
    <tr>
        <th>メールアドレス</th>
        <td>{{ $history->email }}</td>
    </tr>
    <tr>
        <th>電話番号</th>
        <td>{{ $history->tel }}</td>
    </tr>
</table>
<table class="listTbl">
    <tr>
        <th>ステータス</th>
        <td>
            @switch($history->kind)
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
        <td>{{ $history->kind == 1 ? '通常予約' : 'リクエスト予約' }}</td>
    </tr>
    <tr>
        <th>日時</th>
        <td><p>{{ $history->date->format('Y/m/d').'('. $history->date->isoFormat('ddd'). ')' }}
        </p>
       <p>
       {{ $history->time_start ? $history->time_start->format('H:i') : ''}} -
       {{ $history->time_end ? $history->time_end->format('H:i') : '' }}
       </p></td>
    </tr>
    <tr>
        <th>コース</th>
        <td>
        @foreach($courses as $course)
        {{ $history->course_id == $course->id ? $course->title  : '' }}
        @endforeach

        @foreach($course_fees as $fee)
        {{ $history->course_time == $fee->id ? $fee->course : '' }}
        @endforeach
        </td>
    </tr>
    <tr>
        <th>キャスト</th>
        <td>
        @foreach($items as $item)
        {{ $history->item_id == $item->id ? $item->item_name : '' }}
        @endforeach

        <p class="text-xs">(予約時のキャスト名：{{ $history->item_name }})</p>
        </td>
    </tr>
    @isset($history->item_name_other)
    <tr>
        <th>キャストその他</th>
        <td>
        {{ $history->item_name_other }}
        </td>
    </tr>
    @endisset
    @isset($history->option)
    <tr>
        <th>オプション</th>
        <td class="options">
        @php
        $setOption = $history->option;
        $optionVals = is_array($setOption) ? $setOption : explode('-', $setOption);
        @endphp

        @foreach($options as $option)
        <div class="px-1">{{ in_array($option->id, $optionVals) ? '★'.$option->title : '' }}</div>
        @endforeach
        </td>
    </tr>
    @endisset
    @isset($history->option_pay)
    <tr>
        <th>オプションPAY</th>
        <td class="options">
        @php
        $setOptionPay = $history->option_pay;
        $optionPayVals = is_array($setOptionPay) ? $setOptionPay : explode('-', $setOptionPay);
        @endphp

        @foreach($option_pays as $option_pay)
        {{ in_array($option_pay->id, $optionPayVals) ? '★'.$option_pay->title : '' }}
        @endforeach
        </td>
    </tr>
    @endisset

    @isset($history->area)
    <tr>
        <th>ご利用エリア</th>
        <td>{{ $history->area }}</td>
    </tr>
    @endisset
    @isset($history->place)
    <tr>
        <th>具体的な場所</th>
        <td>{{ $history->place }}</td>
    </tr>
    @endisset
    @isset($history->meet)
    <tr>
        <th>待ち合わせ方法</th>
        <td>{{ $history->meet }}</td>
    </tr>
    @endisset
    @isset($history->address)
    <tr>
        <th>住所</th>
        <td>{{ $history->address }}</td>
    </tr>
    @endisset
    @isset($history->comment)
    <tr>
        <th>備考</th>
        <td>{!! nl2br(e($history->comment)) !!}</td>
    </tr>
    @endisset
    @isset($history->memo)
    <tr>
        <th>メモ（管理者のみ）</th>
        <td>{!! nl2br(e($history->memo)) !!}</td>
    </tr>
    @endisset
    @isset($history->memo_share)
    <tr>
        <th>メモ（キャスト共有）</th>
        <td>{!! nl2br(e($history->memo_share)) !!}</td>
    </tr>
    @endisset
</table>


<table class="listTbl">
    <tr>
        <th>コース料金</th>
        <td>{{ number_format($history->fee_course) }} 円</td>
    </tr>
    <tr>
        <th>オプション料金</th>
        <td>{{ number_format($history->fee_option) }} 円</td>
    </tr>
    <tr>
        <th>調整金</th>
        <td>{{ number_format($history->fee_adjust) }} 円</td>
    </tr>
    <tr>
        <th>調整金メモ</th>
        <td>{{ $history->fee_adjust_memo }}</td>
    </tr>
    <tr>
        <th>総　額</th>
        <td>{{ number_format($history->fee_total) }} 円</td>
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
