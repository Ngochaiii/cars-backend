{{--
    Thẻ phụ kiện: ảnh vuông hơn thẻ xe, tên 2 dòng, giá, một nút liên hệ.

    Nút trỏ về trang đặt cọc nếu có (khách hỏi mua qua đúng luồng lead),
    không thì về trang chi tiết mặt hàng.
--}}
@php
    $image = catalog_image(data_get($product, 'hero.src'));
    $url   = route('products.show', $product->slug);
@endphp

<li class="card card--tight">
    <a class="card__media card__media--wide" href="{{ $url }}">
        @if ($image)
            <img src="{{ $image }}" alt="{{ $product->name }}" loading="lazy">
        @else
            <span class="ph" style="position:absolute;inset:0">[ {{ $product->name }} ]</span>
        @endif
    </a>

    <div class="card__body">
        <h3 class="card__name"><a href="{{ $url }}">{{ $product->name }}</a></h3>

        @if ($product->price_from)
            <div class="card__cost">{{ catalog_money($product->price_from) }}</div>
        @endif

        <div class="card__actions">
            <a class="btn btn--sm btn--outline btn--block" href="{{ $url }}">Liên hệ đặt mua</a>
        </div>
    </div>
</li>
