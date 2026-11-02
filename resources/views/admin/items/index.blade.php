
<x-admin-layout>


<x-slot name="header">
キャスト一覧
</x-slot>

<div class="flex items-center justify-start gap-6 mb-4">

<section class="modern-register-form">
    <div class="form-container">
        <h3>キャスト登録</h3>
        <p class="form-hint">名前を登録すると非表示状態で登録されます。詳細は登録後に編集できます</p>

        <form method="post" action="{{ route('admin.items.store') }}" class="inline-form">
            @csrf
            <div class="form-row">
                <input type="text" name="item_name" id="name" class="modern-input" required placeholder="キャスト名" style="width:240px;">
                <button type="submit" class="admin-btn admin-btn-submit">
                    <i class="fas fa-plus"></i>登録
                </button>
            </div>
        </form>
    </div>
</section>


{{-- ── 一括処理フォーム ──────────────────────────────── --}}
<section class="modern-register-form">
    <div class="form-container">
        <h3>一括処理（更新のないキャストの設定）</h3>
        @if(session('success'))
            <div style="color:#16a34a;font-weight:600;margin-bottom:0.5rem;"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
        @endif
        @if(session('info'))
            <div style="color:#6b7280;margin-bottom:0.5rem;"><i class="fas fa-info-circle"></i> {{ session('info') }}</div>
        @endif
            <p class="form-hint" style="color:#dc2626;margin-top:0.5rem;">
                <i class="fas fa-exclamation-circle"></i> 出勤やプロフの更新のないキャストをまとめて非公開または削除できます。削除は復元できないので注意してください。
            </p>
        <form method="POST" action="{{ route('admin.items.bulkAction') }}" onsubmit="return confirmBulkAction(this)" class="inline-form">
            @csrf
            <div class="form-row" style="flex-wrap:wrap;align-items:center;gap:0.6rem;">
                <input type="date" name="before_date" class="modern-input" required style="width:160px;flex:none;">
                <span style="font-size:0.875rem;white-space:nowrap;">より以前のものを</span>
                <select name="action" style="width:130px;flex:none;padding:0.5rem 0.6rem;border:1px solid #d1d5db;border-radius:0.375rem;font-size:0.875rem;">
                    <option value="hide">非公開にする</option>
                    <option value="delete">削除する</option>
                </select>
                <button type="submit" class="admin-btn admin-btn-warning" style="white-space:nowrap;">
                    <i class="fas fa-exclamation-triangle"></i> 更新
                </button>
            </div>
        </form>
    </div>
</section>
</div>

<section id="ListArea">

    {{-- タブ --}}
    <div class="items-tab-bar">
        <a href="{{ route('admin.items.index', ['tab' => 'all',     'search' => $search]) }}"
           class="items-tab-btn {{ $tab === 'all'     ? 'is-active' : '' }}">
            全て（{{ $totalCount }}）
        </a>
        <a href="{{ route('admin.items.index', ['tab' => 'active',  'search' => $search]) }}"
           class="items-tab-btn {{ $tab === 'active'  ? 'is-active' : '' }}">
            公開中（{{ $activeCount }}）
        </a>
        <a href="{{ route('admin.items.index', ['tab' => 'passive', 'search' => $search]) }}"
           class="items-tab-btn {{ $tab === 'passive' ? 'is-active' : '' }}">
            非表示（{{ $passiveCount }}）
        </a>

        {{-- 名前検索 --}}
        <form method="GET" action="{{ route('admin.items.index') }}" class="items-search-form">
            <input type="hidden" name="tab" value="{{ $tab }}">
            <input type="text" name="search" value="{{ $search }}" placeholder="名前検索" class="items-search-input">
            @if($search)
                <a href="{{ route('admin.items.index', ['tab' => $tab]) }}" class="items-search-clear" title="クリア">✕</a>
            @endif
            <button type="submit" class="items-search-btn"><i class="fas fa-search"></i></button>
        </form>

        <div class="item-icon-row ml-6">
            <span class="item_url"><i class="fas fa-camera" title="写メ日記"></i></span>…写メ日記&emsp;
            <span class="item_twitter"><i class="fab fa-x-twitter" title="X(Twitter)"></i></span>…X(Twitter)&emsp;
            <span class="item_video"><i class="fas fa-video" title="動画"></i></span>…動画&emsp;
            <span class="loginid"><i class="fas fa-key" title="ログインID設定済"></i></span>…マイページ
        </div>
    </div>

