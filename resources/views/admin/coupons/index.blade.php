<x-admin-layout>

<x-slot name="header">
クーポン管理
</x-slot>

<section class="modern-register-form">
    <div class="form-container">
        <h3>クーポン登録</h3>
        <p class="form-hint">新しいクーポンを作成します</p>
        <a href="{{ route('admin.coupons.create') }}" class="admin-btn admin-btn-submit">
            <i class="fas fa-plus"></i>新規作成
        </a>
    </div>
</section>

<section id="ListArea">
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- 検索フォーム（メッセージ管理と同様のスタイル） --}}
    <form method="GET" action="{{ route('admin.coupons.index') }}" class="af-search">
        <div class="af-search-row">
            <div class="af-search-field" style="min-width:160px;">
                <label for="coupon_search">タイトル・コード</label>
                <input type="text" name="search" id="coupon_search" class="af-input"
                       value="{{ request('search') }}" placeholder="キーワード">
            </div>
            <div class="af-search-field" style="min-width:100px; max-width:130px;">
                <label for="coupon_is_active">状態</label>
                <select name="is_active" id="coupon_is_active" class="af-select">
                    <option value="">すべて</option>
                    <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>有効</option>
                    <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>無効</option>
                </select>
            </div>
        </div>
        <div class="af-btn-row af-btn-row-center" style="margin-top:0.5rem;">
            <button type="submit" class="af-btn af-btn-search">
                <i class="fas fa-search"></i> 検索
            </button>
            <a href="{{ route('admin.coupons.index') }}" class="af-btn af-btn-reset">
                <i class="fas fa-redo"></i> リセット
            </a>
        </div>
    </form>

    <ul class="list">
        @forelse ($coupons as $coupon)
        <li class="box min-w-60
        @if($coupon->is_active)
         active
        @else
         passive
        @endif" data-id="{{ $coupon->id }}" style="min-width:360px;">
            <div class="img">
                <a href="{{ route('admin.coupons.show', $coupon) }}">
                    @if ($coupon->main_image)
                        <img src="{{ Storage::url($coupon->main_image) }}" alt="クーポン画像">
                    @else
                        <img src="{{ asset('images/dmy.jpg') }}" alt="デフォルト画像">
                    @endif
                </a>
            </div>
            <div class="name">
                <a href="{{ route('admin.coupons.show', $coupon) }}">
                    {{ $coupon->title }}
                    @if($coupon->code)
                        <br><small>コード: {{ $coupon->code }}</small>
                    @endif
                </a>
            </div>
            <div class="status">
                @if($coupon->is_active)
                    <span class="status-active">有効</span>
                @else
                    <span class="status-inactive">無効</span>
                @endif
                @if($coupon->expires_at)
                    <br><small>期限: {{ $coupon->expires_at->format('Y/m/d') }}</small>
                @endif
            </div>
            <div class="action-buttons">
                <a href="{{ route('admin.coupons.edit', $coupon) }}" class="admin-btn admin-btn-secondary admin-btn-sm">
                    <i class="fas fa-edit"></i>編集
                </a>
                <form action="{{ route('admin.coupons.toggle-status', $coupon) }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="admin-btn admin-btn-warning admin-btn-sm">
                        <i class="fas fa-toggle-{{ $coupon->is_active ? 'off' : 'on' }}"></i>
                        {{ $coupon->is_active ? '無効化' : '有効化' }}
                    </button>
                </form>
                <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST" style="display:inline;" onsubmit="return confirm('本当に削除しますか？')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="admin-btn admin-btn-danger admin-btn-sm">
                        <i class="fas fa-trash"></i>削除
                    </button>
                </form>
            </div>
        </li>
        @empty
        <li class="no-data">
            <p>クーポンが登録されていません</p>
        </li>
        @endforelse
    </ul>

    {{ $coupons->appends(request()->query())->links() }}
</section>

</x-admin-layout>

