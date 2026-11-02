@if ($paginator->hasPages())
    <nav class="member-pagination member-pagination--child" aria-label="投稿内ブロックのページ送り">
        <ul class="pagination pagination--child-nav">
            {{-- Previous --}}
            @if ($paginator->onFirstPage())
                <li class="disabled member-pager-child-li member-pager-child-li--prev" aria-disabled="true">
                    <span class="member-pager-child-link member-pager-child-link--disabled">
                        <span class="member-pager-child-arrow" aria-hidden="true">&lsaquo;</span>
                        @if(!empty($prevTitle))
                            <span class="member-pager-child-title">{{ \Illuminate\Support\Str::limit($prevTitle, 36) }}</span>
                        @endif
                    </span>
                </li>
            @else
                <li class="member-pager-child-li member-pager-child-li--prev">
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="member-pager-child-link">
                        <span class="member-pager-child-arrow" aria-hidden="true">&lsaquo;</span>
                        @if(!empty($prevTitle))
                            <span class="member-pager-child-title">{{ \Illuminate\Support\Str::limit($prevTitle, 36) }}</span>
                        @endif
                    </a>
                </li>
            @endif

            {{-- Page numbers --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <li class="disabled" aria-disabled="true"><span>{{ $element }}</span></li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="active" aria-current="page"><span>{{ $page }}</span></li>
                        @else
                            <li><a href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next --}}
            @if ($paginator->hasMorePages())
                <li class="member-pager-child-li member-pager-child-li--next">
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="member-pager-child-link">
                        @if(!empty($nextTitle))
                            <span class="member-pager-child-title">{{ \Illuminate\Support\Str::limit($nextTitle, 36) }}</span>
                        @endif
                        <span class="member-pager-child-arrow" aria-hidden="true">&rsaquo;</span>
                    </a>
                </li>
            @else
                <li class="disabled member-pager-child-li member-pager-child-li--next" aria-disabled="true">
                    <span class="member-pager-child-link member-pager-child-link--disabled">
                        @if(!empty($nextTitle))
                            <span class="member-pager-child-title">{{ \Illuminate\Support\Str::limit($nextTitle, 36) }}</span>
                        @endif
                        <span class="member-pager-child-arrow" aria-hidden="true">&rsaquo;</span>
                    </span>
                </li>
            @endif
        </ul>
    </nav>
@endif
