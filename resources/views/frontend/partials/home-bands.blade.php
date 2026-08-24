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
@endphp

{{-- ── Băng ưu đãi ────────────────────────────────────────────────── --}}
@if (filled($offerTitle))
    <section class="offer">
        <div class="wrap offer__inner">
            @if ($note = catalog_setting('offer_note'))
                <span class="eyebrow eyebrow--light">{{ $note }}</span>
            @endif
            <h2>{{ $offerTitle }}</h2>
            @if ($text = catalog_setting('offer_text'))
                <p>{{ $text }}</p>
            @endif
            <div class="offer__actions">
                <a class="btn btn--light" href="{{ route('products.index') }}">Xem {{ Str::lower(catalog_label('product.plural')) }}</a>
                @if ($lead)
                    <a class="btn btn--ghost" href="{{ route('products.show', $lead->slug) }}#form-dat-coc">Đặt cọc ngay</a>
                @endif
            </div>
        </div>
    </section>
@endif

{{-- ── Pin & trạm sạc ─────────────────────────────────────────────── --}}
@if (filled($chargeTitle))
    <section class="block">
        <div class="wrap split">
            <div class="split__body">
                <span class="eyebrow">{{ catalog_setting('charging_note', 'Pin & trạm sạc') }}</span>
                <h2>{{ $chargeTitle }}</h2>
                @if ($text = catalog_setting('charging_text'))
                    <p>{{ $text }}</p>
                @endif
                @if ($lead)
                    <a class="btn" href="{{ route('products.show', $lead->slug) }}#fuel-calc">Tính chi phí sạc</a>
                @endif
            </div>

            <div class="split__media">
                @if ($img = catalog_image(catalog_setting('charging_image')))
                    <img src="{{ $img }}" alt="{{ $chargeTitle }}" loading="lazy">
                @else
                    <div class="ph" style="height:100%">[ trạm sạc ]</div>
                @endif
            </div>
        </div>
    </section>
@endif

{{-- ── Khám phá đại lý (lấy từ bài viết) ──────────────────────────── --}}
@if ($posts->isNotEmpty())
    <section class="block">
        <div class="wrap">
            <div class="section__head"><h2>Khám phá đại lý</h2></div>

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
    <section class="block block--soft">
        <div class="wrap split split--media-first">
            <div class="split__media">
                @if ($img = catalog_image(catalog_setting('care_image')))
                    <img src="{{ $img }}" alt="{{ $careTitle }}" loading="lazy">
                @else
                    <div class="ph" style="height:100%">[ cố vấn dịch vụ ]</div>
                @endif
            </div>

            <div class="split__body">
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

                @if ($lead)
                    <div class="hero__actions">
                        <a class="btn" href="{{ route('products.show', $lead->slug) }}#form-dat-lich-lai-thu">Đăng ký lái thử</a>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endif
