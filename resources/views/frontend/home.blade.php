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

        // Coverflow ưu tiên xe có ảnh để hai thẻ xem trước không biến thành
        // ô placeholder. Nếu dữ liệu chưa có ảnh nào thì vẫn lùi về danh sách
        // đầy đủ để trang không mất nội dung.
        $discoveryProducts = $products
            ->filter(fn ($product) => filled(catalog_image(data_get($product->hero, 'src'))))
            ->values();
        if ($discoveryProducts->isEmpty()) {
            $discoveryProducts = $products;
        }

        // Coverflow chạy trên CẢ dải xe, không cắt còn vài chiếc: mỗi lúc chỉ
        // thấy 3 thẻ (xe đang chọn + hai xe hai bên) nhưng mũi tên đi hết được
        // danh sách. Thứ tự theo cột `sort`, muốn đổi xe nào lên đầu thì kéo
        // lại thứ tự mặt hàng trong admin. Trần số xe nằm ở
        // config('catalog.frontend.home.products').
        $featured = $discoveryProducts;

        // Tab coverflow = danh mục có hàng, kèm số lượng. Mỗi danh mục chỉ có
        // đúng 1 xe thì tab thành vô nghĩa (bấm cái nào cũng ra 1 xe) — bỏ hẳn
        // dải tab, để coverflow chạy thẳng trên toàn bộ danh sách.
        $byCategory = $featured->groupBy('category_id');
        $tabs = $byCategory->contains(fn ($group) => $group->count() > 1)
            ? $featured->pluck('category')->filter()->unique('id')->values()
            : collect();

        $depositUrl = Route::has('booking')
            ? route('booking', ['hinh-thuc' => 'dat-coc'])
            : ($lead ? route('products.show', $lead->slug).'#form-dat-coc' : null);
        $testDriveUrl = Route::has('booking')
            ? route('booking', ['hinh-thuc' => 'dat-lich-lai-thu'])
            : ($lead ? route('products.show', $lead->slug).'#form-dat-lich-lai-thu' : null);
        $runningCostUrl = $lead ? route('products.show', $lead->slug).'#fuel-calc' : null;
        $serviceUrl = Route::has('services')
            ? route('services')
            : (catalog_feature('posts') ? route('posts.index') : null);
        $leadImage = $lead ? catalog_image(data_get($lead->hero, 'src')) : null;

        $customerTools = collect([
            [
                'number' => '01',
                'name' => 'Khám phá dòng xe',
                'sub' => 'So sánh thiết kế, quãng đường và trang bị để tìm chiếc xe dành cho bạn.',
                'url' => route('products.index'),
                'feature' => true,
            ],
            [
                'number' => '02',
                'name' => 'Đặt cọc trực tuyến',
                'sub' => 'Giữ suất xe nhanh, quy trình minh bạch và được hỗ trợ xuyên suốt.',
                'url' => $depositUrl,
            ],
            [
                'number' => '03',
                'name' => 'Trải nghiệm lái thử',
                'sub' => 'Chọn khung giờ phù hợp tại nhà hoặc trực tiếp ở showroom.',
                'url' => $testDriveUrl,
            ],
            [
                'number' => '04',
                'name' => 'Tính chi phí sở hữu',
                'sub' => 'Đối chiếu chi phí điện với xe xăng, dầu theo hành trình thực tế.',
                'url' => $runningCostUrl,
            ],
            [
                'number' => '05',
                'name' => Route::has('services') ? 'Sạc & chăm sóc xe' : 'Tin tức & ưu đãi',
                'sub' => Route::has('services')
                    ? 'Tìm điểm sạc, lịch bảo dưỡng và dịch vụ đồng hành gần bạn.'
                    : 'Theo dõi chương trình mới và tin tức tại đại lý.',
                'url' => $serviceUrl,
            ],
        ])->filter(fn ($tool) => filled($tool['url']))->values();
    @endphp

    <div class="home-story" data-home-story>

    {{-- ── Hero carousel ──────────────────────────────────────────────── --}}
    @if ($slides->isNotEmpty())
        <section class="hero hero--carousel home-hero" data-hero data-home-hero aria-label="Chương trình nổi bật">
            @foreach ($slides as $i => $slide)
                @php
                    // Hai nguồn slide dùng chung một khuôn: banner tự khai có
                    // chữ và nút riêng, mặt hàng thì suy ra từ tên và giá.
                    $img = $fromBanner
                        ? catalog_image($slide->image)
                        : catalog_image(data_get($slide->hero, 'src'));

                    $mobileImg = $fromBanner
                        ? catalog_image($slide->image_mobile)
                        : catalog_image(data_get($slide->hero, 'mobile_src'));

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
                            <picture>
                                @if ($mobileImg)
                                    <source media="(max-width: 680px)" srcset="{{ $mobileImg }}">
                                @endif
                                <x-img :src="$img" :alt="$alt" sizes="100vw" :eager="$i === 0" />
                            </picture>
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
                                <span class="sr-only">
                                    {{ $fromBanner
                                        ? ($slide->title ?: $slide->cta_label ?: $slide->eyebrow ?: 'Banner '.($i + 1))
                                        : $slide->name }}
                                </span>
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
    <section class="tools home-section" data-home-section aria-labelledby="customer-tools-title">
        <div class="wrap tools__inner">
            <div class="tools__head" data-home-reveal>
                <div>
                    <span class="eyebrow">Hành trình sở hữu</span>
                    <h2 id="customer-tools-title">Từ lựa chọn đầu tiên đến mỗi chuyến đi.</h2>
                </div>
                <p>Khám phá xe, dự toán chi phí và nhận hỗ trợ tại đại lý trong một hành trình liền mạch.</p>
            </div>

            <div class="tools__grid">
                @foreach ($customerTools as $tool)
                    <a class="tools__item {{ ($tool['feature'] ?? false) ? 'tools__item--feature' : '' }}"
                       href="{{ $tool['url'] }}" data-home-reveal>
                        @if (($tool['feature'] ?? false) && $leadImage)
                            <span class="tools__media" data-home-parallax aria-hidden="true">
                                <x-img :src="$leadImage" alt="" sizes="(max-width: 960px) 100vw, 50vw" />
                            </span>
                        @endif
                        <span class="tools__number" aria-hidden="true">{{ $tool['number'] }}</span>
                        <span class="tools__copy">
                            <span class="tools__name">{{ $tool['name'] }}</span>
                            <span class="tools__sub">{{ $tool['sub'] }}</span>
                        </span>
                        <span class="tools__arrow" aria-hidden="true">↗</span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ── Coverflow dải sản phẩm ─────────────────────────────────────── --}}
    <section class="block disc home-section" data-disc data-home-section>
        <div class="wrap">
            <div class="section__head disc__head">
                <span class="eyebrow">Ô tô điện VinFast</span>
                <h2>Xe đang được quan tâm</h2>
                <p>Bấm mũi tên hai bên để xem lần lượt cả dải xe đang bán tại đại lý.</p>
            </div>

            @if ($featured->isEmpty())
                <p class="empty">Chưa có {{ Str::lower(catalog_label('product.plural')) }} nào được đăng.</p>
            @else
                @if ($tabs->isNotEmpty())
                    <div class="disc__tabs">
                        <button type="button" class="disc__tab is-on" data-disc-tab="all">
                            Tất cả <span class="disc__count">{{ $featured->count() }}</span>
                        </button>
                        @foreach ($tabs as $tab)
                            <button type="button" class="disc__tab" data-disc-tab="{{ $tab->slug }}">
                                {{ $tab->name }}
                                <span class="disc__count">{{ $byCategory->get($tab->id)?->count() ?? 0 }}</span>
                            </button>
                        @endforeach
                    </div>
                @endif

                <div class="disc__stage" data-disc-stage data-home-reveal>
                    <button type="button" class="arrow arrow--round disc__arrow disc__arrow--prev"
                            data-disc-prev aria-label="Xe trước">‹</button>

                    <div class="disc__rail">
                        @foreach ($featured as $car)
                            @php
                                $variant = $car->variants->firstWhere('is_default', true) ?? $car->variants->first();
                                $priceNow = $car->price_from ?: $variant?->price;
                                $priceWas = $variant?->price_original;
                                $stats = collect($car->highlights ?? [])->take(3);
                            @endphp
                            <article class="disc__item" data-disc-item
                                     data-disc-cat="{{ $car->category->slug ?? 'all' }}">
                                <a class="disc__media" href="{{ route('products.show', $car->slug) }}">
                                    @if ($img = catalog_image(data_get($car->hero, 'src')))
                                        <x-img :src="$img" :alt="$car->name" sizes="(max-width: 960px) 100vw, 640px" />
                                    @else
                                        <span class="ph" style="position:absolute;inset:0">[ {{ $car->name }} ]</span>
                                    @endif
                                </a>

                                <div class="disc__info">
                                    @if ($car->category)
                                        <span class="disc__class">{{ $car->category->name }}</span>
                                    @endif
                                    <h3 class="disc__name">{{ $car->name }}</h3>

                                    @if ($stats->isNotEmpty())
                                        <dl class="disc__stats">
                                            @foreach ($stats as $stat)
                                                <div>
                                                    <dt>{{ $stat['label'] ?? '' }}</dt>
                                                    <dd>{{ trim(($stat['value'] ?? '').' '.($stat['unit'] ?? '')) }}</dd>
                                                </div>
                                            @endforeach
                                        </dl>
                                    @endif

                                    @if ($priceNow)
                                        <p class="disc__price">
                                            <span>Giá từ</span>
                                            <b>{{ catalog_money($priceNow) }}</b>
                                            @if ($priceWas && $priceWas > $priceNow)
                                                <s>{{ catalog_money($priceWas) }}</s>
                                            @endif
                                        </p>
                                    @endif

                                    <div class="disc__actions">
                                        <a class="btn btn--outline" href="{{ route('products.show', $car->slug) }}">Xem chi tiết</a>
                                        <a class="btn btn--accent"
                                           href="{{ Route::has('booking')
                                               ? route('booking', ['xe' => $car->slug])
                                               : route('products.show', $car->slug).'#form-dat-coc' }}">Đặt cọc</a>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    <button type="button" class="arrow arrow--round disc__arrow disc__arrow--next"
                            data-disc-next aria-label="Xe sau">›</button>
                </div>

                <p class="disc__pager"><span data-disc-count>01 / {{ str_pad($featured->count(), 2, '0', STR_PAD_LEFT) }}</span></p>
            @endif
        </div>
    </section>

    {{-- Lưới toàn dải sản phẩm.

         Coverflow bên trên cho xem từng xe một với ảnh lớn; lưới này cho thấy
         cả dải cùng lúc để khách so sánh nhanh mà không phải bấm mũi tên năm
         lần. Cùng nguồn dữ liệu với coverflow nên hai khối không lệch nhau. --}}
    @if ($discoveryProducts->count() > 1)
        <section class="block block--soft model-band home-section" data-home-section>
            <div class="wrap">
                <div class="section__head" data-home-reveal>
                    <span class="eyebrow">Toàn bộ dải sản phẩm</span>
                    <h2>{{ $discoveryProducts->count() }} mẫu xe đang bán</h2>
                    <p>Xem cả dải cùng lúc để so nhanh, hoặc bấm vào từng xe để đọc chi tiết.</p>
                </div>

                <ul class="model-grid" data-home-reveal>
                    @foreach ($discoveryProducts as $car)
                        @php
                            $carImg = catalog_image(data_get($car->hero, 'src'));
                            $carSpecs = collect($car->highlights ?? [])->take(2);
                        @endphp
                        <li class="model-tile">
                            <a class="model-tile__link" href="{{ route('products.show', $car->slug) }}">
                                <span class="model-tile__media">
                                    @if ($carImg)
                                        <x-img :src="$carImg" :alt="$car->name"
                                               sizes="(max-width: 680px) 100vw, (max-width: 1180px) 45vw, 400px" />
                                    @else
                                        <span class="ph" style="position:absolute;inset:0">[ {{ $car->name }} ]</span>
                                    @endif
                                </span>

                                <span class="model-tile__body">
                                    @if ($car->category)
                                        <span class="model-tile__cat">{{ $car->category->name }}</span>
                                    @endif
                                    <span class="model-tile__name">{{ $car->name }}</span>
                                    @if ($car->price_from)
                                        <span class="model-tile__price">Từ {{ catalog_money($car->price_from) }}</span>
                                    @endif

                                    <span class="model-tile__view" aria-hidden="true">Khám phá <b>↗</b></span>

                                    @if ($carSpecs->isNotEmpty())
                                        <span class="model-tile__specs">
                                            @foreach ($carSpecs as $spec)
                                                <span class="model-tile__spec">
                                                    <b>{{ trim(($spec['value'] ?? '').' '.($spec['unit'] ?? '')) }}</b>
                                                    <span>{{ $spec['label'] ?? '' }}</span>
                                                </span>
                                            @endforeach
                                        </span>
                                    @endif
                                </span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>
    @endif

    @include('frontend.partials.home-bands', ['lead' => $lead, 'posts' => $posts])
    </div>
@endsection
