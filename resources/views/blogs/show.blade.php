@extends('layouts.app')

@section('breadcrumb_label', $blogItem->title)

@section('content')

<div class="titleArea">
    <h2>JOURNAL</h2>
    <div class="title">ジャーナル詳細</div>
</div>

<section id="blogDetailArea">
    <div class="blog-detail-container">

        <article class="blog-detail">
            <header class="blog-detail-header">
                <div class="blog-detail-date">
                    {{ $blogItem->created_at->format('Y年m月d日') }}
                </div>
                <h1 class="blog-detail-title">{{ $blogItem->title }}</h1>
            </header>

            @if ($blogItem->main_image)
            <div class="blog-detail-image">
                <img src="{{ Storage::url($blogItem->main_image) }}" alt="{{ $blogItem->title }}">
            </div>
            @endif

            <div class="blog-detail-content">
                {!! $blogItem->content !!}
            </div>


        </article>

        <div class="blog-detail-actions">
            <x-back-to-list href="{{ route('blogs.index') }}" text="一覧に戻る" />
        </div>
    </div>
</section>

@endsection
