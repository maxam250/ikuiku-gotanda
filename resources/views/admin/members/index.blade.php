
<x-admin-layout>


<x-slot name="header">
会員一覧
</x-slot>

<section class="toPageArea">
    <a href="{{ route('admin.members.create') }}" class="create">新しい会員を作成</a>
</section>

{{-- 検索フォーム --}}
<form method="GET" action="{{ route('admin.members.index') }}" class="af-search">
    <div class="af-search-row">
        <div class="af-search-field" style="min-width:60px; max-width:80px;">
            <label for="member_id">ID</label>
            <input type="number" class="af-input" id="member_id" name="member_id"
                   value="{{ request('member_id') }}" placeholder="ID">
        </div>
        <div class="af-search-field" style="min-width:90px; max-width:120px;">
            <label for="member_no">会員番号</label>
            <input type="text" class="af-input" id="member_no" name="member_no"
                   value="{{ request('member_no') }}" placeholder="会員番号">
        </div>
        <div class="af-search-field">
            <label for="name">名前</label>
            <input type="text" class="af-input" id="name" name="name"
                   value="{{ request('name') }}" placeholder="名前">
        </div>
        <div class="af-search-field">
            <label for="email">メール</label>
            <input type="email" class="af-input" id="email" name="email"
                   value="{{ request('email') }}" placeholder="メール">
        </div>
        <div class="af-search-field">
            <label for="tel">電話番号</label>
            <input type="tel" class="af-input" id="tel" name="tel"
                   value="{{ request('tel') }}" placeholder="電話番号">
        </div>
    </div>
    <div class="af-btn-row af-btn-row-center" style="margin-top:0.5rem;">
        <button type="submit" class="af-btn af-btn-search">
            <i class="fas fa-search"></i> 検索
        </button>
        <a href="{{ route('admin.members.index') }}" class="af-btn af-btn-reset">
            <i class="fas fa-redo"></i> リセット
        </a>
    </div>
</form>

@if(request('member_id') || request('member_no') || request('name') || request('email') || request('tel'))
<div class="af-alert-info" style="margin-top:0.5rem; display:flex; align-items:center; gap:0.4rem; flex-wrap:wrap;">
    <i class="fas fa-search"></i> 検索結果: {{ $members->total() }}件
    @if(request('member_id'))<span class="af-badge af-badge-on">ID: {{ request('member_id') }}</span>@endif
    @if(request('member_no'))<span class="af-badge af-badge-on">会員番号: {{ request('member_no') }}</span>@endif
    @if(request('name'))<span class="af-badge af-badge-on">名前: {{ request('name') }}</span>@endif
    @if(request('email'))<span class="af-badge af-badge-on">メール: {{ request('email') }}</span>@endif
    @if(request('tel'))<span class="af-badge af-badge-on">電話: {{ request('tel') }}</span>@endif
    <a href="{{ route('admin.members.index') }}" class="af-btn af-btn-cancel af-btn-sm">
        <i class="fas fa-times"></i> クリア
    </a>
</div>
@endif

<div class="py-4">
    {{ $members->links() }}
</div>

<section>

    @php
        $now = date('Y-m-d H:i:s');
    @endphp
    <table class="listTbl">
        <thead>
            <tr>
                <th>詳細</th>
                <th>種別</th>
                <th>名前/HN</th>
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
                        <button type="button" data-af-modal-open="#resModal{{ $member->id }}" aria-haspopup="dialog" style="border: none;background:transparent;cursor:pointer;padding:2px;font-size:inherit;">
                            <i class="las la-comment-dots"></i>
                        </button>

@include('admin.members.detail')

                    </td>
                    <td class="center"><p>[ {{ $member->id }} ]</p>{{ $member->kind == 0 ? '停止' : '' }}{{ $member->kind == 1 ? '通常' : '' }}{{ $member->kind == 2 ? 'プレミア' : '' }}</td>
                    <td class="center">{{-- <p>[ {{ $member->member_no }} ]</p>--}}
                        <p>{{ $member->name }}</p>
                        <p>[{{ $member->nickname }}]</p>
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
                        <select class="flag-select" data-member-id="{{ $member->id }}">
                            <option value="0" {{ $member->check == 0 ? 'selected' : '' }}>未認証</option>
                            <option value="1" {{ $member->check == 1 ? 'selected' : '' }}>認証</option>
                        </select>
                    </td>
                    <td class="action">
                        <div class="af-btn-row">
                            <a href="{{ route('admin.members.edit', $member) }}" class="af-btn af-btn-edit af-btn-sm">
                                <i class="fas fa-edit"></i> 編集
                            </a>
                            <form action="{{ route('admin.members.destroy', $member) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button class="af-btn af-btn-delete af-btn-sm" onclick="return confirm('本当に削除しますか？')">
                                    <i class="fas fa-trash"></i> 削除
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>


</section>

<div class="py-4">
    {{ $members->links() }}
</div>


<script>

$(document).ready(function() {
    $('.flag-select').change(function() {
        var memberId = $(this).data('member-id');
        var newFlag = $(this).val();

        $.ajax({
            url: '/kanrigamen/members/update-flag',
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

</x-admin-layout>
