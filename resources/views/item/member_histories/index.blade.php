
<x-item-layout>


<x-slot name="header">
会員一覧
</x-slot>

<section class="toPageArea">
    <a href="{{ route('item.members.create') }}" class="create">新しい会員を作成</a>
</section>


<section>

    @php
        $now = date('Y-m-d H:i:s');
    @endphp
    <table class="listTbl">
        <thead>
            <tr>
                <th>詳細</th>
                <th>種別</th>
                <th>名前</th>
                <th>連絡先</th>
                <th>ポイント</th>
                <th>メモ</th>
                <th>メモ共有</th>
                <th>認証</th>
                <th>アクション</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($members as $member)
                <tr>
                    <td class="center">
                        <button type="button" data-bs-toggle="modal" data-bs-target="#resModal{{ $member->id }}" class="border-0 bg-transparent p-0">
                            <i class="las la-comment-dots"></i>
                        </button>

@include('item.members.detail')

                    </td>
                    <td class="center"><p>[ {{ $member->id }} ]</p>{{ $member->kind == 0 ? '停止' : '' }}{{ $member->kind == 1 ? '通常' : '' }}{{ $member->kind == 2 ? 'プレミア' : '' }}</td>
                    <td class="center"><p>[ {{ $member->member_id }} ]</p>
                        <p>{{ $member->name }}</p>
                    </td>
                    <td>
                        <p>{{ $member->email }}</p>
                        <p>{{ $member->tel }}</p>
                    </td>
                    <td>
                        {{ $member->total_points }} pt
                    </td>
                    <td class="size_15">
                        {!! nl2br(e($member->memo)) !!}
                    </td>
                    <td class="size_15">
                        {!! nl2br(e($member->memo_share)) !!}
                    </td>
                    <td class="size_6">
                        <select class="check-select" data-member-id="{{ $member->id }}" id="colorSelect" onChange="changeColor(this)">
                            <option value="0" {{ $member->check == 0 ? 'selected' : '' }}>未認証</option>
                            <option value="1" {{ $member->check == 1 ? 'selected' : '' }}>認証</option>
                        </select>
                    </td>
                    <td class="action">
                        <a href="{{ route('item.members.edit', $member) }}" class="btn btn-warning">編集</a>
                        <form action="{{ route('item.members.destroy', $member) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button class="af-btn af-btn-delete af-btn-sm" onclick="return confirm('本当に削除しますか？')">削除</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>


</section>




<script>

$(document).ready(function() {
    $('.check-select').change(function() {
        var memberId = $(this).data('member-id');
        var newFlag = $(this).val();

        $.ajax({
            url: '/item/member/update-check',
            method: 'POST',
            data: {
                member_id: memberId,
                flag: newFlag,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if(response.success) {
                    alert('ステータスが更新されました');
                } else {
                    alert('ステータスの更新に失敗しました');
                }
            },
            error: function() {
                alert('エラーが発生しました');
            }
        });
    });
});


</script>
    <!-- Bootstrap JSをインクルード -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


</x-item-layout>
