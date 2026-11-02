<x-admin-layout>

<x-slot name="header">
予約履歴一覧
</x-slot>

<section>
    @php
        $now = date('Y-m-d H:i:s');
    @endphp
    <form method="GET" action="{{ route('admin.histories.index') }}" class="af-search">
        <div class="af-search-row">
            <div class="af-search-field" style="min-width:130px;">
                <label>予約日</label>
                <input type="date" name="date" class="af-input" value="{{ $searchParams['date'] ?? '' }}">
            </div>
            <div class="af-search-field" style="min-width:110px;">
                <label>ステータス</label>
                <select name="flag" class="af-select">
                    <option value="">すべて</option>
                    <option value="0" {{ (isset($searchParams['flag']) && $searchParams['flag'] == '0') ? 'selected' : '' }}>未確認</option>
                    <option value="1" {{ (isset($searchParams['flag']) && $searchParams['flag'] == '1') ? 'selected' : '' }}>確認済み</option>
                    <option value="2" {{ (isset($searchParams['flag']) && $searchParams['flag'] == '2') ? 'selected' : '' }}>確定</option>
                    <option value="3" {{ (isset($searchParams['flag']) && $searchParams['flag'] == '3') ? 'selected' : '' }}>保留</option>
                    <option value="4" {{ (isset($searchParams['flag']) && $searchParams['flag'] == '4') ? 'selected' : '' }}>キャンセル</option>
                </select>
            </div>
            <div class="af-search-field" style="min-width:140px;">
                <label>キャスト</label>
                <select name="item_id" class="af-select">
                    <option value="">すべて</option>
                    @foreach($items as $item)
                        <option value="{{ $item->id }}" {{ (isset($searchParams['item_id']) && $searchParams['item_id'] == $item->id) ? 'selected' : '' }}>
                            {{ $item->item_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="af-search-field" style="min-width:180px;">
                <label>会員</label>
                <select name="member_id" class="af-select">
                    <option value="">すべて</option>
                    @foreach($members as $member)
                        <option value="{{ $member->id }}" {{ (isset($searchParams['member_id']) && $searchParams['member_id'] == $member->id) ? 'selected' : '' }}>
                            {{ $member->member_no }} - {{ $member->name }} ({{ $member->tel }})
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="af-btn-row af-btn-row-center" style="margin-top:0.5rem;">
            <button type="submit" class="af-btn af-btn-search">
                <i class="fas fa-search"></i> 検索
            </button>
            <a href="{{ route('admin.histories.index') }}" class="af-btn af-btn-reset">
                <i class="fas fa-redo"></i> リセット
            </a>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="thead-light">
                <tr>
                    <th>詳細</th>
                    <th>種別</th>
                    <th>日時</th>
                    <th>予約名</th>
                    <th>コース</th>
                    <th>キャスト</th>
                    <th>メモ</th>
                    <th>メモ共有</th>
                    <th>場所</th>
                    <th>料金</th>
                    <th>アクション</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($histories as $history)
                    <tr>
                        <td class="center">
                            <button type="button" data-af-modal-open="#resModal{{ $history->id }}" aria-haspopup="dialog" style="border: none;background:transparent;cursor:pointer;padding:2px;font-size:inherit;">
                                <i class="las la-comment-dots"></i>
                            </button>

@include('admin.histories.detail')

                        </td>
                        <td class="center"><p>[ {{ $history->id }} ]</p>{{ $history->kind == 1 ? '通常' : 'リクエスト' }}</td>
                        <td>
                            <p>{{ $history->date->format('Y/m/d').'('. $history->date->isoFormat('ddd'). ')' }}
                             </p>
                            <p>
                            {{ $history->time_start ? $history->time_start->format('H:i') : ''}} -
                            {{ $history->time_end ? $history->time_end->format('H:i') : '' }}
                            </p>
                        </td>
                        <td class="center"><p>[ {{ $history->member_id }} ]</p>
                            <p>{{ $history->name }}</p>
                        </td>
                        <td>
                            @foreach($courses as $course)
                                {{ $course->id == $history->course_id ? $course->title : '' }}
                            @endforeach

                            @foreach($course_fees as $course_fee)
                                {{ $course_fee->id == $history->course_time ? $course_fee->course : '' }}
                            @endforeach
                        </td>
                        <td>
                            {{ $history->item_name }}
                        </td>
                        <td class="size_15">
                            {!! nl2br(e($history->memo)) !!}
                        </td>
                        <td class="size_15">
                            {!! nl2br(e($history->memo_share)) !!}
                        </td>
                        <td>
                            {{ $history->area }} / {{ $history->place }} / {{ $history->address }}
                        </td>
                        <td>
                            {{ number_format($history->fee_total) }}円
                        </td>
                        <td>
                            <div class="admin-btn-group">
                                <a href="{{ route('admin.histories.edit', $history->id) }}" class="admin-btn admin-btn-secondary admin-btn-sm">
                                    <i class="fas fa-edit"></i>編集
                                </a>
                                <form action="{{ route('admin.histories.destroy', $history->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('本当に削除しますか？');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="admin-btn admin-btn-warning admin-btn-sm">
                                        <i class="fas fa-trash"></i>削除
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</section>

<script>
function changeColor(selectObj) {
    const color = "";
    switch(selectObj.value) {
        case "0":
            color = "#f0f0f0"; //
            break;
        case "1":
            color = "#ff0000"; // Red
            break;
        case "2":
            color = "#00ff00"; // Green
            break;
        case "3":
            color = "#0000ff"; // Blue
            break;
        default:
            color = "#ffffff"; // Default to white
    }
    selectObj.style.backgroundColor = color;
}

$(document).ready(function() {
    $('.flag-select').change(function() {
        var historyId = $(this).data('history-id');
        var newFlag = $(this).val();

        $.ajax({
            url: '{{ route("admin.histories.updateFlag") }}',
            method: 'POST',
            data: {
                history_id: historyId,
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

document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('memberSearch');
    const searchResults = document.getElementById('memberSearchResults');
    let searchTimeout;
    if (!searchInput || !searchResults) return;

    searchInput.addEventListener('input', function() {
        const keyword = this.value.trim();
        
        // 検索結果をクリア
        searchResults.innerHTML = '';
        searchResults.style.display = 'none';

        if (keyword.length === 0) return;

        // デバウンス処理
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            fetch(`/api/members/search?keyword=${encodeURIComponent(keyword)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.length === 0) {
                        searchResults.innerHTML = '<div class="list-group-item">該当なし</div>';
                    } else {
                        searchResults.innerHTML = data.map(member => `
                            <a href="#" class="list-group-item list-group-item-action" 
                               data-member-id="${member.id}"
                               data-member-name="${member.name}">
                                ${member.member_number} - ${member.name} (${member.telephone})
                            </a>
                        `).join('');
                    }
                    searchResults.style.display = 'block';
                })
                .catch(error => {
                    console.error('Error:', error);
                    searchResults.innerHTML = '<div class="list-group-item text-danger">エラーが発生しました</div>';
                    searchResults.style.display = 'block';
                });
        }, 300);
    });

    // 検索結果のクリックイベント
    searchResults.addEventListener('click', function(e) {
        e.preventDefault();
        const item = e.target.closest('.list-group-item');
        if (!item) return;

        const memberId = item.dataset.memberId;
        const memberName = item.dataset.memberName;
        
        document.getElementById('memberId').value = memberId;
        searchInput.value = memberName;
        searchResults.style.display = 'none';
    });

    // 検索結果の外側をクリックした時に非表示
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.style.display = 'none';
        }
    });
});
</script>

</x-admin-layout> 