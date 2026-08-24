{{-- Thẻ bài viết. `cover` là ảnh đại diện, bỏ trống thì dùng ô sọc thay chỗ. --}}
@php
    $image = catalog_image($post->cover);
    $url   = route('posts.show', $post->slug);
@endphp

<li class="card">
    <a class="card__media" href="{{ $url }}">
        @if ($image)
            <img src="{{ $image }}" alt="{{ $post->title }}" loading="lazy">
        @else
            <span class="ph" style="position:absolute;inset:0">[ ảnh ]</span>
        @endif
    </a>

    <div class="card__body">
        <div class="card__date">
            @if ($post->category)<b>{{ $post->category->name }}</b>@endif
            @if ($post->category && $post->published_at) · @endif
            @if ($post->published_at){{ $post->published_at->format('d/m/Y') }}@endif
        </div>

        <h3 class="card__title" style="font-size:18px"><a href="{{ $url }}">{{ $post->title }}</a></h3>

        @if ($post->excerpt)
            <span class="card__meta">{{ Str::limit($post->excerpt, 120) }}</span>
        @endif
    </div>
</li>
