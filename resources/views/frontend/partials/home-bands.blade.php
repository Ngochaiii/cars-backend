{{--
    Các băng nội dung ở nửa dưới trang chủ:
      ưu đãi · pin & trạm sạc · khám phá đại lý · chăm sóc chủ xe

    Ba băng đầu đọc từ Cài đặt (khoá tự do) nên sửa được trong admin; khoá
    trống thì cả khối biến mất. "Khám phá đại lý" lấy từ bài viết.

    Biến: $lead (mặt hàng đầu, có thể null) · $posts
--}}
@php
    $offerTitle = catalog_setting('offer_title');
    $chargeTitle = catalog_setting('charging_title');
    $careTitle  = catalog_setting('care_title');

    // "10 năm|Bảo hành xe và pin;24/7|Cứu hộ toàn tỉnh" → 4 ô chỉ số
    $careStats = collect(explode(';', (string) catalog_setting('care_stats')))
        ->map(fn ($pair) => array_pad(explode('|', trim($pair), 2), 2, ''))
        ->filter(fn ($pair) => filled($pair[0]));
    $offerImage = $lead ? catalog_image(data_get($lead->hero, 'src')) : null;
    $editorialFallbacks = collect([
        catalog_image(catalog_setting('care_image')),
        catalog_image(catalog_setting('charging_image')),
        $offerImage,
    ])->filter()->values();

    $chargeActions = collect([
        [
            'label' => 'Tìm trạm sạc',
            'desc' => 'Xem hạ tầng sạc và dịch vụ hỗ trợ gần bạn.',
            'url' => Route::has('services') ? route('services') : null,
        ],
        [
            'label' => 'Tính chi phí sạc',
            'desc' => 'Dự toán theo quãng đường và nhu cầu di chuyển thực tế.',
            'url' => $lead ? route('products.show', $lead->slug).'#fuel-calc' : null,
        ],
    ])->filter(fn ($action) => filled($action['url']))->values();

    // Panel tìm trạm đã làm luôn việc "tìm trạm sạc", nên chân panel chỉ còn
    // hai lối đi tiếp: xem cả trang trạm & dịch vụ, và tính chi phí sạc.
    $chargeFootLinks = collect([
        [
            'label' => 'Xem tất cả trạm & dịch vụ',
            'url' => Route::has('services') ? route('services') : null,
        ],
        [
            'label' => 'Tính chi phí sạc',
            'url' => $lead ? route('products.show', $lead->slug).'#fuel-calc' : null,
        ],
    ])->filter(fn ($link) => filled($link['url']))->values();

    // Chưa có trạm nào và cũng chưa nối API thì không dựng công cụ tìm kiếm —
    // quay về dải lối tắt cũ thay vì để một ô tìm kiếm không tra được gì.
    $hasFinder = filled(catalog_setting('stations')) || filled(catalog_setting('stations_api'));
    $stageVariant = $hasFinder
        ? 'home-charge__stage--finder'
        : ($chargeActions->isEmpty() ? 'home-charge__stage--media' : '');
@endphp

{{-- ── Băng ưu đãi ────────────────────────────────────────────────── --}}
@if (filled($offerTitle))
    <section class="offer {{ $offerImage ? '' : 'offer--text-only' }} home-section"
             data-home-section data-watermark="ĐẶC QUYỀN">
        <div class="wrap offer__inner">
            <div class="offer__chapter" aria-hidden="true">
                <span>01</span>
                <i></i>
            </div>

            <div class="offer__copy" data-home-reveal>
                @if ($note = catalog_setting('offer_note'))
                    <span class="eyebrow eyebrow--light">{{ $note }}</span>
                @endif
                <h2>{{ $offerTitle }}</h2>
                @if ($text = catalog_setting('offer_text'))
                    <p>{{ $text }}</p>
                @endif
                <div class="offer__actions">
                    <a class="btn btn--light" href="{{ route('products.index') }}">Xem {{ Str::lower(catalog_label('product.plural')) }}</a>
                    @if (Route::has('booking'))
                        <a class="btn btn--ghost" href="{{ route('booking') }}">Đặt cọc ngay</a>
                    @elseif ($lead)
                        <a class="btn btn--ghost" href="{{ route('products.show', $lead->slug) }}#form-dat-coc">Đặt cọc ngay</a>
                    @endif
                </div>
            </div>

            @if ($offerImage)
                <div class="offer__media" data-home-reveal data-home-parallax>
                    <x-img :src="$offerImage" :alt="$lead->name" sizes="(max-width: 960px) 100vw, 50vw" />
                    <span>{{ $lead->name }}</span>
                </div>
            @endif
        </div>
    </section>
@endif

