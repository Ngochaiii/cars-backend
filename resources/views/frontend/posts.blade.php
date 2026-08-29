{{--
    Danh sách bài viết — dùng cho cả /tin-tuc và /chuyen-muc/{slug}.

    Trang đầu và đủ bài thì dựng theo thiết kế: một bài nổi bật to bên trái,
    danh sách dọc bên phải, phần còn lại xuống lưới 3 cột. Ít bài hoặc trang
    sau thì về lưới thường — không có bài "nổi bật" giả.

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

    <section class="block" style="padding-top:32px">
        <div class="wrap">
            <h1>{{ $heading }}</h1>

            @if ($categories->isNotEmpty())
                <ul class="chips" style="margin-top:28px">
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
                @php
                    $featured = $posts->onFirstPage() && $posts->count() >= 3;
                    $lead     = $featured ? $posts->first() : null;
                    $side     = $featured ? $posts->slice(1, 4) : collect();
                    $grid     = $featured ? $posts->slice(5) : collect($posts->items());
                @endphp

                @if ($featured)
                    <div class="news-lead" @if ($categories->isEmpty()) style="margin-top:36px" @endif>
                        <a class="card" href="{{ route('posts.show', $lead->slug) }}">
                            <span class="card__media">
                                @if ($cover = catalog_image($lead->cover))
                                    <x-img :src="$cover" :alt="$lead->title" sizes="(max-width: 960px) 100vw, 55vw" />
                                @else
                                    <span class="ph" style="position:absolute;inset:0">[ ảnh bài nổi bật ]</span>
                                @endif
                            </span>
                            <span class="card__body">
                                <span class="card__date">
                                    @if ($lead->category)<b>{{ $lead->category->name }}</b> · @endif
                                    {{ $lead->published_at?->format('d/m/Y') }}
                                </span>
                                <span class="card__title" style="display:block">{{ $lead->title }}</span>
                                @if ($lead->excerpt)
                                    <span class="card__meta" style="margin-top:10px">{{ Str::limit($lead->excerpt, 180) }}</span>
                                @endif
                            </span>
                        </a>

                        <div class="news-side">
                            @foreach ($side as $item)
                                <a class="news-side__item" href="{{ route('posts.show', $item->slug) }}">
                                    <div class="card__date">
                                        @if ($item->category)<b>{{ $item->category->name }}</b> · @endif
                                        {{ $item->published_at?->format('d/m/Y') }}
                                    </div>
                                    <div class="news-side__title">{{ $item->title }}</div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($grid->isNotEmpty())
                    <ul class="cards cards--3" @if (! $featured && $categories->isEmpty()) style="margin-top:36px" @endif>
                        @each('frontend.partials.post-card', $grid, 'post')
                    </ul>
                @endif

                <div class="pagination-wrap">{{ $posts->links('frontend.partials.pagination') }}</div>
            @endif
        </div>
    </section>
@endsection
