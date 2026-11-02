<x-admin-layout>
    <x-slot name="header">
        ポイント管理
    </x-slot>

    <section>
        <form method="GET" action="{{ route('admin.member_points.index') }}" class="af-search">
            <div class="af-search-row">
                <div class="af-search-field">
                    <label>会員名</label>
                    <input type="text" name="name" class="af-input" value="{{ $searchParams['name'] ?? '' }}" placeholder="会員名で検索">
                </div>
                <div class="af-search-field">
                    <label>電話番号</label>
                    <input type="text" name="tel" class="af-input" value="{{ $searchParams['tel'] ?? '' }}" placeholder="電話番号で検索">
                </div>
                <div class="af-search-field" style="min-width:80px; max-width:100px;">
                    <label>会員ID</label>
                    <input type="text" name="member_id" class="af-input" value="{{ $searchParams['member_id'] ?? '' }}" placeholder="ID">
                </div>
                <div class="af-search-field" style="min-width:90px; max-width:120px;">
                    <label>会員番号</label>
                    <input type="text" name="member_no" class="af-input" value="{{ $searchParams['member_no'] ?? '' }}" placeholder="会員番号">
                </div>
            </div>
            <div class="af-btn-row af-btn-row-center" style="margin-top:0.5rem;">
                <button type="submit" class="af-btn af-btn-search">
                    <i class="fas fa-search"></i> 検索
                </button>
                <a href="{{ route('admin.member_points.index') }}" class="af-btn af-btn-reset">
                    <i class="fas fa-redo"></i> リセット
                </a>
            </div>
        </form>

        <div class="af-section" style="margin-top:0.75rem;">
            <div class="af-section-header">
                <h5 class="af-section-title">会員一覧</h5>
            </div>
            <div class="af-section-body" style="padding:0;">
                @if(isset($members) && $members->count() > 0)
                    <div class="af-table-wrap">
                        <table class="af-table">
                            <thead>
                                <tr>
                                    <th style="width:140px;"></th>
                                    <th>会員名</th>
                                    <th>電話番号</th>
                                    <th>現在のポイント</th>
                                    <th>最終更新日</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($members as $member)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.member_points.edit', $member->id) }}" class="af-btn af-btn-edit af-btn-sm">
                                                ポイント管理
                                            </a>
                                        </td>
                                        <td>{{ $member->name }}</td>
                                        <td>{{ $member->tel }}</td>
                                        <td>
                                            @if($member->point > 0)
                                                <strong>{{ number_format($member->point) }}</strong> pt
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ $member->updated_at->format('Y/m/d H:i') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div style="padding:0.75rem;">{{ $members->links() }}</div>
                @else
                    <div class="af-alert af-alert-success" style="margin:0.75rem;">会員が見つかりませんでした。</div>
                @endif
            </div>
        </div>
    </section>
</x-admin-layout>