{{-- ── Pin & trạm sạc ─────────────────────────────────────────────── --}}
@if (filled($chargeTitle))
    <section class="home-charge home-section" data-home-section aria-labelledby="home-charge-title">
        <div class="wrap">
            <header class="home-charge__head" data-home-reveal>
                <div>
                    <span class="eyebrow">{{ catalog_setting('charging_note', 'Pin & trạm sạc') }}</span>
                    <h2 id="home-charge-title">{{ $chargeTitle }}</h2>
                </div>
                @if ($text = catalog_setting('charging_text'))
                    <p>{{ $text }}</p>
                @endif
            </header>

            <div class="home-charge__stage {{ $stageVariant }}">
                <div class="home-charge__media" data-home-reveal data-home-parallax>
                    @if ($img = catalog_image(catalog_setting('charging_image')))
                        <x-img :src="$img" :alt="$chargeTitle" sizes="(max-width: 960px) 100vw, 50vw" />
                    @else
                        <div class="ph" style="height:100%">[ trạm sạc ]</div>
                    @endif
                </div>

                @if ($hasFinder)
                    {{-- Nửa phải là công cụ: nhập vị trí → trạm gần nhất → chỉ đường.
                         Hai lối tắt cũ tụt xuống chân panel, không mất đường vào. --}}
                    @include('frontend.partials.station-finder', ['footLinks' => $chargeFootLinks])
                @elseif ($chargeActions->isNotEmpty())
                    <nav class="home-charge__rail" aria-label="Tiện ích sạc" data-home-reveal>
                        <span class="home-charge__rail-label">Tiện ích sạc</span>

                        @foreach ($chargeActions as $action)
                            <a class="home-charge__action" href="{{ $action['url'] }}">
                                <span class="home-charge__number">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                <span class="home-charge__action-copy">
                                    <b>{{ $action['label'] }}</b>
                                    <small>{{ $action['desc'] }}</small>
                                </span>
                                <span class="home-charge__arrow" aria-hidden="true">↗</span>
                            </a>
                        @endforeach
                    </nav>
                @endif
            </div>
        </div>
    </section>
@endif

{{-- ── Khám phá đại lý (lấy từ bài viết) ──────────────────────────── --}}
@if ($posts->isNotEmpty())
    <section class="block home-section home-editorial" data-home-section>
        <div class="wrap">
            <div class="section__head home-editorial__head" data-home-reveal>
                <span class="eyebrow">Câu chuyện &amp; trải nghiệm</span>
                <h2>Khám phá đại lý</h2>
            </div>

            @php $tileLead = $posts->first(); $tileRest = $posts->slice(1)->take(2); @endphp

            <div class="tiles {{ $tileRest->isEmpty() ? 'tiles--solo' : '' }}" data-home-reveal>
                <a class="tile" href="{{ route('posts.show', $tileLead->slug) }}">
                    <div class="tile__media">
                        @php $cover = catalog_image($tileLead->cover) ?: $editorialFallbacks->first(); @endphp
                        @if ($cover)
                            <x-img :src="$cover" :alt="$tileLead->title" sizes="(max-width: 960px) 100vw, 55vw" />
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
                                    @php
                                        $cover = catalog_image($tile->cover)
                                            ?: $editorialFallbacks->get($loop->index + 1)
                                            ?: $editorialFallbacks->first();
                                    @endphp
                                    @if ($cover)
                                        <x-img :src="$cover" :alt="$tile->title" sizes="(max-width: 960px) 100vw, 30vw" />
                                    @else
                                        <div class="ph" style="height:100%">[ {{ $tile->title }} ]</div>
                                    @endif
                                </div>
                                <div class="tile__kicker">{{ $tile->category?->name ?: $tile->published_at?->format('d/m/Y') }}</div>
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

{{-- ── Chăm sóc chủ xe ────────────────────────────────────────────── --}}
@if (filled($careTitle))
    <section class="block block--soft home-section home-feature home-feature--care" data-home-section>
        <div class="wrap split split--media-first home-feature__grid">
            <div class="split__media" data-home-reveal data-home-parallax>
                @if ($img = catalog_image(catalog_setting('care_image')))
                    <x-img :src="$img" :alt="$careTitle" sizes="(max-width: 960px) 100vw, 50vw" />
                @else
                    <div class="ph" style="height:100%">[ cố vấn dịch vụ ]</div>
                @endif
            </div>

            <div class="split__body" data-home-reveal>
                <span class="eyebrow">{{ catalog_setting('care_note', 'Chăm sóc chủ xe') }}</span>
                <h2>{{ $careTitle }}</h2>

                @if ($careStats->isNotEmpty())
                    <div class="stat-grid">
                        @foreach ($careStats as [$value, $label])
                            <div>
                                <b>{{ $value }}</b>
                                <span>{{ $label }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif

                @if (Route::has('booking'))
                    <div class="hero__actions">
                        <a class="btn" href="{{ route('booking', ['hinh-thuc' => 'dat-lich-lai-thu']) }}">Đăng ký lái thử</a>
                    </div>
                @elseif ($lead)
                    <div class="hero__actions">
                        <a class="btn" href="{{ route('products.show', $lead->slug) }}#form-dat-lich-lai-thu">Đăng ký lái thử</a>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endif
