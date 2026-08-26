{{--
    Trang chủ — bám bố cục bản thiết kế:
      hero carousel · công cụ mua xe · coverflow dải sản phẩm ·
      băng ưu đãi · pin & trạm sạc · khám phá đại lý · chăm sóc chủ xe

    Hero và coverflow lấy dữ liệu từ mặt hàng trong DB, không bảng riêng.
    Các băng nội dung (ưu đãi, pin & trạm sạc, chăm sóc chủ xe) đọc từ Cài
    đặt — khoá nào trống thì cả khối tự ẩn, không để chữ mẫu chết trong view.

    Biến: $products · $posts
--}}
@extends('frontend.layout', [
    'title'       => catalog_setting('site_name', config('app.name')),
    'description' => catalog_setting('site_description'),
    'canonical'   => rtrim(config('app.url'), '/').'/',
])

@section('content')
    @php
        // Banner tự khai được ưu tiên; chưa có thì lùi về 3 mặt hàng đầu như cũ.
        $slides     = $banners->isNotEmpty() ? $banners : $products->take(3);
        $fromBanner = $banners->isNotEmpty();
        $lead       = $products->first();

        // Tab coverflow = danh mục có hàng, kèm số lượng. Mỗi danh mục chỉ có
        // đúng 1 xe thì tab thành vô nghĩa (bấm cái nào cũng ra 1 xe) — bỏ hẳn
        // dải tab, để coverflow chạy thẳng trên toàn bộ danh sách.
        $byCategory = $products->groupBy('category_id');
        $tabs = $byCategory->contains(fn ($group) => $group->count() > 1)
            ? $products->pluck('category')->filter()->unique('id')->values()
            : collect();
    @endphp

    {{-- ── Hero carousel ──────────────────────────────────────────────── --}}
    @if ($slides->isNotEmpty())
        <section class="hero hero--carousel" data-hero>
            @foreach ($slides as $i => $slide)
                @php
                    // Hai nguồn slide dùng chung một khuôn: banner tự khai có
                    // chữ và nút riêng, mặt hàng thì suy ra từ tên và giá.
                    $img = $fromBanner
                        ? catalog_image($slide->image)
                        : catalog_image(data_get($slide->hero, 'src'));

                    // Banner chỉ ảnh vẫn là một link, nên alt PHẢI mô tả được
                    // đích đến — link chỉ chứa ảnh mà alt rỗng thì người dùng
                    // trình đọc màn hình không biết bấm vào đi đâu.
                    $alt = $fromBanner
                        ? ($slide->title ?: $slide->cta_label ?: $slide->eyebrow ?: 'Banner khuyến mãi')
                        : $slide->name;

                    $eyebrow = $fromBanner
                        ? $slide->eyebrow
                        : trim($slide->name.($slide->category ? ' · '.$slide->category->name : ''));

                    $heading = $fromBanner ? $slide->title : ($slide->tagline ?: $slide->name);

                    $lede = $fromBanner
                        ? $slide->subtitle
                        : ($slide->price_from ? 'Giá từ '.catalog_money_short($slide->price_from) : null);

                    $ctaLabel = $fromBanner ? $slide->cta_label : 'Khám phá '.$slide->name;
                    $ctaUrl   = $fromBanner ? $slide->cta_url : route('products.show', $slide->slug);

                    // Banner chỉ có ảnh: hiện đúng tấm ảnh, bấm vào là đi tới
                    // link. Ảnh loại này thường đã thiết kế sẵn chữ bên trong,
                    // đè thêm tiêu đề và nút của site lên là hỏng bố cục.
                    $bare = $fromBanner && $img && $slide->isBare();
                @endphp

                <div class="hero__slide {{ $bare ? 'hero__slide--bare' : '' }} {{ $i === 0 ? 'is-on' : '' }}"
                     data-hero-slide aria-hidden="{{ $i === 0 ? 'false' : 'true' }}">
                    @if ($img)
                        <div class="hero__media">
                            <img src="{{ $img }}" alt="{{ $alt }}"
                                 @if ($i === 0) fetchpriority="high" @else loading="lazy" @endif>
                        </div>
                    @endif

                    {{-- Cả tấm ảnh là một link. Ảnh không kèm link thì để yên,
                         không dựng thẻ <a> rỗng bấm vào chẳng đi đâu. --}}
                    @if ($bare && filled($ctaUrl))
                        <a class="hero__bare-link" href="{{ $ctaUrl }}">
                            <span class="sr-only">{{ $alt }}</span>
                        </a>
                    @endif

                    @unless ($bare)
                    <div class="hero__body">
                        <div class="wrap">
                            <div class="hero__inner">
                                @if (filled($eyebrow))
                                    <span class="eyebrow">{{ $eyebrow }}</span>
                                @endif

                                <h1>{{ $heading }}</h1>

                                @if (filled($lede))
                                    <p class="hero__lede">{{ $lede }}</p>
                                @endif

                                <div class="hero__actions">
                                    {{-- Nhãn không kèm link là nút chết — bỏ hẳn. --}}
                                    @if (filled($ctaLabel) && filled($ctaUrl))
                                        <a class="btn btn--light" href="{{ $ctaUrl }}">{{ $ctaLabel }}</a>
                                    @endif

                                    {{-- Nút luôn dùng biến thể cho nền tối: nền carousel
                                         tối kể cả khi không có ảnh (xem .hero--carousel
                                         trong frontend.css). Đổi class theo $img thì banner
                                         không ảnh ra nút chữ đen trên nền đen. --}}
                                    <a class="btn btn--ghost" href="{{ route('products.index') }}">Xem tất cả</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endunless
                </div>
            @endforeach

            @if ($slides->count() > 1)
                <div class="hero__nav wrap">
                    <div class="hero__dots">
                        @foreach ($slides as $i => $slide)
                            <button type="button" class="hero__dot {{ $i === 0 ? 'is-on' : '' }}"
                                    data-hero-dot="{{ $i }}" aria-current="{{ $i === 0 ? 'true' : 'false' }}">
                                <span class="sr-only">{{ $fromBanner ? $slide->title : $slide->name }}</span>
                            </button>
                        @endforeach
                        <span class="hero__count" data-hero-count>01 / {{ str_pad($slides->count(), 2, '0', STR_PAD_LEFT) }}</span>
                    </div>

                    <div class="hero__arrows">
                        <button type="button" class="arrow" data-hero-prev aria-label="Slide trước">‹</button>
                        <button type="button" class="arrow" data-hero-next aria-label="Slide sau">›</button>
                    </div>
                </div>
            @endif
        </section>
    @endif

    {{-- ── Công cụ mua xe ─────────────────────────────────────────────── --}}
    <section class="tools">
        <div class="wrap tools__inner">
            <span class="eyebrow">Công cụ mua xe</span>
            <div class="tools__grid">
                <a class="tools__item" href="{{ route('products.index') }}">
                    <div class="tools__name">Toàn bộ {{ Str::lower(catalog_label('product.plural')) }}</div>
                    <div class="tools__sub">So sánh giá và thông số từng {{ Str::lower(catalog_label('variant.single')) }}.</div>
                </a>

                {{-- Có trang đặt cọc riêng thì trỏ thẳng vào đó; không thì
                     về form nằm cuối trang chi tiết như trước. --}}
                @if (Route::has('booking'))
                    <a class="tools__item" href="{{ route('booking', ['hinh-thuc' => 'dat-coc']) }}">
                        <div class="tools__name">Đặt cọc online</div>
                        <div class="tools__sub">Giữ suất xe, hoàn cọc trong 7 ngày.</div>
                    </a>
                    <a class="tools__item" href="{{ route('booking', ['hinh-thuc' => 'dat-lich-lai-thu']) }}">
                        <div class="tools__name">Đăng ký lái thử</div>
                        <div class="tools__sub">Chọn khung giờ, lái thử tại nhà hoặc showroom.</div>
                    </a>
                @elseif ($lead)
                    <a class="tools__item" href="{{ route('products.show', $lead->slug) }}#form-dat-coc">
                        <div class="tools__name">Đặt cọc online</div>
                        <div class="tools__sub">Giữ suất xe, hoàn cọc trong 7 ngày.</div>
                    </a>
                    <a class="tools__item" href="{{ route('products.show', $lead->slug) }}#form-dat-lich-lai-thu">
                        <div class="tools__name">Đăng ký lái thử</div>
                        <div class="tools__sub">Chọn khung giờ, lái thử tại nhà hoặc showroom.</div>
                    </a>
                @endif

                @if ($lead)
                    <a class="tools__item" href="{{ route('products.show', $lead->slug) }}#fuel-calc">
                        <div class="tools__name">Tính chi phí sử dụng</div>
                        <div class="tools__sub">So sánh tiền điện với xe xăng, dầu tương đương.</div>
                    </a>
                @endif

                @if (Route::has('services'))
                    <a class="tools__item" href="{{ route('services') }}">
                        <div class="tools__name">Trạm sạc &amp; dịch vụ</div>
                        <div class="tools__sub">Điểm sạc trong tỉnh và lịch bảo dưỡng.</div>
                    </a>
                @elseif (catalog_feature('posts'))
                    <a class="tools__item" href="{{ route('posts.index') }}">
                        <div class="tools__name">Tin tức &amp; ưu đãi</div>
                        <div class="tools__sub">Chương trình đang chạy tại đại lý.</div>
                    </a>
                @endif
            </div>
        </div>
    </section>

    {{-- ── Coverflow dải sản phẩm ─────────────────────────────────────── --}}
    <section class="block disc" data-disc>
        <div class="wrap">
            <div class="section__head disc__head">
                <h2>Khám phá dải sản phẩm</h2>
            </div>

            @if ($products->isEmpty())
                <p class="empty">Chưa có {{ Str::lower(catalog_label('product.plural')) }} nào được đăng.</p>
            @else
                @if ($tabs->isNotEmpty())
                    <div class="disc__tabs">
                        <button type="button" class="disc__tab is-on" data-disc-tab="all">
                            Tất cả <span class="disc__count">{{ $products->count() }}</span>
                        </button>
                        @foreach ($tabs as $tab)
                            <button type="button" class="disc__tab" data-disc-tab="{{ $tab->slug }}">
                                {{ $tab->name }}
                                <span class="disc__count">{{ $byCategory->get($tab->id)?->count() ?? 0 }}</span>
                            </button>
                        @endforeach
                    </div>
                @endif

                <div class="disc__stage" data-disc-stage>
                    <button type="button" class="arrow arrow--round disc__arrow disc__arrow--prev"
                            data-disc-prev aria-label="Xe trước">‹</button>

                    <div class="disc__rail">
                        @foreach ($products as $car)
                            <article class="disc__item" data-disc-item
                                     data-disc-cat="{{ $car->category->slug ?? 'all' }}">
                                <a class="disc__media" href="{{ route('products.show', $car->slug) }}">
                                    @if ($img = catalog_image(data_get($car->hero, 'src')))
                                        <img src="{{ $img }}" alt="{{ $car->name }}" loading="lazy">
                                    @else
                                        <span class="ph" style="position:absolute;inset:0">[ {{ $car->name }} ]</span>
                                    @endif
                                </a>

                                <div class="disc__info">
                                    <h3 class="disc__name">{{ $car->name }}</h3>
                                    <p class="disc__meta">
                                        {{ collect([
                                            $car->category?->name,
                                            collect($car->highlights ?? [])->take(2)
                                                ->map(fn ($h) => trim(($h['value'] ?? '').' '.($h['unit'] ?? '')))
                                                ->filter()->implode(' · '),
                                        ])->filter()->implode(' · ') }}
                                    </p>

                                    <div class="disc__actions">
                                        <a class="btn" href="{{ route('products.show', $car->slug) }}">Khám phá</a>
                                        <a class="btn btn--outline"
                                           href="{{ Route::has('booking')
                                               ? route('booking', ['xe' => $car->slug])
                                               : route('products.show', $car->slug).'#form-dat-coc' }}">Đặt cọc</a>
                                    </div>

                                    @if ($car->price_from)
                                        <p class="disc__price">Giá từ {{ catalog_money($car->price_from) }}</p>
                                    @endif
                                </div>
                            </article>
                        @endforeach
                    </div>

                    <button type="button" class="arrow arrow--round disc__arrow disc__arrow--next"
                            data-disc-next aria-label="Xe sau">›</button>
                </div>

                <p class="disc__pager"><span data-disc-count>01 / {{ str_pad($products->count(), 2, '0', STR_PAD_LEFT) }}</span></p>
            @endif
        </div>
    </section>

    @include('frontend.partials.home-bands', ['lead' => $lead, 'posts' => $posts])
@endsection
