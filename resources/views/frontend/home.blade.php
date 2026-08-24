{{--
    Trang chủ — bám bố cục bản thiết kế:
      hero chiến dịch · công cụ mua xe · lưới xe · băng ưu đãi · tin tức

    Hero lấy từ mặt hàng đầu tiên (ảnh + tên + giá) nên không cần bảng riêng.
    Băng ưu đãi chỉ hiện khi Cài đặt có `offer_title` — không có thì bỏ qua
    cả khối, không để chữ mẫu chết cứng trong template.

    Biến: $products · $posts
--}}
@extends('frontend.layout', [
    'title'       => catalog_setting('site_name', config('app.name')),
    'description' => catalog_setting('site_description'),
    'canonical'   => rtrim(config('app.url'), '/').'/',
])

@section('content')
    @php
        $lead      = $products->first();
        $leadImage = $lead ? catalog_image(data_get($lead->hero, 'src')) : null;

        $offerTitle = catalog_setting('offer_title');
        $offerText  = catalog_setting('offer_text');
        $offerNote  = catalog_setting('offer_note');
    @endphp

    {{-- ── Hero chiến dịch ────────────────────────────────────────────── --}}
    <section class="hero {{ $leadImage ? 'hero--overlay' : 'hero--plain' }}">
        @if ($leadImage)
            <div class="hero__media">
                <img src="{{ $leadImage }}" alt="{{ $lead->name }}" fetchpriority="high">
            </div>
        @endif

        <div class="hero__body">
            <div class="wrap">
                <div class="hero__inner">
                    <span class="eyebrow">{{ catalog_setting('site_name', config('app.name')) }}</span>

                    <h1>
                        @if ($lead)
                            {{ $lead->name }}
                        @else
                            {{ catalog_label('product.plural') }}
                        @endif
                    </h1>

                    <p class="hero__lede">
                        {{ $lead->tagline ?? catalog_setting('site_description')
                            ?? 'Toàn bộ '.Str::lower(catalog_label('product.plural')).' đang mở bán — giá và thông số cập nhật theo từng '.Str::lower(catalog_label('variant.single')).'.' }}
                    </p>

                    @if ($lead && $lead->price_from)
                        <div class="hero__price">
                            <span class="hero__price-label">Giá từ</span>
                            <span class="hero__price-now">{{ catalog_money($lead->price_from) }}</span>
                        </div>
                    @endif

                    <div class="hero__actions">
                        @if ($lead)
                            <a class="btn {{ $leadImage ? 'btn--accent' : '' }}"
                               href="{{ route('products.show', $lead->slug) }}">Khám phá</a>
                            <a class="btn {{ $leadImage ? 'btn--ghost' : 'btn--outline' }}"
                               href="{{ route('products.index') }}">Xem tất cả</a>
                        @else
                            <a class="btn" href="{{ route('products.index') }}">Xem tất cả</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ── Công cụ mua xe ─────────────────────────────────────────────── --}}
    <section class="tools">
        <div class="wrap tools__inner">
            <span class="eyebrow">Công cụ mua xe</span>
            <div class="tools__grid">
                <a class="tools__item" href="{{ route('products.index') }}">
                    <div class="tools__name">Toàn bộ {{ Str::lower(catalog_label('product.plural')) }}</div>
                    <div class="tools__sub">So sánh giá và thông số từng {{ Str::lower(catalog_label('variant.single')) }}.</div>
                </a>

                @if ($lead)
                    <a class="tools__item" href="{{ route('products.show', $lead->slug) }}">
                        <div class="tools__name">Đăng ký lái thử</div>
                        <div class="tools__sub">Chọn khung giờ, lái thử tại nhà hoặc showroom.</div>
                    </a>
                    <a class="tools__item" href="{{ route('products.show', $lead->slug) }}">
                        <div class="tools__name">Tính chi phí sử dụng</div>
                        <div class="tools__sub">So sánh tiền điện với xe xăng, dầu tương đương.</div>
                    </a>
                @endif

                @if (catalog_feature('posts'))
                    <a class="tools__item" href="{{ route('posts.index') }}">
                        <div class="tools__name">Tin tức &amp; ưu đãi</div>
                        <div class="tools__sub">Chương trình đang chạy tại đại lý.</div>
                    </a>
                @endif
            </div>
        </div>
    </section>

    {{-- ── Dải sản phẩm ───────────────────────────────────────────────── --}}
    <section class="block">
        <div class="wrap">
            <div class="section__head">
                <h2>Khám phá dải sản phẩm</h2>
            </div>

            @if ($products->isEmpty())
                <p class="empty">Chưa có {{ Str::lower(catalog_label('product.plural')) }} nào được đăng.</p>
            @else
                <ul class="cards">
                    @each('frontend.partials.product-card', $products, 'product')
                </ul>
            @endif
        </div>
    </section>

    {{-- ── Băng ưu đãi (Cài đặt → offer_title / offer_text / offer_note) ── --}}
    @if (filled($offerTitle))
        <section class="offer">
            <div class="wrap offer__inner">
                @if (filled($offerNote))
                    <span class="eyebrow eyebrow--light">{{ $offerNote }}</span>
                @endif
                <h2>{{ $offerTitle }}</h2>
                @if (filled($offerText))
                    <p>{{ $offerText }}</p>
                @endif
                <div class="offer__actions">
                    <a class="btn btn--light" href="{{ route('products.index') }}">Xem {{ Str::lower(catalog_label('product.plural')) }}</a>
                    @if ($lead)
                        <a class="btn btn--ghost" href="{{ route('products.show', $lead->slug) }}">Đặt cọc ngay</a>
                    @endif
                </div>
            </div>
        </section>
    @endif

    {{-- ── Tin tức ────────────────────────────────────────────────────── --}}
    @if ($posts->isNotEmpty())
        <section class="block block--soft">
            <div class="wrap">
                <div class="section__head">
                    <h2>Tin mới từ đại lý</h2>
                </div>

                @php $tileLead = $posts->first(); $tileRest = $posts->slice(1)->take(2); @endphp

                <div class="tiles {{ $tileRest->isEmpty() ? 'tiles--solo' : '' }}">
                    <a class="tile" href="{{ route('posts.show', $tileLead->slug) }}">
                        <div class="tile__media">
                            @if ($cover = catalog_image($tileLead->cover))
                                <img src="{{ $cover }}" alt="{{ $tileLead->title }}" loading="lazy">
                            @else
                                <div class="ph" style="height:100%">[ {{ $tileLead->title }} ]</div>
                            @endif
                        </div>
                        @if ($tileLead->category)
                            <div class="tile__kicker">{{ $tileLead->category->name }}</div>
                        @endif
                        <div class="tile__title">{{ $tileLead->title }}</div>
                        @if ($tileLead->excerpt)
                            <p>{{ Str::limit($tileLead->excerpt, 160) }}</p>
                        @endif
                    </a>

                    @if ($tileRest->isNotEmpty())
                        <div class="tiles__side">
                            @foreach ($tileRest as $tile)
                                <a class="tile" href="{{ route('posts.show', $tile->slug) }}">
                                    <div class="tile__media">
                                        @if ($cover = catalog_image($tile->cover))
                                            <img src="{{ $cover }}" alt="{{ $tile->title }}" loading="lazy">
                                        @else
                                            <div class="ph" style="height:100%">[ {{ $tile->title }} ]</div>
                                        @endif
                                    </div>
                                    @if ($tile->published_at)
                                        <div class="tile__kicker">{{ $tile->published_at->format('d/m/Y') }}</div>
                                    @endif
                                    <div class="tile__title">{{ $tile->title }}</div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>

                <p class="pagination-wrap">
                    <a class="link-arrow" href="{{ route('posts.index') }}">Xem tất cả tin tức ›</a>
                </p>
            </div>
        </section>
    @endif
@endsection
