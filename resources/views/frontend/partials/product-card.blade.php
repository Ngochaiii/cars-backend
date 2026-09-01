{{--
    Thẻ mặt hàng dùng ở trang chủ, danh sách và danh mục.

    Bảng thông số nhỏ trong thẻ lấy 3 chỉ số đầu của `highlights` — mặt hàng
    chưa khai chỉ số thì bỏ luôn bảng, thẻ vẫn cân.
--}}
@php
    $image      = catalog_image(data_get($product, 'hero.src'));
    $url        = route('products.show', $product->slug);
    $miniSpecs  = collect($product->highlights ?? [])->take(3);
@endphp

<li class="card">
    <a class="card__media" href="{{ $url }}">
        @if ($image)
            <x-img :src="$image" :alt="$product->name" sizes="(max-width: 680px) 100vw, (max-width: 1180px) 45vw, 420px" />
        @else
            <span class="ph" style="position:absolute;inset:0">[ ảnh {{ $product->name }} ]</span>
        @endif

        @if ($product->category)
            <span class="card__tag">{{ $product->category->name }}</span>
        @endif

        <span class="card__view" aria-hidden="true">↗</span>
    </a>

    <div class="card__body">
        <div class="card__head">
            <h3 class="card__title"><a href="{{ $url }}">{{ $product->name }}</a></h3>
            @if ($product->price_from)
                <span class="card__price">Từ <b>{{ catalog_money($product->price_from) }}</b></span>
            @endif
        </div>

        @if ($product->tagline)
            <span class="card__meta">{{ $product->tagline }}</span>
        @endif

        @if ($miniSpecs->isNotEmpty())
            <ul class="card__specs">
                @foreach ($miniSpecs as $spec)
                    <li class="card__spec">
                        <b>{{ trim(($spec['value'] ?? '').' '.($spec['unit'] ?? '')) }}</b>
                        <span>{{ $spec['label'] ?? '' }}</span>
                    </li>
                @endforeach
            </ul>
        @endif

        <div class="card__actions">
            @if (Route::has('booking'))
                <a class="btn btn--sm" href="{{ route('booking', ['xe' => $product->slug]) }}">{{ catalog_label('cta.deposit') }}</a>
                <a class="btn btn--sm btn--outline" href="{{ $url }}">Xem chi tiết</a>
            @else
                <a class="btn btn--sm" href="{{ $url }}">Xem chi tiết</a>
            @endif

            @if (Route::has('compare'))
                <a class="btn btn--sm btn--outline" href="{{ route('compare', ['xe' => $product->slug]) }}">So sánh</a>
            @endif
        </div>
    </div>
</li>
