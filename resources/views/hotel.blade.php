@extends('layouts.app')

@section('breadcrumb_label', 'ホテル')

@section('content')
<div class="titleArea">
    <h2>HOTEL</h2>
    <div class="title">ホテル概要とアクセスのご案内</div>
</div>

<div class="hotel-page">

    @if($categories->isNotEmpty())
    <nav class="hotel-filter" aria-label="ホテルカテゴリ">
        <ul class="hotel-filter-list">
            <li>
                <a href="{{ route('hotels') }}"
                   class="hotel-filter-link {{ empty($categoryId) ? 'is-active' : '' }}">すべて</a>
            </li>
            @foreach($categories as $cat)
            <li>
                <a href="{{ route('hotels', ['category_id' => $cat->id]) }}"
                   class="hotel-filter-link {{ (string)$categoryId === (string)$cat->id ? 'is-active' : '' }}">
                    {{ $cat->title }}
                </a>
            </li>
            @endforeach
        </ul>
    </nav>
    @endif

    @if($equipments->count() > 0)
    @php
        $hasAnyImage = $equipments->contains(fn ($e) => !empty($e->filename));
        $hotelColspan = $hasAnyImage ? 5 : 4;
    @endphp
    <div class="hotel-table-wrap">
        <table class="hotel-table{{ $hasAnyImage ? '' : ' hotel-table--no-img' }}">
            <thead>
                <tr>
                    @if($hasAnyImage)
                    <th class="hotel-th-img" scope="col"></th>
                    @endif
                    <th scope="col">名前</th>
                    <th scope="col">電話番号</th>
                    <th scope="col">住所</th>
                    <th scope="col">料金</th>
                </tr>
            </thead>
            <tbody>
                @foreach($equipments as $equipment)
                @php
                    $hasExtra = $equipment->link
                        || $equipment->map_code
                        || $equipment->map_url
                        || $equipment->equipmentSubImages->count() > 0
                        || $equipment->equipmentListImages->count() > 0
                        || $equipment->accessoryListImages->count() > 0;
                @endphp
                <tr class="hotel-row-main">
                    @if($hasAnyImage)
                    <td class="hotel-td-img{{ $equipment->filename ? '' : ' is-empty' }}">
                        @if($equipment->filename)
                        <button type="button"
                                class="hotel-img-btn js-hotel-modal"
                                data-modal-src="{{ Storage::url('equipment/' . $equipment->filename) }}"
                                data-modal-title="{{ $equipment->title }}">
                            <img src="{{ Storage::url('equipment/' . $equipment->filename) }}"
                                 alt="{{ $equipment->title }}"
                                 class="hotel-img-main"
                                 loading="lazy">
                        </button>
                        @endif
                    </td>
                    @endif
                    <td class="hotel-td-text hotel-td-name">
                        <span class="hotel-name">{{ $equipment->title }}</span>
                        @if($equipment->equipment_cate)
                        <span class="hotel-card-cate">{{ $equipment->equipment_cate->title }}</span>
                        @endif
                    </td>
                    <td class="hotel-td-text">{{ $equipment->text2 ?: '—' }}</td>
                    <td class="hotel-td-text">{{ $equipment->text3 ?: '—' }}</td>
                    <td class="hotel-td-text">{{ $equipment->text4 ?: '—' }}</td>
                </tr>
                @if($hasExtra)
                <tr class="hotel-row-extra">
                    <td colspan="{{ $hotelColspan }}">
                        <div class="hotel-extra">
                            @if($equipment->link)
                            <div class="hotel-extra-item">
                                <span class="hotel-extra-label">外部リンク</span>
                                <a href="{{ $equipment->link }}" target="_blank" rel="noopener noreferrer" class="hotel-ext-link">
                                    <i class="fas fa-external-link-alt" aria-hidden="true"></i>
                                    {{ $equipment->link }}
                                </a>
                            </div>
                            @endif
                            @if($equipment->map_code || $equipment->map_url)
                            <div class="hotel-extra-item">
                                <span class="hotel-extra-label">アクセスマップ</span>
                                @if($equipment->map_code)
                                <div class="hotel-map-wrap">{!! $equipment->map_code !!}</div>
                                @else
                                <a href="{{ $equipment->map_url }}" target="_blank" rel="noopener noreferrer" class="hotel-ext-link">
                                    <i class="fas fa-map-marker-alt" aria-hidden="true"></i> 地図を見る
                                </a>
                                @endif
                            </div>
                            @endif
                            @if($equipment->equipmentSubImages->count() > 0)
                            <div class="hotel-extra-item">
                                <span class="hotel-extra-label">サブ画像</span>
                                <div class="hotel-thumb-row">
                                    @foreach($equipment->equipmentSubImages as $image)
                                    <button type="button"
                                            class="hotel-img-btn js-hotel-modal"
                                            data-modal-src="{{ Storage::url('equipment_sub/' . $image->filename) }}"
                                            data-modal-title="{{ $image->name }}">
                                        <img src="{{ Storage::url('equipment_sub/' . $image->filename) }}"
                                             alt="{{ $image->name }}"
                                             class="hotel-img-thumb"
                                             loading="lazy">
                                    </button>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                            @if($equipment->equipmentListImages->count() > 0)
                            <div class="hotel-extra-item">
                                <span class="hotel-extra-label">機材リスト</span>
                                <div class="hotel-list-grid">
                                    @foreach($equipment->equipmentListImages as $equipmentImage)
                                    <div class="hotel-list-item">
                                        <button type="button"
                                                class="hotel-img-btn js-hotel-modal"
                                                data-modal-src="{{ !empty($equipmentImage->filename) ? Storage::url('equipment_list/'.$equipmentImage->filename) : asset('images/dmy.jpg') }}"
                                                data-modal-title="{{ $equipmentImage->name }}">
                                            <img src="{{ !empty($equipmentImage->filename) ? Storage::url('equipment_list/'.$equipmentImage->filename) : asset('images/dmy.jpg') }}"
                                                 alt="{{ $equipmentImage->name }}"
                                                 class="hotel-img-list-item"
                                                 loading="lazy">
                                        </button>
                                        <p class="hotel-list-name">{{ $equipmentImage->name }}</p>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                            @if($equipment->accessoryListImages->count() > 0)
                            <div class="hotel-extra-item">
                                <span class="hotel-extra-label">小物リスト</span>
                                <div class="hotel-list-grid">
                                    @foreach($equipment->accessoryListImages as $accessory)
                                    <div class="hotel-list-item">
                                        <button type="button"
                                                class="hotel-img-btn js-hotel-modal"
                                                data-modal-src="{{ !empty($accessory->filename) ? Storage::url('accessory_list/'.$accessory->filename) : asset('images/dmy.jpg') }}"
                                                data-modal-title="{{ $accessory->name }}">
                                            <img src="{{ !empty($accessory->filename) ? Storage::url('accessory_list/'.$accessory->filename) : asset('images/dmy.jpg') }}"
                                                 alt="{{ $accessory->name }}"
                                                 class="hotel-img-list-item"
                                                 loading="lazy">
                                        </button>
                                        <p class="hotel-list-name">{{ $accessory->name }}</p>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>
                    </td>
                </tr>
                @endif
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="hotel-pagination">
        {{ $equipments->links() }}
    </div>
    @else
    <div class="hotel-empty">
        <i class="fas fa-hotel" aria-hidden="true"></i>
        <p>該当するホテル情報はありません。</p>
    </div>
    @endif
