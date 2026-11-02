{{-- ============================================================
     左カラム（旧 www_tpl/side.tpl → navi.tpl / inc_outlink.tpl / inc_group.tpl）
     PC は全ページでコンテンツの左に表示。スマホはナビをドロワーに任せ、
     外部ウィジェット・バナー・グループ店のみメインの下に表示する。
     ウィジェットは管理画面「店舗情報」のウィジェット[1]〜[3]。
     ============================================================ --}}
@php
    $sideGroups = \App\Models\ShopGroup::where('is_publish', 1)
        ->orderBy('sort')
        ->get();
    // 旧DBから移行した行はファイル名のみの場合があるので shop-groups/ を補完する
    $sideGroupImage = function ($group) {
        if (!$group->filename) {
            return $group->image_url ?: null;
        }
        $path = str_contains($group->filename, '/') ? $group->filename : 'shop-groups/' . $group->filename;
        return Storage::disk('public')->exists($path) ? Storage::url($path) : null;
    };

    // 旧 inc_outlink.tpl のバナー（デリヘルじゃぱん・風俗じゃぱん）
    $sideBanners = [
        ['url' => 'https://deli-fuzoku.jp/ikuikugtd/girlsdiary/',                              'img' => 'https://ad.deli-fuzoku.jp/img/pc_deli_banner3_1_468_60.png', 'alt' => '写メ日記一覧｜デリヘルじゃぱん'],
        ['url' => 'https://deli-fuzoku.jp/ikuikugtd/schedule/',                                'img' => 'https://ad.deli-fuzoku.jp/img/pc_deli_banner4_1_468_60.png', 'alt' => '出勤情報一覧｜デリヘルじゃぱん'],
        ['url' => 'https://fuzoku.jp/yaricirikuikugotanda/girlsdiary/?FJ=official&id=diary',   'img' => 'https://ad.fuzoku.jp/img/sp_fuzoku_banner3_1_640_80.png',    'alt' => '写メ日記一覧｜風俗じゃぱん'],
        ['url' => 'https://fuzoku.jp/yaricirikuikugotanda/schedule/',                          'img' => 'https://ad.fuzoku.jp/img/pc_fuzoku_banner4_1_468_60.png',    'alt' => '出勤情報一覧｜風俗じゃぱん'],
        ['url' => 'https://fuzoku.jp/yaricirikuikugotanda/review/?FJ=official&id=review',      'img' => 'https://ad.fuzoku.jp/img/sp_fuzoku_banner5_1_640_80.jpg',    'alt' => '口コミ体験談一覧｜風俗じゃぱん'],
    ];
@endphp

<aside class="site-side" aria-label="サイドメニュー">

    {{-- 縦ナビ（navi.tpl） --}}
    <nav class="site-side-navi" aria-label="サイト内ナビゲーション">
        @include('layouts.navigation')
    </nav>

    {{-- 外部ウィジェット（inc_outlink.tpl: シティヘブンの写メ日記など） --}}
    <ul class="site-side-widgets">
        @foreach(['shop_wiget1', 'shop_wiget2', 'shop_wiget3'] as $wiget)
            @if(!empty($shops->{$wiget}))
            <li class="site-side-widget">{!! $shops->{$wiget} !!}</li>
            @endif
        @endforeach
    </ul>

    {{-- バナー（inc_outlink.tpl） --}}
    <ul class="site-side-banners">
        @foreach($sideBanners as $banner)
        <li><a href="{{ $banner['url'] }}" target="_blank" rel="noopener"><img src="{{ $banner['img'] }}" alt="{{ $banner['alt'] }}" loading="lazy"></a></li>
        @endforeach
    </ul>

    {{-- グループ店バナー（inc_group.tpl → 管理画面「グループ店」） --}}
    @if($sideGroups->isNotEmpty())
    <section class="site-side-group">
        <h2 class="site-side-heading">GROUP</h2>
        <ul>
            @foreach($sideGroups as $group)
            @php $groupImg = $sideGroupImage($group); @endphp
            <li>
                <a href="{{ trim($group->url) ?: '#' }}" target="_blank" rel="noopener">
                    @if($groupImg)
                        <img src="{{ $groupImg }}" alt="{{ $group->name }}" loading="lazy">
                    @else
                        <span class="site-side-group-name">{{ $group->name }}</span>
                    @endif
                </a>
            </li>
            @endforeach
        </ul>
    </section>
    @endif

</aside>
