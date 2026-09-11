{{--
    Ghé thăm showroom — mosaic 3 ảnh: một ảnh lớn bên trái (mặt tiền), hai
    ảnh nhỏ xếp dọc bên phải (lễ tân, xưởng dịch vụ), mỗi ô có chú thích đè
    góc dưới. Đầu khối là tiêu đề + địa chỉ, góc phải là nút chỉ đường.

    Có sẵn ảnh mặc định ở public/assets/images; địa chỉ và link chỉ đường lấy
    từ Cài đặt → Chung (address, map_url). Bật `showroom_off` là cả khối ẩn.
--}}
@unless (catalog_setting('showroom_off'))
@php
    $siteName = catalog_setting('site_name', config('app.name'));
    $showroomTitle = trim((string) catalog_setting('showroom_title')) ?: 'Ghé thăm '.$siteName;
    $showroomNote = trim((string) catalog_setting('showroom_note')) ?: 'Không gian đón tiếp quý khách';
    $address = trim(preg_replace('/\s*\R\s*/', ', ', (string) catalog_setting('address')));
    $mapUrl = trim((string) catalog_setting('map_url'));
    $hours = trim((string) catalog_setting('opening_hours'));

    $tiles = collect([
        [
            'image' => catalog_image(catalog_setting('showroom_image_1')) ?: asset('assets/images/showroom-mat-tien.jpg'),
            'caption' => trim((string) catalog_setting('showroom_caption_1')) ?: 'Điểm hẹn của những trải nghiệm '.Str::before($siteName, ' '),
            'kicker' => $siteName,
        ],
        [
            'image' => catalog_image(catalog_setting('showroom_image_2')) ?: asset('assets/images/showroom-le-tan.jpg'),
            'caption' => trim((string) catalog_setting('showroom_caption_2')) ?: 'Quầy lễ tân & tư vấn',
        ],
        [
            'image' => catalog_image(catalog_setting('showroom_image_3')) ?: asset('assets/images/showroom-xuong-dich-vu.jpg'),
            'caption' => trim((string) catalog_setting('showroom_caption_3')) ?: 'Xưởng dịch vụ chính hãng',
        ],
    ]);
@endphp

<section class="block home-section home-showroom" data-home-section aria-labelledby="home-showroom-title">
    <div class="wrap">
        <header class="home-showroom__head" data-home-reveal>
            <div>
                <span class="eyebrow">{{ $showroomNote }}</span>
                <h2 id="home-showroom-title">{{ $showroomTitle }}</h2>
                @if ($address !== '' || $hours !== '')
                    <p class="home-showroom__meta">
                        @if ($address !== '')
                            <span>
                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 21s-6-5.7-6-11a6 6 0 0 1 12 0c0 5.3-6 11-6 11Z"/><circle cx="12" cy="10" r="2.2"/></svg>
                                {{ $address }}
                            </span>
                        @endif
                        @if ($hours !== '')
                            <span>
                                <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="8.5"/><path d="M12 7.5V12l3 2"/></svg>
                                {{ $hours }}
                            </span>
                        @endif
                    </p>
                @endif
            </div>

            @if ($mapUrl !== '')
                <a class="btn btn--outline home-showroom__map" href="{{ $mapUrl }}" target="_blank" rel="noopener noreferrer">
                    Xem chỉ đường <span aria-hidden="true">↗</span>
                </a>
            @endif
        </header>

        <div class="home-showroom__grid" data-home-reveal>
            @foreach ($tiles as $i => $tile)
                <figure class="home-showroom__tile {{ $i === 0 ? 'home-showroom__tile--lead' : '' }}">
                    <x-img :src="$tile['image']" :alt="$tile['caption']"
                           :sizes="$i === 0 ? '(max-width: 960px) 100vw, 60vw' : '(max-width: 960px) 100vw, 38vw'" />
                    <figcaption>
                        @if ($i === 0)
                            <span class="home-showroom__kicker">{{ $tile['kicker'] }}</span>
                        @else
                            <span class="home-showroom__num">{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</span>
                        @endif
                        <b>{{ $tile['caption'] }}</b>
                    </figcaption>
                </figure>
            @endforeach
        </div>
    </div>
</section>
@endunless
