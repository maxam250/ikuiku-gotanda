<x-admin-layout>

    <x-slot name="header">
    予約履歴一覧
    </x-slot>
    
    <section>
        {{-- 検索フォーム（メッセージ管理と同様のスタイル） --}}
        <form method="GET" action="{{ route('admin.histories.index') }}" class="af-search">
            <div class="af-search-row">
                <div class="af-search-field" style="min-width:130px; max-width:150px;">
                    <label for="hist_date_from">予約日（開始）</label>
                    <input type="date" name="date_from" id="hist_date_from" class="af-input"
                           value="{{ $searchParams['date_from'] ?? '' }}">
                </div>
                <div class="af-search-field" style="min-width:130px; max-width:150px;">
                    <label for="hist_date_to">予約日（終了）</label>
                    <input type="date" name="date_to" id="hist_date_to" class="af-input"
                           value="{{ $searchParams['date_to'] ?? '' }}">
                </div>
                <div class="af-search-field" style="min-width:110px; max-width:130px;">
                    <label for="hist_flag">ステータス</label>
                    <select name="flag" id="hist_flag" class="af-select">
                        <option value="">すべて</option>
                        <option value="0" {{ (isset($searchParams['flag']) && $searchParams['flag'] == '0') ? 'selected' : '' }}>未確認</option>
                        <option value="1" {{ (isset($searchParams['flag']) && $searchParams['flag'] == '1') ? 'selected' : '' }}>確認済み</option>
                        <option value="2" {{ (isset($searchParams['flag']) && $searchParams['flag'] == '2') ? 'selected' : '' }}>確定</option>
                        <option value="3" {{ (isset($searchParams['flag']) && $searchParams['flag'] == '3') ? 'selected' : '' }}>保留</option>
                        <option value="4" {{ (isset($searchParams['flag']) && $searchParams['flag'] == '4') ? 'selected' : '' }}>キャンセル</option>
                    </select>
                </div>
                <div class="af-search-field" style="min-width:140px; max-width:200px;">
                    <label for="hist_item_id">キャスト</label>
                    <select name="item_id" id="hist_item_id" class="af-select">
                        <option value="">すべて</option>
                        @foreach($items as $item)
                            <option value="{{ $item->id }}" {{ (isset($searchParams['item_id']) && $searchParams['item_id'] == $item->id) ? 'selected' : '' }}>
                                {{ $item->item_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="af-search-field" style="min-width:180px;">
                    <label for="hist_member_id">会員</label>
                    <select name="member_id" id="hist_member_id" class="af-select">
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

        @if($histories->count() > 0)
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
                                <td>
                                    @if($history->flag == 0)
                                        <span class="badge bg-warning">未確認</span>
                                    @elseif($history->flag == 1)
                                        <span class="badge bg-info">確認済み</span>
                                    @elseif($history->flag == 2)
                                        <span class="badge bg-success">確定</span>
                                    @elseif($history->flag == 3)
                                        <span class="badge bg-secondary">保留</span>
                                    @elseif($history->flag == 4)
                                        <span class="badge bg-danger">キャンセル</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $history->date->format('Y-m-d') }}<br>
                                    {{ $history->time_start->format('H:i') }} - {{ $history->time_end->format('H:i') }}
                                </td>
                                <td>{{ $history->name }}</td>
                                <td>
                                    @if($history->course_id)
                                        @php
                                            $course = $courses->firstWhere('id', $history->course_id);
                                        @endphp
                                        {{ $course ? $course->course_name : 'N/A' }}
                                    @endif
                                </td>
                                <td>{{ $history->item_name }}</td>
                                <td>{{ Str::limit($history->memo, 30) }}</td>
                                <td>{{ Str::limit($history->memo_share, 30) }}</td>
                                <td>{{ $history->place }}</td>
                                <td>¥{{ number_format($history->fee_total) }}</td>
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

            <!-- ページネーション -->
            <div class="d-flex justify-content-center mt-4">
                {{ $histories->appends(request()->query())->links() }}
            </div>
        @else
            <div class="alert alert-info text-center">
                @if(request()->has('date_from') || request()->has('date_to') || request()->has('flag') || request()->has('item_id') || request()->has('member_id'))
                    検索条件に該当する予約履歴が見つかりませんでした。
                @else
                    検索条件を指定して予約履歴を表示してください。
                @endif
            </div>
        @endif
    </section>
    
    </x-admin-layout> 