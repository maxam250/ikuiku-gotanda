@extends('layouts.app')

@section('content')
<div class="titleArea">
    <h2>JOURNAL</h2>
    <div class="title">ジャーナル一覧</div>
</div>

<section id="blogListArea">
    <div class="blog-list-container">

        <div class="blog-list-header">
            <h1 class="blog-list-title">
                <i class="fas fa-book-open blog-list-icon"></i>
                <span>Journal</span>
            </h1>
            <p class="blog-list-description">スタッフからの最新情報をお届けします</p>
        </div>

        @if(isset($blogs) && $blogs->count() > 0)
            <div class="blog-grid">
                @foreach ($blogs as $blogItem)
                    <article class="blog-item">
                        <a href="{{ route('blogs.show', $blogItem->id) }}" class="blog-item-link">
                            <div class="blog-item-image">
                                @if ($blogItem->main_image)
                                    <img src="{{ Storage::url($blogItem->main_image) }}" alt="{{ $blogItem->title }}">
                                @else
                                    <img src="{{ asset('images/dmy.jpg') }}" alt="{{ $blogItem->title }}">
                                @endif
                            </div>
                            <div class="blog-item-content">
                                <div class="blog-item-date">
                                    {{ $blogItem->created_at->format('Y.m.d') }}
                                </div>
                                <h2 class="blog-item-title">{{ $blogItem->title }}</h2>
                                <div class="blog-item-body">{!! Str::limit(strip_tags($blogItem->content), 120) !!}</div>
                            </div>
                        </a>
                    </article>
                @endforeach
            </div>

            <div class="blog-pagination-wrapper">
                {{ $blogs->links() }}
            </div>
        @else
            <div class="blog-empty">
                <i class="fas fa-book-open empty-icon"></i>
                <p>現在、記事はありません。</p>
            </div>
        @endif
    </div>
</section>

@endsection
