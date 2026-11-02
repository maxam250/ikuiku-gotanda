@extends('layouts.app')


@section('content')
<div class="titleArea">
<h2>NEWS</h2>
<div class="title">お知らせ一覧</div>
</div>

<section id="newsListArea">
    <div class="news-list-container">
        <div class="news-list-header">
            <h1 class="news-list-title">
                <i class="fas fa-newspaper news-list-icon"></i>
                <span>News</span>
            </h1>
            <p class="news-list-description">最新のニュースをお届けします</p>
        </div>

        @if(@isset($news) && $news->count() > 0)
            <div class="news-grid">
                @foreach ($news as $newsItem)
                    <article class="news-item">
                        <a href="{{ route('news.show', $newsItem->id) }}" class="news-item-link">
                            @if ( $newsItem->filename )
                            <div class="news-item-image">
                                <img src="{{ Storage::url('news/'.$newsItem->filename) }}" alt="{{ $newsItem->title }}">
                            </div>
                            @else
                            <div class="news-item-image">
                                <div class="news-item-no-img">{{ $newsItem->title }}</div>
                            </div>
                            @endif
                            <div class="news-item-content">
                                @if($newsItem->cate && ($cat = config('news_categories.'.$newsItem->cate)))
                                <span class="news-cate-badge"
                                      style="background:{{ $cat['bg'] }};color:{{ $cat['color'] }};">
                                    {{ $cat['label'] }}
                                </span>
                                @endif
                                <div class="news-item-date">
                                    {{ $newsItem->created_at->format('Y.m.d') }}
                                </div>
                                <h2 class="news-item-title">{{ $newsItem->title }}</h2>
                                <div class="news-item-body">{!! Str::limit(strip_tags($newsItem->body), 120) !!}</div>
                                @if ( $newsItem->link )
                                <div class="news-item-external">
                                    <i class="fas fa-external-link-alt"></i>
                                    外部リンク
                                </div>
                                @endif
                            </div>
                        </a>
                    </article>
                @endforeach
            </div>

            <!-- ページネーション -->
            <div class="news-pagination-wrapper">
                {{ $news->links() }}
            </div>
        @else
            <div class="news-empty">
                <i class="fas fa-newspaper empty-icon"></i>
                <p>現在、ニュースはありません。</p>
            </div>
        @endif
    </div>
</section>

@endsection
