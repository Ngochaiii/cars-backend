{{--
    Hero của trang chi tiết. `hero` là {type, src, poster}:
      - type = image → ảnh, chữ đè lên ảnh
      - type = video → nhúng video (YouTube/Vimeo/mp4)
      - không có hero → khối chữ trên nền xám, không để trang trống hốc
--}}
@php
    $hero  = $product->hero ?? [];
    $src   = $hero['src'] ?? null;
    $isVideo = ($hero['type'] ?? 'image') === 'video';
    $media = $isVideo ? \App\Support\Media::embed($src) : catalog_image($src);
@endphp

<div class="hero {{ $media ? 'hero--overlay' : 'hero--plain' }}">
    @if ($media)
        <div class="hero__media">
            @if ($isVideo)
                @if (\App\Support\Media::isFile($media))
                    <video src="{{ $media }}" poster="{{ catalog_image($hero['poster'] ?? null) }}"
                           controls preload="metadata"></video>
                @else
                    <iframe src="{{ $media }}" title="{{ $product->name }}" loading="lazy" allowfullscreen></iframe>
                @endif
            @else
                <img src="{{ $media }}" alt="{{ $product->name }}">
            @endif
        </div>
    @endif

    <div class="hero__body">
        <div class="wrap">
            @if ($product->category)
                <span class="eyebrow">{{ $product->category->name }}</span>
            @endif

            <h1>{{ $product->name }}</h1>

            @if ($product->tagline)
                <p>{{ $product->tagline }}</p>
            @endif

            @if ($product->price_from)
                <p class="price-from">
                    {{ catalog_label('product.single') }} từ {{ catalog_money($product->price_from) }}
                </p>
            @endif
        </div>
    </div>
</div>
