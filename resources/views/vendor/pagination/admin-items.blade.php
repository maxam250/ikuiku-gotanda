<nav class="admin-pagination">
    {{-- 前のページ --}}
    @if ($paginator->onFirstPage())
        <span class="admin-page-btn disabled">&lsaquo; 前</span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}" class="admin-page-btn" rel="prev">&lsaquo; 前</a>
    @endif

    {{-- ページ番号 --}}
    @foreach ($elements as $element)
        @if (is_string($element))
            <span class="admin-page-btn disabled">{{ $element }}</span>
        @endif
        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span class="admin-page-btn current">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="admin-page-btn">{{ $page }}</a>
                @endif
            @endforeach
        @endif
    @endforeach

    {{-- 次のページ --}}
    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" class="admin-page-btn" rel="next">次 &rsaquo;</a>
    @else
        <span class="admin-page-btn disabled">次 &rsaquo;</span>
    @endif
</nav>
