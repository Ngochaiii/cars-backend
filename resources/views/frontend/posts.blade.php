{{--
    Danh sách bài viết — dùng cho cả /tin-tuc và /chuyen-muc/{slug}.

    Biến: $heading · $posts (paginator) · $categories · $postCategory?
--}}
@extends('frontend.layout', [
    'title'     => $heading,
    'canonical' => $canonical ?? null,
])

@section('content')
    <div class="wrap">
        <ol class="breadcrumb">
            <li><a href="{{ route('home') }}">Trang chủ</a></li>
            @isset($postCategory)
                <li><a href="{{ route('posts.index') }}">Tin tức</a></li>
            @endisset
            <li>{{ $heading }}</li>
        </ol>
    </div>

    <section class="block">
        <div class="wrap">
            <h1>{{ $heading }}</h1>

            @if ($categories->isNotEmpty())
                <ul class="chips">
                    <li>
                        <a class="chip {{ empty($postCategory) ? 'chip--on' : '' }}" href="{{ route('posts.index') }}">
                            Tất cả
                        </a>
                    </li>
                    @foreach ($categories as $item)
                        <li>
                            <a class="chip {{ (isset($postCategory) && $postCategory->is($item)) ? 'chip--on' : '' }}"
                               href="{{ route('post-categories.show', $item->slug) }}">{{ $item->name }}</a>
                        </li>
                    @endforeach
                </ul>
            @endif

            @if ($posts->isEmpty())
                <p class="empty">Chưa có bài viết nào ở đây.</p>
            @else
                <ul class="cards">
                    @each('frontend.partials.post-card', $posts, 'post')
                </ul>

                <div class="pagination-wrap">{{ $posts->links('frontend.partials.pagination') }}</div>
            @endif
        </div>
    </section>
@endsection
