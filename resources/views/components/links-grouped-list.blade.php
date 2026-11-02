@props([
    'links'           => collect(),
    'title'           => 'リンク一覧',
    'showEmptyGroups' => false,
])

@php
    $positionConfig = [
        0 => 'link-group--free-top',
        1 => 'link-group--468',
        2 => 'link-group--200',
        3 => 'link-group--88',
        4 => 'link-group--text',
        5 => 'link-group--free-bottom',
    ];

    $displayLinks = collect($links)
        ->filter(fn($l) => !empty($l->fullcode) || !empty($l->filename) || !empty($l->link))
        ->sortBy([['position', 'asc'], ['sort', 'asc'], ['id', 'asc']]);

    $groups = $displayLinks->groupBy('position');
@endphp

@if ($displayLinks->isNotEmpty())
<section class="links-grouped-list">
    @if (!empty($title))
        <h3 class="links-grouped-title">{{ $title }}</h3>
    @endif

    @foreach ($positionConfig as $pos => $groupClass)
        @php $groupLinks = $groups->get($pos, collect()); @endphp
        @if ($groupLinks->isNotEmpty())
            <div class="link-group {{ $groupClass }}">
                @foreach ($groupLinks as $link)
                    <div class="link-item">
                        @if (!empty($link->fullcode))
                            {!! $link->fullcode !!}
                        @elseif (!empty($link->filename))
                            @php $href = !empty($link->link) ? $link->link : '#'; @endphp
                            <a href="{{ $href }}" target="_blank" rel="noopener noreferrer">
                                <img src="{{ \Illuminate\Support\Facades\Storage::url('link/' . $link->filename) }}"
                                     alt="{{ $link->title ?? '' }}">
                            </a>
                        @elseif (!empty($link->link))
                            <a href="{{ $link->link }}" target="_blank" rel="noopener noreferrer">
                                {{ $link->title ?: $link->link }}
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    @endforeach
</section>
@endif
