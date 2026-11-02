<x-admin-layout>


<x-slot name="header">
予約一覧
</x-slot>

<section class="toPageArea">
    <a href="{{ route('admin.reservations.create') }}" class="create">新しい予約を作成</a>
</section>

<section>
    @php
        $now = date('Y-m-d H:i:s');
    @endphp
    <form method="GET" action="{{ route('admin.reservations.index') }}" class="af-search">
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
            <a href="{{ route('admin.reservations.index') }}" class="af-btn af-btn-reset">
                <i class="fas fa-redo"></i> リセット
            </a>
            <a href="{{ route('admin.reservations.print') }}?{{ http_build_query(array_filter($searchParams ?? [], fn($v) => $v !== null && $v !== '')) }}"
               target="_blank"
               class="af-btn"
               style="background:#555;color:#fff;border-color:#555;">
                <i class="fas fa-print"></i> 印刷
            </a>
        </div>
    </form>

    <style>
    .reservation-list-table .reservation-kind-col {
        min-width: 96px;
        font-size: 0.92rem;
        font-weight: 600;
    }
    .reservation-list-table .reservation-kind-col p {
        font-size: 0.86rem;
        margin-bottom: 0.15rem;
    }
    .reservation-list-table .action-col {
        width: 200px;
        min-width: 200px;
        max-width: 200px;
    }
    .reservation-list-table .admin-btn-group {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.2rem;
    }
    .reservation-list-table .admin-btn-group .admin-btn,
    .reservation-list-table .admin-btn-group form .admin-btn {
        padding: 0.16rem 0;
        font-size: 0.68rem;
        line-height: 1.05;
        min-height: 22px;
        width: 100%;
        text-align: center;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .reservation-list-table .admin-btn-group .admin-btn i {
        margin-right: 0.15rem;
        font-size: 0.62rem;
    }
    .reservation-list-table .admin-btn-group form {
        display: contents;
    }
    </style>

    <table class="listTbl reservation-list-table">
        <thead>
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
                <th>フラグ</th>
                <th class="action-col">アクション</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($reservations as $reservation)
                <tr>
                    <td class="center">
                        <button type="button" data-af-modal-open="#resModal{{ $reservation->id }}" aria-haspopup="dialog" style="border: none;background:transparent;cursor:pointer;padding:2px;font-size:inherit;">
                            <i class="las la-comment-dots"></i>
                        </button>

@include('admin.reservations.detail')

                    </td>
                    <td class="center reservation-kind-col"><p>ID:{{ $reservation->id }}</p>{{ $reservation->kind == 1 ? '通常' : 'リクエスト' }}</td>
                    <td>
                        <p>{{ $reservation->date->isoFormat('YYYY/MM/DD(ddd)') }}
                         </p>
                        <p>
                        {{ $reservation->time_start ? $reservation->time_start->format('H:i') : ''}} -
                        {{ $reservation->time_end ? $reservation->time_end->format('H:i') : '' }}
                        </p>
                    </td>
                    <td class="center"><p>[ {{ $reservation->member_id }} ]</p>
                        <p class="whitespace-nowrap">{{ $reservation->name }}</p>
                    </td>
                    <td>
                        @foreach($courses as $course)
                            <span class="whitespace-nowrap">{{ $course->id == $reservation->course_id ? $course->title : '' }}</span>
                        @endforeach
                        {{ $reservation->course_time ? $reservation->course_time : '-' }}分
                        {{-- @foreach($course_fees as $course_fee)
                            {{ $course_fee->id == $reservation->course_time ? $course_fee->course : '' }}
                        @endforeach --}}
                    </td>
                    <td>
                        {{ $reservation->item_name }}
                        @if ($reservation->item_name_other)
                        <p>{{ $reservation->item_name_other }}</p>
                        @endif
                    </td>
                    <td class="size_15">
                        {!! nl2br(e($reservation->memo)) !!}
                    </td>
                    <td class="size_15">
                        {!! nl2br(e($reservation->memo_share)) !!}
                    </td>
                    <td>
                        @if ($reservation->course_id != 3)
                        @foreach($equipments as $equipment)
                            @php
                                $equipmentId = $equipment->external_id ?? $equipment->id;
                            @endphp
                            <span class="whitespace-nowrap">{{ $equipmentId == $reservation->equipment_id ? $equipment->title : '' }}</span>
                        @endforeach
                        @else
                            {{ $reservation->area }} / {{ $reservation->place }} / {{ $reservation->address }}
                        @endif
                    </td>
                    <td>
                        <p class="whitespace-nowrap text-base font-semibold">{{ number_format($reservation->fee_total) }}円</p>
                        <p class="whitespace-nowrap text-xs text-gray-600">税込 {{ number_format($reservation->fee_total * config('tax.intax')) }}円</p>
                    </td>
                    <td class="size_6">
                        <select class="flag-select" data-reservation-id="{{ $reservation->id }}" id="colorSelect" onChange="changeColor(this)">
                            <option value="0" {{ $reservation->flag == 0 ? 'selected' : '' }}>未設定</option>
                            <option value="1" {{ $reservation->flag == 1 ? 'selected' : '' }}>確認中</option>
                            <option value="2" {{ $reservation->flag == 2 ? 'selected' : '' }}>確定</option>
                            <option value="3" {{ $reservation->flag == 3 ? 'selected' : '' }}>保留</option>
                            <option value="4" {{ $reservation->flag == 4 ? 'selected' : '' }}>キャンセル</option>
                        </select>
                    </td>
                    <td class="action-col">
                        <div class="admin-btn-group">
                            {{-- 1行目: 編集 | メッセージ --}}
                            <a href="{{ route('admin.reservations.edit', $reservation->id) }}" class="admin-btn admin-btn-secondary admin-btn-base">
                                <i class="fas fa-edit"></i>編集
                            </a>
                            <a href="{{ $reservation->member_id
                                        ? route('admin.messages.show', $reservation->member_id)
                                        : route('admin.messages.show.guest', urlencode($reservation->email)) }}"
                            class="admin-btn admin-btn-secondary admin-btn-base bg-teal-600 border-0">
                                <i class="fa-solid fa-message"></i>メッセージ
                            </a>
                            {{-- 2行目: 削除 | 印刷 --}}
                            <form action="{{ route('admin.reservations.destroy', $reservation->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="admin-btn admin-btn-warning admin-btn-base" onclick="return confirm('本当に削除しますか？')">
                                    <i class="fas fa-trash"></i>削除
                                </button>
                            </form>
                            <a href="{{ route('admin.reservations.print') }}?single_id={{ $reservation->id }}"
                               target="_blank"
                               class="admin-btn admin-btn-base"
                               style="background:#2e7d32;color:#fff;border-color:#2e7d32;">
                                <i class="fas fa-print"></i>印刷
                            </a>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</section>


