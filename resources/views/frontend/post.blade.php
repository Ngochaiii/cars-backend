{{--
    Trang tin — hai cột như báo điện tử: bài ở cột trái, tin liên quan ở cột
    phải. Trước đây bài chạy một cột giữa trang nên hai bên trống hơn nửa màn
    hình ở khổ desktop.

    Bề rộng từng khối vẫn chọn được trong admin: ảnh bìa có ô "Bề rộng ảnh bìa",
    từng mục nội dung có ô "Bề rộng". Trong bố cục hai cột thì "cột chữ" và
    "rộng" đều vừa đúng cột bài; riêng "tràn hết màn hình" mới phá khung ra.
--}}
@extends('frontend.layout', [
    'title'       => data_get($post->seo, 'title', $post->title),
    'description' => data_get($post->seo, 'description', $post->excerpt),
    'canonical'   => data_get($post->seo, 'canonical') ?: \App\Support\Url::absolute('post', $post->slug),
    'ogImage'     => \App\Support\Url::asset(data_get($post->seo, 'image') ?: $post->cover),
    'ogType'      => 'article',
    'jsonld'      => \App\Support\JsonLd::forPost($post),
])

@section('content')
    <article class="article">
        <div class="wrap article-split">
            <div class="article-main">
                <header class="article__head">
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
                </header>

                <div class="article__cover article__cover--{{ $post->cover_width ?: 'narrow' }}">
                    @if ($cover = catalog_image($post->cover))
                        <x-img :src="$cover" :alt="$post->title" sizes="(max-width: 960px) 100vw, 760px" eager />
                    @else
                        <div class="ph" style="height:100%">[ ảnh bài viết ]</div>
                    @endif
                </div>

                @include('frontend.partials.sections', [
                    'sections' => $sections,
                    'bare' => true,
                    'defaultWidth' => 'narrow',
                ])

                <div class="article__foot">
                    <a class="article__back" href="{{ route('posts.index') }}">← Tất cả tin tức</a>
                </div>
            </div>

            @if ($related->isNotEmpty())
                <aside class="article-aside" aria-labelledby="tin-lien-quan">
                    <h2 class="article-aside__title" id="tin-lien-quan">Tin liên quan</h2>

                    <div class="news-side">
                        @foreach ($related as $item)
                            <a class="news-side__item" href="{{ route('posts.show', $item->slug) }}">
                                <div class="card__date">
                                    @if ($item->category)<b>{{ $item->category->name }}</b> · @endif
                                    {{ $item->published_at?->format('d/m/Y') }}
                                </div>
                                <div class="news-side__title">{{ $item->title }}</div>
                            </a>
                        @endforeach
                    </div>

                    <a class="article-aside__all" href="{{ route('posts.index') }}">Xem tất cả tin tức →</a>
                </aside>
            @endif
        </div>
    </article>
@endsection
