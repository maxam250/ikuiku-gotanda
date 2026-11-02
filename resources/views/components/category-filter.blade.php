@props([
    'categories',
    'currentId'  => null,
    'routeName',
    'routeParams' => [],
])

@if($categories->isNotEmpty())
<div class="cat-filter">
    <ul class="cat-filter-list">
        {{-- すべて --}}
        <li class="cat-filter-item">
            <a href="{{ route($routeName, $routeParams) }}"
               class="cat-filter-link {{ $currentId === null ? 'is-active' : '' }}">
                <span class="cat-filter-icon cat-filter-icon--all">
                    <i class="fas fa-th" aria-hidden="true"></i>
                </span>
                <span class="cat-filter-name">すべて</span>
            </a>
        </li>
        {{-- カテゴリごと --}}
        @foreach($categories as $cat)
        <li class="cat-filter-item">
            <a href="{{ route($routeName, array_merge($routeParams, ['category_id' => $cat->id])) }}"
               class="cat-filter-link {{ (string)$currentId === (string)$cat->id ? 'is-active' : '' }}">
                <span class="cat-filter-icon" data-color="{{ $cat->color }}" style="background:{{ $cat->color }}">
                    <i class="{{ $cat->icon }}" aria-hidden="true"></i>
                </span>
                <span class="cat-filter-name">{{ $cat->name }}</span>
            </a>
        </li>
        @endforeach
    </ul>
</div>

<div class="flex items-center gap-2 mb-2 bg-gray-100 p-2 rounded" style="font-size: 0.56rem;">
    <span class="item-card-badge item-card-badge--fc" title="ファンクラブ">
        <i class="fas fa-heart"></i>
    </span>…ファンクラブ
    <span class="item-card-badge item-card-badge--opt" title="マイオプション">
        <i class="fas fa-star"></i>
    </span>…マイオプション
    <span class="item-card-badge item-card-badge--cos" title="マイコスチューム">
        <i class="fas fa-tshirt"></i>
    </span>…マイコスチューム
</div>
@endif
