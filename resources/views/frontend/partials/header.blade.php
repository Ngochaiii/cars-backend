{{--
    Header dính đỉnh trang: thương hiệu trái · menu giữa · nút gọi phải.

    Cài đặt tuỳ chọn (Cài đặt → khoá tự do, không có thì tự ẩn):
      brand_sub  — dòng chữ nhỏ cạnh tên (VD "Bắc Giang")
      promo_text — băng khuyến mãi đen trên cùng
      promo_url  — link cho băng đó
--}}
@php
    $logo      = catalog_image(catalog_setting('logo'));
    $siteName  = catalog_setting('site_name', config('app.name'));
    $brandSub  = catalog_setting('brand_sub');
    $hotline   = catalog_setting('hotline');
    $promoText = catalog_setting('promo_text');
    $promoUrl  = catalog_setting('promo_url');
    $items     = catalog_menu(config('catalog.frontend.menus.header', 'header'));
@endphp

@if (filled($promoText))
    <div class="promo-bar">
        @if (filled($promoUrl))
            <a href="{{ $promoUrl }}">{{ $promoText }}</a>
        @else
            {{ $promoText }}
        @endif
    </div>
@endif

<header class="site-header">
    <div class="wrap site-header__inner">
        {{-- Logo và tên đại lý đứng cạnh nhau; tên đọc được từ alt của ảnh nên
             phần chữ chỉ là lớp nhìn (aria-hidden) — mobile ẩn nó đi, chỉ còn logo. --}}
        <a class="brand @if ($logo) brand--logo @endif" href="{{ route('home') }}">
            @if ($logo)
                <x-img :src="$logo" :alt="$siteName" sizes="220px" eager />
            @else
                <span class="brand__emblem" aria-hidden="true">V</span>
            @endif
            <span class="brand__wordmark" @if ($logo) aria-hidden="true" @endif>
                <span class="brand__name">{{ $siteName }}</span>
                @if (filled($brandSub))
                    <span class="brand__sub">{{ $brandSub }}</span>
                @endif
            </span>
        </a>

        <button class="nav-toggle__button" type="button" aria-label="Mở menu"
                aria-expanded="false" aria-controls="main-nav" data-nav-toggle>
            <span></span><span></span><span></span>
        </button>

        <nav class="nav" id="main-nav" aria-label="Menu chính">
            <ul class="nav__list">
                {{-- Menu chưa dựng trong admin thì vẫn có lối vào danh sách mặt hàng. --}}
                @forelse ($items as $item)
                    @include('frontend.partials.menu-item', ['item' => $item])
                @empty
                    <li class="nav__item">
                        <a class="nav__link" href="{{ route('products.index') }}">{{ catalog_label('product.plural') }}</a>
                    </li>
                    @if (Route::has('accessories'))
                        <li class="nav__item">
                            <a class="nav__link" href="{{ route('accessories') }}">Phụ kiện</a>
                        </li>
                    @endif
                    @if (Route::has('services'))
                        <li class="nav__item">
                            <a class="nav__link" href="{{ route('services') }}">Trạm sạc &amp; Dịch vụ</a>
                        </li>
                    @endif
                    @if (catalog_feature('posts'))
                        <li class="nav__item">
                            <a class="nav__link" href="{{ route('posts.index') }}">Tin tức</a>
                        </li>
                    @endif
                @endforelse
            </ul>
        </nav>

        <div class="header__cta">
            @if (Route::has('search'))
                <form class="header__search" method="GET" action="{{ route('search') }}" role="search">
                    <label class="sr-only" for="header-q">Tìm kiếm</label>
                    <input id="header-q" type="search" name="q" placeholder="Tìm xe…">
                </form>
            @endif

            @if (filled($hotline))
                <a class="header__tel" href="tel:{{ preg_replace('/\s+/', '', $hotline) }}">{{ $hotline }}</a>
            @endif

            @if (Route::has('booking'))
                <a class="btn btn--sm" href="{{ route('booking') }}">
                    <span class="header__cta-long">{{ catalog_setting('header_cta', catalog_label('cta.deposit').' & '.Str::lower(catalog_label('cta.test_drive'))) }}</span>
                    <span class="header__cta-short">{{ catalog_label('cta.deposit') }}</span>
                </a>
            @elseif (filled($hotline))
                <a class="btn btn--sm" href="tel:{{ preg_replace('/\s+/', '', $hotline) }}">Gọi tư vấn</a>
            @endif
        </div>
    </div>
    <button class="nav__backdrop" type="button" aria-label="Đóng menu" data-nav-close></button>
</header>

{{-- Trên điện thoại, hai ý định có giá trị nhất luôn ở trong tầm ngón tay.
     Trang chi tiết xe có order-bar riêng nên CSS sẽ ẩn thanh này tại đó. --}}
@if (filled($hotline) || Route::has('booking'))
    <nav class="mobile-contact-dock" aria-label="Liên hệ nhanh">
        @if (filled($hotline))
            <a class="mobile-contact-dock__item" href="tel:{{ preg_replace('/\s+/', '', $hotline) }}">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M7.2 3.5 9.7 8l-2 1.7a15.5 15.5 0 0 0 6.6 6.6l1.7-2 4.5 2.5-.8 3.2c-.2.8-.9 1.3-1.7 1.3C9.6 20.7 3.3 14.4 2.7 6c-.1-.8.5-1.5 1.3-1.7l3.2-.8Z"/>
                </svg>
                <span><small>Hotline tư vấn</small>{{ $hotline }}</span>
            </a>
        @endif

        @if (Route::has('booking'))
            <a class="mobile-contact-dock__item mobile-contact-dock__item--primary" href="{{ route('booking') }}">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M6.5 3v3M17.5 3v3M4 8.5h16M5.5 5h13A1.5 1.5 0 0 1 20 6.5v12a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 4 18.5v-12A1.5 1.5 0 0 1 5.5 5Z"/>
                    <path d="m9 14 2 2 4-4"/>
                </svg>
                <span><small>Đăng ký ngay</small>{{ catalog_label('cta.test_drive') }} &amp; {{ Str::lower(catalog_label('cta.deposit')) }}</span>
            </a>
        @endif
    </nav>
@endif
