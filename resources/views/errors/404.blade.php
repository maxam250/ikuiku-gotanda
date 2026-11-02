@extends('layouts.app', ['forceNoindex' => true])

@section('content')
<section id="error404Area" class="err404">
    <div class="err404-inner">
        <p class="err404-code" aria-hidden="true">404</p>
        <div class="err404-divider" aria-hidden="true"></div>
        <h1 class="err404-title">お探しのページは見つかりませんでした</h1>
        <p class="err404-text">
            URL が間違っているか、ページが移動または削除された可能性があります。
        </p>
        <div class="err404-actions">
            <a href="{{ url('/') }}" class="err404-btn err404-btn--primary">
                <i class="fas fa-home" aria-hidden="true"></i>
                <span>トップページへ</span>
            </a>
            <a href="{{ route('item.index') }}" class="err404-btn err404-btn--ghost">
                <i class="fas fa-users" aria-hidden="true"></i>
                <span>女の子一覧</span>
            </a>
        </div>
    </div>
</section>

<style>
.err404 {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 52vh;
    padding: 4rem 1.25rem 5rem;
}
.err404-inner {
    width: 100%;
    max-width: 480px;
    text-align: center;
}
.err404-code {
    font-size: clamp(4.5rem, 18vw, 7.5rem);
    font-weight: 800;
    line-height: 1;
    letter-spacing: 0.04em;
    margin: 0;
    color: transparent;
    background: linear-gradient(180deg, #ffffff 0%, #b9b9c8 55%, #6b6b7a 100%);
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
}
.err404-divider {
    width: 48px;
    height: 3px;
    margin: 1.5rem auto 1.75rem;
    border-radius: 3px;
    background: var(--color-primary, #ff2d78);
}
.err404-title {
    font-size: clamp(1rem, 4vw, 1.25rem);
    font-weight: 700;
    letter-spacing: 0.02em;
    color: var(--ui-text, #ededf5);
    margin: 0 0 0.9rem;
    line-height: 1.6;
}
.err404-text {
    font-size: 0.85rem;
    line-height: 1.9;
    color: #8f8f9e;
    margin: 0 0 2.25rem;
}
.err404-actions {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 0.75rem;
}
.err404-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    min-width: 168px;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-size: 0.875rem;
    font-weight: 600;
    letter-spacing: 0.02em;
    text-decoration: none;
    transition: background-color 0.25s ease, border-color 0.25s ease, color 0.25s ease, transform 0.15s ease;
}
.err404-btn i { font-size: 0.8rem; }
.err404-btn:hover { transform: translateY(-1px); }
.err404-btn--primary {
    background: var(--color-primary, #ff2d78);
    color: #fff;
    border: 1px solid var(--color-primary, #ff2d78);
}
.err404-btn--primary:hover {
    background: #c40041;
    border-color: #c40041;
    color: #fff;
}
.err404-btn--ghost {
    background: transparent;
    color: #cfcfda;
    border: 1px solid #2c2c38;
}
.err404-btn--ghost:hover {
    border-color: var(--color-primary, #ff2d78);
    color: #fff;
}
@media (max-width: 480px) {
    .err404-btn { flex: 1 1 100%; }
}
</style>
@endsection
