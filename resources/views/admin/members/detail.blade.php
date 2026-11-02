<!-- モーダル本体（Bootstrap 不使用） -->
<div class="af-modal-overlay" id="resModal{{ $member->id }}" tabindex="-1" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="resModalTitleMem{{ $member->id }}">
    <div class="af-modal">
        <div class="af-modal-header">
            <h5 class="af-modal-title" id="resModalTitleMem{{ $member->id }}">ID：{{ $member->id }}</h5>
            <button type="button" class="af-modal-close" data-af-modal-close aria-label="閉じる">&times;</button>
        </div>
        <div class="af-modal-body">
                <div class="flex mb-1">
<p style="margin-right:1rem;">登録日：{{ $member->created_at ? $member->created_at->format('Y/m/d　H:i:s') : '' }}</p>


</div>

<table class="listTbl">
    <tr>
        <th>名前</th>
        <td>【 会員番号:{{ $member->member_no }} 】&emsp;{{ $member->name }} [ {{ $member->nickname }} ]&emsp;
        @if( !empty($member->member_id) )
        <a href="{{ route('admin.members.edit', ['member' => $member->member_id]) }}" target="_blank"><i class="las la-external-link-square-alt"></i></a>
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
        <th>性別</th>
        <td>@if( $member->gender == 1)
            男性
            @elseif( $member->gender == 2)
            女性
            @else
            未回答/それ以外
            @endif
        </td>
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
        <div class="af-modal-footer">
            <button type="button" class="af-btn af-btn-cancel" data-af-modal-close>閉じる</button>
        </div>
    </div>
</div>