<script>
function changeColor(selectObj) {
    const color = "";
    switch(selectObj.value) {
        case "0":
            color = "#f0f0f0";
            break;
        case "1":
            color = "#ffe2e2";
            break;
        case "2":
            color = "#d5ffd5";
            break;
        case "3":
            color = "#eeeeff";
            break;
        case "4":
            color = "#fffac6";
            break;
        default:
            color = "#ffffff"; // Default to white
    }
    selectObj.style.backgroundColor = color;
}


$(document).ready(function() {
    $('.flag-select').change(function() {
        var reservationId = $(this).data('reservation-id');
        var newFlag = $(this).val();

        $.ajax({
            url: '/kanrigamen/reservation/update-flag',
            method: 'POST',
            data: {
                reservation_id: reservationId,
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
    const memberSearch = document.getElementById('memberSearch');
    const memberId = document.getElementById('memberId');
    const memberSearchResults = document.getElementById('memberSearchResults');
    const clearMemberSearch = document.getElementById('clearMemberSearch');
    let searchTimeout;

    // CSRFトークンを取得
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    memberSearch.addEventListener('input', function() {
        const keyword = this.value.trim();

        // 2文字以上で検索開始
        if (keyword.length >= 2) {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                fetch(`/api/members/search?keyword=${encodeURIComponent(keyword)}`, {
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    memberSearchResults.innerHTML = '';
                    if (data.length > 0) {
                        data.forEach(member => {
                            const div = document.createElement('div');
                            div.className = 'list-group-item list-group-item-action py-2';
                            div.innerHTML = `
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="font-weight-bold">${member.name}</div>
                                    <small class="text-muted">ID: ${member.id}</small>
                                </div>
                                <div class="small text-muted">
                                    電話番号: ${member.tel}<br>
                                    会員番号: ${member.member_no}
                                </div>
                            `;
                            div.addEventListener('click', () => {
                                memberSearch.value = member.name;
                                memberId.value = member.id;
                                memberSearchResults.style.display = 'none';
                            });
                            memberSearchResults.appendChild(div);
                        });
                        memberSearchResults.style.display = 'block';
                    } else {
                        memberSearchResults.style.display = 'none';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    memberSearchResults.style.display = 'none';
                });
            }, 300);
        } else {
            memberSearchResults.style.display = 'none';
        }
    });

    // クリアボタンの処理
    clearMemberSearch.addEventListener('click', function(e) {
        e.preventDefault();
        memberSearch.value = '';
        memberId.value = '';
        memberSearchResults.style.display = 'none';
    });

    // 外側クリックで結果を非表示
    document.addEventListener('click', function(e) {
        if (!memberSearch.contains(e.target) && !memberSearchResults.contains(e.target)) {
            memberSearchResults.style.display = 'none';
        }
    });
});

</script>


</x-admin-layout>
