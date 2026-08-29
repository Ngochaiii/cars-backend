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
@endphp

{{-- ── Băng ưu đãi ────────────────────────────────────────────────── --}}
@if (filled($offerTitle))
    <section class="offer home-section" data-home-section data-watermark="ĐẶC QUYỀN">
        <div class="wrap offer__inner">
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
    <section class="block home-section home-feature home-feature--charge" data-home-section>
        <div class="wrap split home-feature__grid">
            <div class="split__body" data-home-reveal>
                <span class="eyebrow">{{ catalog_setting('charging_note', 'Pin & trạm sạc') }}</span>
                <h2>{{ $chargeTitle }}</h2>
                @if ($text = catalog_setting('charging_text'))
                    <p>{{ $text }}</p>
                @endif
                <div class="hero__actions">
                    @if (Route::has('services'))
                        <a class="btn btn--light" href="{{ route('services') }}">Xem trạm sạc</a>
                    @endif
                    @if ($lead)
                        <a class="btn {{ Route::has('services') ? 'btn--ghost' : 'btn--light' }}"
                           href="{{ route('products.show', $lead->slug) }}#fuel-calc">Tính chi phí sạc</a>
                    @endif
                </div>
            </div>

            <div class="split__media" data-home-reveal data-home-parallax>
                @if ($img = catalog_image(catalog_setting('charging_image')))
                    <x-img :src="$img" :alt="$chargeTitle" sizes="(max-width: 960px) 100vw, 50vw" />
                @else
                    <div class="ph" style="height:100%">[ trạm sạc ]</div>
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