{{-- ページネーション（上） --}}
<div class="pagination-wrap">
    {{ $items->links('vendor.pagination.admin-items') }}
    <span class="pagination-info">全{{ $totalCount }}件 / {{ $items->currentPage() }}ページ目（{{ $items->firstItem() }}〜{{ $items->lastItem() }}件）</span>
</div>

<ul class="list sortable-list">
    @foreach ($items as $item)
    <li class="box {{ $item->item_active == 1 ? 'active' : 'passive' }}" data-id="{{ $item->id }}">
        <div class="img">
            <a href="{{ route('admin.items.edit', $item) }}">
                @if ($item->thumbnail)
                    <img src="{{ Storage::url($item->thumbnail->filename) }}" alt="{{ $item->item_name }}">
                @else
                    <img src="{{ asset('images/dmy.jpg') }}" alt="">
                @endif
            </a>
        </div>
        <div class="name"><a href="{{ route('admin.items.edit', $item) }}">{{ $item->item_name }}({{ $item->item_age }})</a></div>

        {{-- アイコン行 --}}
        <div class="item-icon-row">
            @if($item->item_url)
                <span class="item_url"><i class="fas fa-camera" title="写メ日記"></i></span>
            @endif
            @if($item->item_twitter)
                <span class="item_twitter"><i class="fab fa-x-twitter" title="X(Twitter)"></i></span>
            @endif
            @if($item->item_video)
                <span class="item_video"><i class="fas fa-video" title="動画"></i></span>
            @endif
            @if($item->loginid)
                <span class="loginid"><i class="fas fa-key" title="ログインID設定済"></i></span>
            @endif
        </div>

        <div class="action-buttons">
            <a href="{{ route('admin.items.edit', $item) }}" class="admin-btn admin-btn-secondary admin-btn-xs">
                <i class="fas fa-edit"></i>編集
            </a>
            <form action="{{ route('admin.items.toggleActive', $item) }}" method="POST">
                @csrf
                @if($item->item_active == 1)
                    <button class="admin-btn admin-btn-info admin-btn-xs">
                        <i class="fas fa-eye-slash"></i>非公開
                    </button>
                @else
                    <button class="admin-btn admin-btn-primary admin-btn-xs">
                        <i class="fas fa-eye"></i>公開
                    </button>
                @endif
            </form>
        </div>
    </li>
    @endforeach
</ul>

{{-- ページネーション（下） --}}
<div class="pagination-wrap">
    {{ $items->links('vendor.pagination.admin-items') }}
</div>

</section>



<script>
    // ── 一括処理確認アラート ──────────────────────────
    function confirmBulkAction(form) {
        const action = form.querySelector('[name="action"]').value;
        const date   = form.querySelector('[name="before_date"]').value;
        const label  = date.replace(/-/g, '/');
        if (action === 'delete') {
            return confirm(
                label + ' より以前のキャストを削除します。\n\n' +
                '⚠️ この操作は復元できません。\n本当に実行しますか？'
            );
        } else {
            return confirm(label + ' より以前のキャストを非公開にします。\n\n実行しますか？');
        }
    }

    $(function() {
        var $sortableList = $('.sortable-list');

        // ── ドラッグ並び替え ─────────────────────────
        $sortableList.sortable({
            cancel: 'input,textarea,button,select,option,form',
            delay: 50,
            distance: 3,
            scrollSpeed: 25,
            scrollSensitivity: 50,
            placeholder: 'sortable-placeholder',
            tolerance: 'pointer',
            update: function() {
                var order = [];
                $(this).find('> li').each(function(index) {
                    order.push({
                        id: $(this).attr('data-id'),
                        item_sort: index + 1
                    });
                });

                $.ajax({
                    url: '{{ route('admin.items.sort') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        order: order
                    },
                    success: function(response) {
                        if (!response || !response.success) {
                            alert('並び替えの保存に失敗しました');
                        }
                    },
                    error: function(xhr) {
                        console.error('Sort error:', xhr.status, xhr.responseText);
                        alert('並び替えの保存中にエラーが発生しました（' + xhr.status + '）');
                    }
                });
            }
        });
    });
</script>

</x-admin-layout>
