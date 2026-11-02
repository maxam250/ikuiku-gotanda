@extends('layouts.app')

@section('breadcrumb_label', 'ファンクラブ')

@section('content')
<div class="titleArea">
    <h2>FAN CLUB</h2>
    <div class="title">ファンクラブ</div>
</div>

<section class="fanclub-section">

    @if($fanclubs->isEmpty())
        <div class="fanclub-empty">
            <i class="fas fa-heart"></i>
            <p>現在開設中のファンクラブはありません。</p>
        </div>
    @else
        <div class="fanclub-grid">
            @foreach($fanclubs as $fanclub)
            @php
                $membership = $membershipByFanclubId->get($fanclub->id);
                $isMemberActive = $membership && $membership->status === 'active';
            @endphp
            <div class="fanclub-card {{ $isMemberActive ? 'is-member' : '' }}">

                @if($isMemberActive)
                    <span class="fanclub-member-badge"><i class="fas fa-check-circle"></i> 会員</span>
                @endif

                <a href="{{ route('fanclub.show', $fanclub) }}" class="fanclub-cast-link">
                    <div class="fanclub-cast-image">
                        @if($fanclub->main_image_path)
                            <img src="{{ asset('storage/' . $fanclub->main_image_path) }}"
                                 alt="{{ $fanclub->cast?->item_name }}">
                        @else
                            <img src="{{ $fanclub->cast?->thumbnail?->filename ? asset('storage/' . $fanclub->cast->thumbnail->filename) : asset('images/dmy.jpg') }}"
                                 alt="{{ $fanclub->cast?->item_name }}">
                        @endif
                    </div>
                </a>

                <div class="fanclub-card-body">
                    <h3 class="fanclub-cast-name">
                        <a href="{{ route('fanclub.show', $fanclub) }}">
                            {{ $fanclub->cast?->item_name }}
                        </a>
                    </h3>

                    <h4 class="fanclub-title">{{ $fanclub->title }}</h4>

                    @if($fanclub->description)
                        <p class="fanclub-description">{{ Str::limit($fanclub->description, 60) }}</p>
                    @endif

                    <div class="fanclub-meta">
                        <span class="fanclub-price">
                            <i class="fas fa-yen-sign"></i>
                            入会費 {{ number_format($fanclub->price) }}円
                        </span>
                        <span class="fanclub-member-count">
                            <i class="fas fa-users"></i>
                            {{ $fanclub->active_member_count }}人
                        </span>
                    </div>

                    <div class="fanclub-action">
                        @if($isMemberActive)
                            <a href="{{ route('member.fanclubs.show', $fanclub) }}" class="fanclub-btn fanclub-btn-view">
                                <i class="fas fa-heart"></i> コンテンツを見る
                            </a>
                        @else
                            <a href="{{ route('fanclub.show', $fanclub) }}" class="fanclub-btn fanclub-btn-join">
                                <i class="fas fa-info-circle"></i> 詳細を見る
                            </a>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="fanclub-pagination">
            {{ $fanclubs->links() }}
        </div>
    @endif

</section>
@endsection
