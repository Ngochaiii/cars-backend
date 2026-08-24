{{--
    Hero của trang chi tiết. `hero` là {type, src, poster}:
      - type = image → ảnh nền, chữ trắng đè lên, có lớp phủ tối cho dễ đọc
      - type = video → nhúng video (YouTube/Vimeo/mp4) làm nền
      - không có hero → khối chữ trên nền xám nhạt, không để trang trống hốc
--}}
@php
    $hero    = $product->hero ?? [];
    $src     = $hero['src'] ?? null;
    $isVideo = ($hero['type'] ?? 'image') === 'video';
    $media   = $isVideo ? \App\Support\Media::embed($src) : catalog_image($src);

    $variant  = $product->variants->firstWhere('is_default', true) ?? $product->variants->first();
    $priceNow = $product->price_from ?: $variant?->price;
    $priceWas = $variant?->price_original;
@endphp

<section class="hero {{ $media ? 'hero--overlay' : 'hero--plain' }}">
    @if ($media)
        <div class="hero__media">
            @if ($isVideo)
                @if (\App\Support\Media::isFile($media))
                    <video src="{{ $media }}" poster="{{ catalog_image($hero['poster'] ?? null) }}"
                           autoplay muted loop playsinline></video>
                @else
                    <iframe src="{{ $media }}" title="{{ $product->name }}" loading="lazy" allowfullscreen></iframe>
                @endif
            @else
                <img src="{{ $media }}" alt="{{ $product->name }}" fetchpriority="high">
            @endif
        </div>
    @endif

    <div class="hero__body">
        <div class="wrap">
            <div class="hero__inner">
                <span class="eyebrow">
                    {{ $product->name }}@if ($product->category) · {{ $product->category->name }}@endif
                </span>

                <h1>{{ $product->name }}</h1>

                @if ($product->tagline)
                    <p class="hero__lede">{{ $product->tagline }}</p>
                @endif

                @if ($priceNow)
                    <div class="hero__price">
                        <span class="hero__price-label">{{ catalog_label('product.single') }} từ</span>
                        <span class="hero__price-now">{{ catalog_money($priceNow) }}</span>
                        @if ($priceWas && $priceWas > $priceNow)
                            <span class="hero__price-was">{{ catalog_money($priceWas) }}</span>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
