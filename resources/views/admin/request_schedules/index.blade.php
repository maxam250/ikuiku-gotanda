
<x-admin-layout>


<x-slot name="header">
出勤リクエスト
</x-slot>


<section id="itemList">
    @if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if ($requests->isNotEmpty())
<div class="bulk-actions" style="margin-bottom: 1rem;">
    <form method="POST" action="{{ route('admin.request_schedules.approve_bulk') }}" style="display:inline;">
        @csrf
        @foreach ($requests as $req)
            <input type="hidden" name="schedule_ids[]" value="{{ $req->id }}">
        @endforeach
        <button type="submit" class="btn" onclick="return confirm('表示中の {{ $requests->count() }} 件をすべて承認します。よろしいですか？');">一括承認</button>
    </form>
</div>
@endif

<table>
    <tr>
        <th>キャスト名</th>
        <th>日付</th>
        <th>申請内容</th>
        <th>申請時間</th>
        <th>操作</th>
    </tr>
    @foreach ($requests as $req)
        <tr>
            <td><img src="{{ $req->itemImage && $req->itemImage->filename
                ? Storage::url( $req->itemImage->filename)
                : asset('images/dmy.jpg') }}"
                alt="キャスト画像" style="display:inline-block;width:50px; margin-right:1rem;">
            <span display:inline-block;>{{ $req->item->item_name ?? '未登録' }}</span></td>            
            <td class="text-center size_s">{{ $req->date }}</td>
            <td class="text-center">
                @if ($req->flag == 1 || $req->flag == 4) <div class="request add">出勤申請</div>
                @elseif ($req->flag == 2) <div class="request delete">削除申請</div>
                @elseif ($req->flag == 3) <div class="request update">変更申請</div>
                @endif
            </td>
            <td class="text-center"><p>{{ substr($req->time_start, 0, 5) }}～ {{ substr($req->time_end, 0, 5) }}</p>
                <p>▼</p>
                <p>
                {{ substr($req->request_time_start ?? $req->time_start, 0, 5) }} ～ {{ substr($req->request_time_end ?? $req->time_end, 0, 5) }}</p>
            </td>
            <td class="text-center size_s">
                <form method="POST" action="{{ route('admin.request_schedules.approve') }}" style="display:inline">
                    @csrf
                    <input type="hidden" name="schedule_id" value="{{ $req->id }}">
                    <button type="submit" class="btn">承認</button>
                </form>
                <form method="POST" action="{{ route('admin.request_schedules.reject') }}" style="display:inline">
                    @csrf
                    <input type="hidden" name="schedule_id" value="{{ $req->id }}">
                    <button type="submit" class="btn">却下</button>
                </form>
            </td>
        </tr>
    @endforeach
</table>
</section>


</x-admin-layout>
