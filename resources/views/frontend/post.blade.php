@extends('frontend.layout', [
    'title'       => data_get($post->seo, 'title', $post->title),
    'description' => data_get($post->seo, 'description', $post->excerpt),
    'canonical'   => \App\Support\Url::absolute('post', $post->slug),
    'ogImage'     => catalog_image($post->cover),
    'jsonld'      => \App\Support\JsonLd::forPost($post),
])

@section('content')
    <article class="wrap">
        <div class="article__head wrap--narrow" style="margin:0 auto;padding-inline:0">
            <div class="article__kicker">
                <a href="{{ route('posts.index') }}">Tin tức</a>
                @if ($post->category)
                    / <a href="{{ route('post-categories.show', $post->category->slug) }}">{{ $post->category->name }}</a>
                @endif
                @if ($post->published_at)
                    · {{ $post->published_at->format('d/m/Y') }}
                @endif
            </div>

            <h1>{{ $post->title }}</h1>

            @if ($post->excerpt)
                <p class="article__lede">{{ $post->excerpt }}</p>
            @endif
        </div>

        <div class="article__cover">
            @if ($cover = catalog_image($post->cover))
                <img src="{{ $cover }}" alt="{{ $post->title }}">
            @else
                <div class="ph" style="height:100%">[ ảnh bài viết ]</div>
            @endif
        </div>

        @include('frontend.partials.sections', ['sections' => $sections])

        <div class="article__foot wrap--narrow" style="margin:0 auto;padding-inline:0">
            <a class="article__back" href="{{ route('posts.index') }}">← Tất cả tin tức</a>
        </div>
    </article>

    <div style="height:80px"></div>
@endsection
