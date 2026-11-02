@extends('layouts.app')

@section('breadcrumb_label', $fanclub->title)

@section('content')
<div class="titleArea">
    <h2>FAN CLUB</h2>
</div>

<section class="fanclub-section">
 @if($fanclub->main_image_path)
        <div class="fanclub-main-image">

                <img src="{{ asset('storage/' . $fanclub->main_image_path) }}"
                        alt="{{ $fanclub->cast?->item_name }}">

        </div>
 @endif
    {{-- ファンクラブ概要ヘッダー --}}
    <div class="fc-show-header">


        <div class="fc-show-cast">
            <img src="{{ $fanclub->cast?->thumbnail?->filename ? asset('storage/' . $fanclub->cast->thumbnail->filename) : asset('images/dmy.jpg') }}"
                 alt="{{ $fanclub->cast?->item_name }}">
        </div>

        <div class="fc-show-info">
            <p class="fc-show-cast-name">{{ $fanclub->cast?->item_name }}</p>
            <h2 class="fc-show-title">{{ $fanclub->title }}</h2>
            @if($fanclub->description)
                <p class="fc-show-description">{{ $fanclub->description }}</p>
            @endif
            <div class="fc-show-meta">
                <span class="fc-show-price">
                    <i class="fas fa-yen-sign"></i> 入会費 {{ number_format($fanclub->price) }}円
                </span>
                <span class="fc-show-members">
                    <i class="fas fa-users"></i> {{ $fanclub->active_member_count }}人が参加中
                </span>
            </div>

            {{-- CTA --}}
            <div class="fc-show-cta">
                @if($isMemberActive)
                    <span class="fc-member-badge"><i class="fas fa-check-circle"></i> 会員</span>
                    <a href="{{ route('member.fanclubs.withdraw.confirm', $fanclub) }}" class="fc-btn fc-btn-cancel">
                        退会する
                    </a>
                @elseif($member)
                    <a href="{{ route('member.fanclubs.subscribe.confirm', $fanclub) }}" class="fc-btn fc-btn-join">
                        <i class="fas fa-plus-circle"></i> ファンクラブに参加する
                    </a>
                @else
                    <a href="{{ route('member.login') }}?redirect={{ urlencode(url()->current()) }}" class="fc-btn fc-btn-login">
                        <i class="fas fa-lock"></i> ログインして参加する
                    </a>
                @endif
            </div>
        </div>
    </div>

    {{-- コンテンツ一覧 --}}
    <div class="fc-contents-area">
        <h3 class="fc-contents-heading">
            <i class="fas fa-photo-video"></i> コンテンツ
        </h3>

        @if($contents->isEmpty())
            <div class="fc-empty">
                <p>まだコンテンツはありません。</p>
            </div>
        @else
            <div class="fc-contents-list">
                @foreach($contents as $content)
                @php
                    $memberShowParams = ['fanclub' => $fanclub];
                    if ($content->category_id) {
                        $memberShowParams['channel'] = $content->category_id;
                    } else {
                        $memberShowParams['content'] = $content->id;
                    }
                    $memberShowParams['page'] = $contentMemberPages[$content->id] ?? 1;
                @endphp
                <div class="fc-content-card {{ $content->is_paid ? 'is-paid' : 'is-free' }}">

                    {{-- 無料 or 有料バッジ --}}
                    <span class="fc-content-badge {{ $content->is_paid ? 'badge-paid' : 'badge-free' }}">
                        @if($content->is_paid)
                            <i class="fas fa-crown"></i> 会員限定
                        @else
                            <i class="fas fa-unlock"></i> 無料
                        @endif
                    </span>

                    <div class="fc-content-card-body">
                        <h4 class="fc-content-title">{{ $content->title }}</h4>

                        @if($content->description)
                            <p class="fc-content-desc">{{ Str::limit($content->description, 80) }}</p>
                        @endif

                        <div class="fc-content-footer">
                            @if($content->category)
                                <span class="fc-content-category">{{ $content->category->name }}</span>
                            @endif
                            <span class="fc-content-date">{{ optional($content->published_at)->format('Y.m.d') }}</span>
                            <span class="fc-content-children">
                                <i class="fas fa-images"></i> {{ $content->children_count }}件
                            </span>
                        </div>

                        {{-- 会員マイページへ（チャンネル・ページ指定で該当ポストへ） --}}
                        <div class="fc-content-action">
                            @if(!$content->is_paid)
                                {{-- 無料コンテンツ --}}
                                @if($member)
                                    <a href="{{ route('member.fanclubs.show', $memberShowParams) }}" class="fc-content-btn fc-content-btn-view">
                                        <i class="fas fa-eye"></i> コンテンツを見る
                                    </a>
                                @else
                                    <a href="{{ route('member.login') }}?redirect={{ urlencode(url()->current()) }}" class="fc-content-btn fc-content-btn-login">
                                        <i class="fas fa-sign-in-alt"></i> ログインして見る
                                    </a>
                                @endif
                            @else
                                {{-- 有料（会員限定）コンテンツ --}}
                                @if($isMemberActive)
                                    <a href="{{ route('member.fanclubs.show', $memberShowParams) }}" class="fc-content-btn fc-content-btn-view">
                                        <i class="fas fa-eye"></i> コンテンツを見る
                                    </a>
                                @elseif($member)
                                    <span class="fc-content-btn fc-content-btn-locked">
                                        <i class="fas fa-lock"></i> 会員限定コンテンツ
                                    </span>
                                @else
                                    <a href="{{ route('member.login') }}?redirect={{ urlencode(url()->current()) }}" class="fc-content-btn fc-content-btn-login">
                                        <i class="fas fa-lock"></i> ログインして見る
                                    </a>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>

    <div class="fc-back-link">
        <a href="{{ route('fanclub') }}"><i class="fas fa-chevron-left"></i> ファンクラブ一覧に戻る</a>
    </div>

</section>
@endsection
