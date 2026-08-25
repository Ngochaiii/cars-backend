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
        // Khối mở đầu có tiêu đề RIÊNG, không lặp lại tagline đang làm h1 ở
        // hero — bản thiết kế dùng hai câu khác nhau.
        $introTitle     = data_get($product->hero, 'intro_title');
        $intro          = data_get($product->hero, 'intro_body') ?: data_get($product->seo, 'description');
        $orderForm      = $forms->first();

        // Link brochure khai ở Cài đặt (chung cho cả hãng) — trống thì nút
        // brochure không dựng.
        $brochure       = catalog_setting('brochure_url');
    @endphp

    <article>
        {{-- ── Hero ───────────────────────────────────────────────────── --}}
        @include('frontend.partials.hero', ['product' => $product, 'heroForms' => $forms])

        <div class="wrap">
            <ol class="breadcrumb">
                <li><a href="{{ route('home') }}">Trang chủ</a></li>
                <li><a href="{{ route('products.index') }}">{{ catalog_label('product.plural') }}</a></li>
                @if ($product->category)
                    <li><a href="{{ route('categories.show', $product->category->slug) }}">{{ $product->category->name }}</a></li>
                @endif
                <li>{{ $product->name }}</li>
            </ol>
        </div>

        {{-- ── Đoạn mở đầu (lấy từ mô tả SEO, không có thì bỏ) ────────── --}}
        @if (filled($intro) || filled($introTitle))
            <section class="intro">
                @if (filled($introTitle))
                    <h2>{{ $introTitle }}</h2>
                @endif
                @if (filled($intro))
                    <p>{{ $intro }}</p>
                @endif
            </section>
        @endif

        {{-- ── Chỉ số nổi bật ─────────────────────────────────────────── --}}
        @if (catalog_feature('highlights') && filled($product->highlights))
            <div class="wrap kpi-strip">
                @include('frontend.partials.highlights', ['highlights' => $product->highlights])
            </div>
        @endif

        {{-- Bản thiết kế KHÔNG có khối liệt kê phiên bản ở trang chi tiết:
             phiên bản xuất hiện ở bảng thông số và ở bước chọn xe khi đặt
             cọc. Dữ liệu phiên bản vẫn dùng để lấy giá, giá gạch và số liệu
             cho bảng so sánh chi phí — chỉ không dựng thành một mục riêng.
             Muốn hiện lại thì include partials/variants ở đây. --}}

        {{-- ── Bảng màu ───────────────────────────────────────────────── --}}
        @if (catalog_feature('options') && $product->options->isNotEmpty())
            <section class="section block--tint">
                {{-- Không tiêu đề: thiết kế để ảnh xe và dãy màu tự nói. --}}
                <div class="wrap" style="text-align:center">
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

                    {{-- Hàng nút dưới bảng, như thiết kế. Brochure chỉ hiện
                         khi có link thật — nhãn không kèm link là nút chết. --}}
                    @if ($brochure || $orderForm)
                        <div class="spec-actions">
                            @if ($brochure)
                                <a class="btn btn--outline-accent" href="{{ $brochure }}" rel="noopener" target="_blank">Tải brochure</a>
                            @endif
                            @if ($orderForm)
                                <a class="btn btn--accent" href="#form-{{ $orderForm->key }}">{{ $orderForm->name }}</a>
                            @endif
                        </div>
                    @endif

                    <p class="specs__note">
                        (*) Thông số và hình ảnh mang tính minh họa. Quãng đường di chuyển tính theo chu trình
                        kiểm định, thực tế thay đổi tùy tốc độ, nhiệt độ, địa hình, tải trọng và thói quen lái.
                    </p>
                </div>
            </section>
        @endif

        {{-- ── So sánh chi phí nhiên liệu ─────────────────────────────── --}}
        @if (catalog_feature('fuel_calc') && $fuelCalc)
            <section class="section block--tint">
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
            <section class="section">
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
                                <div class="order-bar__price">Từ {{ catalog_money_short($product->price_from) }}</div>
                            @elseif ($defaultVariant?->price)
                                <div class="order-bar__price">Từ {{ catalog_money_short($defaultVariant->price) }}</div>
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
