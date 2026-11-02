<!-- モーダル本体 -->
<div class="modal fade" id="resModal{{ $member->id }}" tabindex="-1" aria-labelledby="resModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="resModalLabel">ID：{{ $member->id }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="flex mb-1">
<p class="mr-3">登録日：{{ $member->created_at ? $member->created_at->format('Y/m/d　H:i:s') : '' }}</p>


</div>

<table class="listTbl">
    <tr>
        <th>名前</th>
        <td>【 会員ID:{{ $member->member_id }} 】&emsp;{{ $member->name }}&emsp;
        @if( !empty($member->member_id) )
        <a href="{{ route('item.members.edit', ['member' => $member->member_id]) }}" target="_blank"><i class="las la-external-link-square-alt"></i></a>
        @endif
        </td>
    </tr>
    <tr>
        <th>メールアドレス</th>
        <td>{{ $member->email }}</td>
    </tr>
    <tr>
        <th>電話番号</th>
        <td>{{ $member->tel }}</td>
    </tr>
    <tr>
        <th>生年月日</th>
        <td>{{ $member->birth }}</td>
    </tr>
    <tr>
        <th>性別</th>
        <td>{{ $member->gender }}</td>
    </tr>
    <tr>
        <th>LINE</th>
        <td>{{ $member->line }}</td>
    </tr>
    <tr>
        <th>SNS</th>
        <td>{{ $member->sns }}</td>
    </tr>
    <tr>
        <th>ホームページ等</th>
        <td>{{ $member->url }}</td>
    </tr>
    <tr>
        <th>メモ</th>
        <td>{!! nl2br(e($member->memo)) !!}</td>
    </tr>
    <tr>
        <th>メモ（共有）</th>
        <td>{!! nl2br(e($member->memo_share)) !!}</td>
    </tr>
</table>



<table class="listTbl">
    <tr>
        <th>お気に入り</th>
        <td>
            @foreach($items as $item)
            {{ $member->item_id == $item->id ? $item->item_name : '' }}
            @endforeach
        </td>
    </tr>
    <tr>
        <th>ブロック</th>
        <td>
            @foreach($items as $item)
            {{ $member->item_id == $item->id ? $item->item_name : '' }}
            @endforeach
        </td>
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
