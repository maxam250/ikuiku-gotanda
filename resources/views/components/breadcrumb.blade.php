@props(['items' => []])

<nav class="breadcrumb" aria-label="パンくずリスト">
    <ol itemscope itemtype="https://schema.org/BreadcrumbList">
        <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
            <a href="/" itemprop="item">
                <span itemprop="name">HOME</span>
            </a>
            <meta itemprop="position" content="1" />
        </li>
        
        @foreach($items as $index => $item)
            @if($loop->last)
                <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    <span itemprop="name" class="current">{{ $item['name'] }}</span>
                    <meta itemprop="position" content="{{ $index + 2 }}" />
                </li>
            @else
                <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    <a href="{{ $item['url'] }}" itemprop="item">
                        <span itemprop="name">{{ $item['name'] }}</span>
                    </a>
                    <meta itemprop="position" content="{{ $index + 2 }}" />
                </li>
            @endif
        @endforeach
    </ol>
</nav> 