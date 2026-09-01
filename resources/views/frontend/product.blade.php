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
    'canonical'   => data_get($product->seo, 'canonical') ?: \App\Support\Url::absolute('product', $product->slug),
    'ogImage'     => \App\Support\Url::asset(data_get($product->seo, 'image') ?: data_get($product->hero, 'src')),
    'ogType'      => 'product',
    'bodyClass'   => 'product-page',
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

        // Ưu tiên brochure riêng của xe, sau đó mới lùi về link chung.
        $brochure       = $product->brochure_url ?: catalog_setting('brochure_url');
        $hotline        = catalog_setting('hotline');
        $bookingUrl     = Route::has('booking')
            ? route('booking', ['xe' => $product->slug])
            : ($orderForm ? '#form-'.$orderForm->key : '#dang-ky-tu-van');
        $testDriveUrl   = Route::has('booking')
            ? route('booking', ['xe' => $product->slug, 'hinh-thuc' => 'dat-lich-lai-thu'])
            : $bookingUrl;
    @endphp

    <article class="product-story" data-product-story>
        <nav class="product-nav" aria-label="Điều hướng trang xe" data-product-nav>
            <div class="wrap product-nav__inner">
                <a class="product-nav__name" href="#tong-quan">{{ $product->name }}</a>
                <div class="product-nav__links">
                    <a href="#tong-quan">Tổng quan</a>
                    @if ($product->options->isNotEmpty())<a href="#mau-xe">Màu xe</a>@endif
                    @if ($product->variants->isNotEmpty())<a href="#phien-ban">Phiên bản</a>@endif
                    @if (filled($product->specs))<a href="#thong-so">Thông số</a>@endif
                    @if ($loan)<a href="#tra-gop">Trả góp</a>@endif
                </div>
                <a class="btn btn--accent btn--sm" href="{{ $bookingUrl }}">{{ catalog_label('cta.deposit') }}</a>
            </div>
        </nav>
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
            <section class="intro" id="cau-chuyen">
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

        {{-- ── Bảng màu ───────────────────────────────────────────────── --}}
        @if (catalog_feature('options') && $product->options->isNotEmpty())
            <section class="section product-colors block--tint story-section" id="mau-xe" data-story-section>
                <div class="wrap" style="text-align:center">
                    <div class="section__head section__head--center">
                        <span class="eyebrow">Cá nhân hóa chiếc xe của bạn</span>
                        <h2>Chọn màu {{ $product->name }}</h2>
                    </div>
                    @include('frontend.partials.options', ['options' => $product->options, 'product' => $product])
                </div>
            </section>
        @endif

        @if (catalog_feature('variants') && $product->variants->isNotEmpty())
            <section class="section product-trims story-section" id="phien-ban" data-story-section>
                <div class="wrap">
                    <div class="section__head section__head--center">
                        <span class="eyebrow">Giá bán và lựa chọn</span>
                        <h2>Phiên bản {{ $product->name }}</h2>
                        <p>Chọn phiên bản phù hợp với nhu cầu di chuyển và ngân sách của bạn.</p>
                    </div>
                    @include('frontend.partials.variants', [
                        'variants' => $product->variants,
                        'product' => $product,
                        'bookingUrl' => $bookingUrl,
                    ])
                </div>
            </section>
        @endif

        {{-- Phần biến thiên: các mục do người nhập tự đặt tên. --}}
        @include('frontend.partials.sections', ['sections' => $sections])

        {{-- ── Thông số kỹ thuật ──────────────────────────────────────── --}}
        @php
            // Ba cách khai thông số độc lập nhau — chỉ cần một cái có dữ liệu
            // là khối phải hiện, nếu không ảnh/PDF vừa tải lên sẽ biến mất.
            $coSpecMedia = filled($product->spec_images) || filled($product->spec_pdf);
        @endphp
        @if (catalog_feature('specs') && (filled($product->specs) || $coSpecMedia))
            <section class="section product-specs story-section" id="thong-so" data-story-section>
                <div class="wrap">
                    <div class="section__head"><h2>{{ catalog_label('specs') }}</h2></div>
                    @if (filled($product->specs))
                        @include('frontend.partials.specs', [
                            'specs' => $product->specs,
                            'notes' => $product->spec_notes ?? [],
                        ])
                    @endif

                    @include('frontend.partials.spec-media', [
                        'specImages' => $product->spec_images ?? [],
                        'specPdf' => $product->spec_pdf,
                        'specPdfLabel' => $product->spec_pdf_label,
                    ])

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
            <section class="section block--tint story-section" data-story-section>
                <div class="wrap">
                    <div class="section__head">
                        <h2>So sánh giữa {{ $product->name }} và xe động cơ đốt trong</h2>
                        <p>Số liệu tham khảo, không phải báo giá chính thức.</p>
                    </div>
                    @include('frontend.partials.fuel-calculator', ['product' => $product, 'fuelCalc' => $fuelCalc])
                </div>
            </section>
        @endif

        {{-- ── Trả góp ────────────────────────────────────────────────── --}}
        @if ($loan)
            <section class="section story-section" id="tra-gop" data-story-section>
                <div class="wrap">
                    <div class="section__head">
                        <h2>Trả góp {{ $product->name }}</h2>
                        <p>Ước tính khoản vay và lãi theo dư nợ giảm dần.</p>
                    </div>
                    @include('frontend.partials.loan-calculator', ['product' => $product, 'loan' => $loan])
                </div>
            </section>
        @endif

        {{-- Form(s) cuối trang: khoá khai ở config('catalog.frontend.product_forms'). --}}
        @foreach ($forms as $f)
            <section class="section product-lead story-section" data-story-section>
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
        @if ($orderForm || Route::has('booking'))
            <div class="order-bar" aria-label="Liên hệ nhanh" data-story-cta>
                <div class="wrap order-bar__inner">
                    <div class="order-bar__info">
                        <div class="order-bar__thumb">
                            @if ($heroImage)
                                <x-img :src="$heroImage" alt="" aria-hidden="true" sizes="120px" />
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
                        @if (filled($hotline))
                            <a class="btn btn--outline order-bar__call"
                               href="tel:{{ preg_replace('/\s+/', '', $hotline) }}">Gọi tư vấn</a>
                        @endif
                        <a class="btn btn--accent" href="{{ $bookingUrl }}">{{ catalog_label('cta.deposit') }}</a>
                        <a class="order-bar__test" href="{{ $testDriveUrl }}">Đăng ký lái thử</a>
                    </div>
                </div>
            </div>
        @endif
    </article>
@endsection
