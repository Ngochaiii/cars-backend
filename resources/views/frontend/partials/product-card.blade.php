{{-- Thẻ mặt hàng dùng ở trang chủ, danh sách và danh mục. --}}
@php
    $image = catalog_image(data_get($product, 'hero.src'));
    $url   = route('products.show', $product->slug);
@endphp

<li class="card">
    @if ($image)
        <a class="card__media" href="{{ $url }}">
            <img src="{{ $image }}" alt="{{ $product->name }}" loading="lazy">
        </a>
    @endif

    <div class="card__body">
        @if ($product->category)
            <span class="card__meta">{{ $product->category->name }}</span>
        @endif

        <h3 class="card__title"><a href="{{ $url }}">{{ $product->name }}</a></h3>

        @if ($product->tagline)
            <span class="card__meta">{{ $product->tagline }}</span>
        @endif

        @if ($product->price_from)
            <span class="card__price">từ {{ catalog_money($product->price_from) }}</span>
        @endif
    </div>
</li>
