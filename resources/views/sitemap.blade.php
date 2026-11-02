<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

    {{-- 年齢確認ページ --}}
    <url>
        <loc>{{ url('/') }}</loc>
        <changefreq>weekly</changefreq>
        <priority>1.0</priority>
    </url>

    {{-- トップページ --}}
    <url>
        <loc>{{ url('/top') }}</loc>
        <changefreq>weekly</changefreq>
        <priority>1.0</priority>
    </url>

    {{-- キャスト一覧 --}}
    <url>
        <loc>{{ url('/items') }}</loc>
        <changefreq>daily</changefreq>
        <priority>0.9</priority>
    </url>

    {{-- キャスト詳細 --}}
    @foreach($items as $item)
    <url>
        <loc>{{ url('/items/' . $item->id) }}</loc>
        <lastmod>{{ $item->updated_at->toAtomString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
    @endforeach

    {{-- 新人キャスト --}}
    <url>
        <loc>{{ url('/newface') }}</loc>
        <changefreq>daily</changefreq>
        <priority>0.8</priority>
    </url>

    {{-- 動画 --}}
    <url>
        <loc>{{ url('/movie') }}</loc>
        <changefreq>weekly</changefreq>
        <priority>0.7</priority>
    </url>

    {{-- X（Twitter） --}}
    <url>
        <loc>{{ url('/x') }}</loc>
        <changefreq>daily</changefreq>
        <priority>0.7</priority>
    </url>

    {{-- スケジュール --}}
    <url>
        <loc>{{ url('/schedule') }}</loc>
        <changefreq>daily</changefreq>
        <priority>0.8</priority>
    </url>

    {{-- ランキング --}}
    <url>
        <loc>{{ url('/rank') }}</loc>
        <changefreq>daily</changefreq>
        <priority>0.7</priority>
    </url>

    {{-- ご利用案内 --}}
    <url>
        <loc>{{ url('/system') }}</loc>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
    </url>

    {{-- ホテル・スタジオ --}}
    <url>
        <loc>{{ url('/hotel') }}</loc>
        <changefreq>monthly</changefreq>
        <priority>0.6</priority>
    </url>

    {{-- コスチューム --}}
    <url>
        <loc>{{ url('/costume') }}</loc>
        <changefreq>monthly</changefreq>
        <priority>0.6</priority>
    </url>

    {{-- オプション --}}
    <url>
        <loc>{{ url('/options') }}</loc>
        <changefreq>monthly</changefreq>
        <priority>0.6</priority>
    </url>

    {{-- 使い方ガイド --}}
    <url>
        <loc>{{ url('/guidance') }}</loc>
        <changefreq>monthly</changefreq>
        <priority>0.6</priority>
    </url>

    {{-- アクセス --}}
    <url>
        <loc>{{ url('/access') }}</loc>
        <changefreq>monthly</changefreq>
        <priority>0.6</priority>
    </url>

    {{-- プライバシーポリシー --}}
    <url>
        <loc>{{ url('/privacy') }}</loc>
        <changefreq>yearly</changefreq>
        <priority>0.4</priority>
    </url>

    {{-- イベント --}}
    <url>
        <loc>{{ url('/event') }}</loc>
        <changefreq>weekly</changefreq>
        <priority>0.7</priority>
    </url>

    {{-- 特設イベント一覧 --}}
    <url>
        <loc>{{ url('/special-event') }}</loc>
        <changefreq>weekly</changefreq>
        <priority>0.7</priority>
    </url>

    {{-- 特設イベント詳細 --}}
    @foreach($specialEvents as $event)
    <url>
        <loc>{{ url('/special-event/' . $event->id) }}</loc>
        <lastmod>{{ $event->updated_at->toAtomString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.6</priority>
    </url>
    @endforeach

    {{-- インフォメーション一覧 --}}
    <url>
        <loc>{{ url('/news') }}</loc>
        <changefreq>weekly</changefreq>
        <priority>0.7</priority>
    </url>

    {{-- インフォメーション詳細 --}}
    @foreach($news as $item)
    <url>
        <loc>{{ url('/news/' . $item->id) }}</loc>
        <lastmod>{{ $item->updated_at->toAtomString() }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.6</priority>
    </url>
    @endforeach

    {{-- ブログ一覧 --}}
    <url>
        <loc>{{ url('/blogs') }}</loc>
        <changefreq>weekly</changefreq>
        <priority>0.7</priority>
    </url>

    {{-- ブログ詳細 --}}
    @foreach($blogs as $blog)
    <url>
        <loc>{{ url('/blogs/' . $blog->id) }}</loc>
        <lastmod>{{ $blog->updated_at->toAtomString() }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.6</priority>
    </url>
    @endforeach

    {{-- クーポン一覧 --}}
    <url>
        <loc>{{ url('/coupons') }}</loc>
        <changefreq>weekly</changefreq>
        <priority>0.7</priority>
    </url>

    {{-- クーポン詳細 --}}
    @foreach($coupons as $coupon)
    <url>
        <loc>{{ url('/coupons/' . $coupon->id) }}</loc>
        <lastmod>{{ $coupon->updated_at->toAtomString() }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.6</priority>
    </url>
    @endforeach

    {{-- ファンクラブ一覧 --}}
    <url>
        <loc>{{ url('/fanclub') }}</loc>
        <changefreq>weekly</changefreq>
        <priority>0.6</priority>
    </url>

    {{-- ファンクラブ詳細 --}}
    @foreach($fanclubs as $fanclub)
    <url>
        <loc>{{ url('/fanclub/' . $fanclub->id) }}</loc>
        <lastmod>{{ $fanclub->updated_at->toAtomString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.5</priority>
    </url>
    @endforeach

    {{-- ギャラリー一覧 --}}
    <url>
        <loc>{{ url('/galleries') }}</loc>
        <changefreq>weekly</changefreq>
        <priority>0.6</priority>
    </url>

    {{-- ギャラリー詳細 --}}
    @foreach($galleries as $gallery)
    <url>
        <loc>{{ url('/galleries/' . $gallery->id) }}</loc>
        <lastmod>{{ $gallery->updated_at->toAtomString() }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.5</priority>
    </url>
    @endforeach

    {{-- お問い合わせ --}}
    <url>
        <loc>{{ url('/contact') }}</loc>
        <changefreq>monthly</changefreq>
        <priority>0.5</priority>
    </url>

    {{-- キャスト募集 --}}
    <url>
        <loc>{{ url('/recruit') }}</loc>
        <changefreq>monthly</changefreq>
        <priority>0.5</priority>
    </url>

</urlset>
