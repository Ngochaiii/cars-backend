{{--
    Trang chi tiết mặt hàng — bám bố cục bản thiết kế:

      hero tối · chỉ số nổi bật · phiên bản · bảng màu · mục tự đặt ·
      thông số · so sánh chi phí · form · thanh đặt cọc dính đáy

    Khối nào thiếu dữ liệu thì tự ẩn, không để trang hở mảng trống.

    Biến: $product · $sections · $forms · $fuelCalc
--}}
@extends('frontend.layout', [
    'title'       => data_get($product->seo, 'title', $product->name),
    'description' => data_get($product->seo, 'description', $product->tagline),
    'canonical'   => \App\Support\Url::absolute('product', $product->slug),
    'ogImage'     => catalog_image(data_get($product->hero, 'src')),
    'jsonld'      => \App\Support\JsonLd::forProduct($product),
])

@section('content')
    @php
        $defaultVariant = $product->variants->firstWhere('is_default', true) ?? $product->variants->first();
        $heroImage      = catalog_image(data_get($product->hero, 'src'));
        $intro          = data_get($product->seo, 'description');
        $orderForm      = $forms->first();
    @endphp

    <article>
        {{-- ── Hero ───────────────────────────────────────────────────── --}}
        @include('frontend.partials.hero', ['product' => $product, 'heroForms' => $forms])

        {{-- ── Chỉ số nổi bật ─────────────────────────────────────────── --}}
        @if (catalog_feature('highlights') && filled($product->highlights))
            <section class="section" style="padding-block:72px">
                <div class="wrap">
                    @include('frontend.partials.highlights', ['highlights' => $product->highlights])
                </div>
            </section>
        @endif

        <div class="wrap">
            <ol class="breadcrumb" style="padding-top:0">
                <li><a href="{{ route('home') }}">Trang chủ</a></li>
                <li><a href="{{ route('products.index') }}">{{ catalog_label('product.plural') }}</a></li>
                @if ($product->category)
                    <li><a href="{{ route('categories.show', $product->category->slug) }}">{{ $product->category->name }}</a></li>
                @endif
                <li>{{ $product->name }}</li>
            </ol>
        </div>

        {{-- ── Đoạn mở đầu (lấy từ mô tả SEO, không có thì bỏ) ────────── --}}
        @if (filled($intro))
            <section class="section" style="text-align:center">
                <div class="wrap wrap--narrow">
                    <h2 style="margin-bottom:22px">{{ $product->tagline ?: $product->name }}</h2>
                    <p class="lede" style="margin-inline:auto">{{ $intro }}</p>
                </div>
            </section>
        @endif

        {{-- ── Phiên bản ──────────────────────────────────────────────── --}}
        @if (catalog_feature('variants') && $product->variants->isNotEmpty())
            <section class="section">
                <div class="wrap">
                    <div class="section__head"><h2>{{ catalog_label('variant.plural') }}</h2></div>
                    @include('frontend.partials.variants', ['variants' => $product->variants])
                </div>
            </section>
        @endif

        {{-- ── Bảng màu ───────────────────────────────────────────────── --}}
        @if (catalog_feature('options') && $product->options->isNotEmpty())
            <section class="section block--tint" style="padding-block:88px">
                <div class="wrap" style="text-align:center">
                    <div class="section__head" style="margin-bottom:32px">
                        <h2>{{ catalog_label('option.plural') }}</h2>
                    </div>
                    @include('frontend.partials.options', ['options' => $product->options, 'product' => $product])
                </div>
            </section>
        @endif

        {{-- Phần biến thiên: các mục do người nhập tự đặt tên. --}}
        @include('frontend.partials.sections', ['sections' => $sections])

        {{-- ── Thông số kỹ thuật ──────────────────────────────────────── --}}
        @if (catalog_feature('specs') && filled($product->specs))
            <section class="section">
                <div class="wrap">
                    <div class="section__head"><h2>{{ catalog_label('specs') }}</h2></div>
                    @include('frontend.partials.specs', ['specs' => $product->specs])

                    <p class="specs__note">
                        (*) Thông số và hình ảnh mang tính minh họa. Quãng đường di chuyển tính theo chu trình
                        kiểm định, thực tế thay đổi tùy tốc độ, nhiệt độ, địa hình, tải trọng và thói quen lái.
                    </p>
                </div>
            </section>
        @endif

        {{-- ── So sánh chi phí nhiên liệu ─────────────────────────────── --}}
        @if (catalog_feature('fuel_calc') && $fuelCalc)
            <section class="section block--tint" style="padding-block:96px">
                <div class="wrap">
                    <div class="section__head">
                        <h2>So sánh giữa {{ $product->name }} và xe động cơ đốt trong</h2>
                        <p>Số liệu tham khảo, không phải báo giá chính thức.</p>
                    </div>
                    @include('frontend.partials.fuel-calculator', ['product' => $product, 'fuelCalc' => $fuelCalc])
                </div>
            </section>
        @endif

        {{-- Form(s) cuối trang: khoá khai ở config('catalog.frontend.product_forms'). --}}
        @foreach ($forms as $f)
            <section class="section" style="padding-block:96px">
                <div class="wrap">
                    <div class="section__head" style="text-align:center">
                        <h2>{{ $f->name }}</h2>
                        <p style="margin-inline:auto">{{ $f->description ?: 'Để lại thông tin, tư vấn viên sẽ liên hệ lại.' }}</p>
                    </div>
                    @include('frontend.partials.lead-form', ['form' => $f, 'product' => $product])
                </div>
            </section>
        @endforeach

        {{-- ── Thanh đặt cọc dính đáy ─────────────────────────────────── --}}
        @if ($orderForm)
            <div class="order-bar">
                <div class="wrap order-bar__inner">
                    <div class="order-bar__info">
                        <div class="order-bar__thumb">
                            @if ($heroImage)
                                <img src="{{ $heroImage }}" alt="" aria-hidden="true">
                            @endif
                        </div>
                        <div>
                            <div class="order-bar__name">{{ $product->name }}</div>
                            @if ($product->price_from)
                                <div class="order-bar__price">Từ {{ catalog_money($product->price_from) }}</div>
                            @elseif ($defaultVariant?->price)
                                <div class="order-bar__price">Từ {{ catalog_money($defaultVariant->price) }}</div>
                            @endif
                        </div>
                    </div>

                    <div class="order-bar__actions">
                        <a class="btn btn--accent" href="#form-{{ $orderForm->key }}">{{ $orderForm->name }}</a>
                    </div>
                </div>
            </div>
        @endif
    </article>
@endsection
