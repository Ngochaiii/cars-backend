@extends('frontend.layout', [
    'title'       => data_get($post->seo, 'title', $post->title),
    'description' => data_get($post->seo, 'description', $post->excerpt),
    'canonical'   => \App\Support\Url::absolute('post', $post->slug),
    'ogImage'     => catalog_image($post->cover),
    'jsonld'      => \App\Support\JsonLd::forPost($post),
])

@section('content')
    <article>
        <div class="wrap">
            <ol class="breadcrumb">
                <li><a href="{{ route('home') }}">Trang chủ</a></li>
                <li><a href="{{ route('posts.index') }}">Tin tức</a></li>
                @if ($post->category)
                    <li><a href="{{ route('post-categories.show', $post->category->slug) }}">{{ $post->category->name }}</a></li>
                @endif
                <li>{{ Str::limit($post->title, 40) }}</li>
            </ol>
        </div>

        <header class="block">
            <div class="wrap wrap--narrow">
                @if ($post->published_at)
                    <span class="eyebrow">{{ $post->published_at->format('d/m/Y') }}</span>
                @endif

                <h1>{{ $post->title }}</h1>

                @if ($post->excerpt)
                    <p class="lede">{{ $post->excerpt }}</p>
                @endif

                @if ($cover = catalog_image($post->cover))
                    <img src="{{ $cover }}" alt="{{ $post->title }}" style="border-radius: var(--radius)">
                @endif
            </div>
        </header>

        @include('frontend.partials.sections', ['sections' => $sections])
    </article>
@endsection