</div>

<div id="hotelImageModal" class="hotel-modal" role="dialog" aria-modal="true" aria-label="画像拡大" hidden>
    <div class="hotel-modal-inner">
        <button type="button" class="hotel-modal-close" onclick="closeHotelModal()" aria-label="閉じる">&times;</button>
        <h4 id="hotelModalTitle" class="hotel-modal-title"></h4>
        <img id="hotelModalImage" src="" alt="" class="hotel-modal-img">
    </div>
</div>

<script>
(function () {
    const modal = document.getElementById('hotelImageModal');

    function openHotelModal(imageSrc, title) {
        document.getElementById('hotelModalImage').src = imageSrc;
        document.getElementById('hotelModalImage').alt = title;
        document.getElementById('hotelModalTitle').textContent = title;
        modal.hidden = false;
        modal.classList.add('is-open');
        document.body.style.overflow = 'hidden';
    }

    window.closeHotelModal = function () {
        modal.hidden = true;
        modal.classList.remove('is-open');
        document.body.style.overflow = '';
    };

    document.querySelectorAll('.js-hotel-modal').forEach(function (btn) {
        btn.addEventListener('click', function () {
            openHotelModal(btn.dataset.modalSrc, btn.dataset.modalTitle || '');
        });
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') window.closeHotelModal();
    });

    modal.addEventListener('click', function (e) {
        if (e.target === modal) window.closeHotelModal();
    });
})();
</script>
@endsection
