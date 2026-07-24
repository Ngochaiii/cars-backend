{{-- Thẻ bài viết. `cover` là ảnh đại diện, bỏ trống thì thẻ chỉ có chữ. --}}
@php
    $image = catalog_image($post->cover);
    $url   = route('posts.show', $post->slug);
@endphp

<li class="card">
    @if ($image)
        <a class="card__media" href="{{ $url }}">
            <img src="{{ $image }}" alt="{{ $post->title }}" loading="lazy">
        </a>
    @endif

    <div class="card__body">
        @if ($post->published_at)
            <span class="card__meta">{{ $post->published_at->format('d/m/Y') }}</span>
        @endif

        <h3 class="card__title"><a href="{{ $url }}">{{ $post->title }}</a></h3>

        @if ($post->excerpt)
            <span class="card__meta">{{ Str::limit($post->excerpt, 120) }}</span>
        @endif
    </div>
</li>
