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
        <a class="brand" href="{{ route('home') }}">
            @if ($logo)
                <img src="{{ $logo }}" alt="{{ $siteName }}">
            @else
                <span class="brand__name">{{ $siteName }}</span>
                @if (filled($brandSub))
                    <span class="brand__sub">{{ $brandSub }}</span>
                @endif
            @endif
        </a>

        {{-- Mở menu trên mobile bằng checkbox — không một dòng JS nào. --}}
        <input class="nav-toggle" type="checkbox" id="nav-toggle" aria-hidden="true">
        <label class="nav-toggle__label" for="nav-toggle" aria-label="Mở menu">☰</label>

        <nav class="nav" aria-label="Menu chính">
            <ul class="nav__list">
                {{-- Menu chưa dựng trong admin thì vẫn có lối vào danh sách mặt hàng. --}}
                @forelse ($items as $item)
                    @include('frontend.partials.menu-item', ['item' => $item])
                @empty
                    <li class="nav__item">
                        <a class="nav__link" href="{{ route('products.index') }}">{{ catalog_label('product.plural') }}</a>
                    </li>
                    @if (catalog_feature('posts'))
                        <li class="nav__item">
                            <a class="nav__link" href="{{ route('posts.index') }}">Tin tức</a>
                        </li>
                    @endif
                @endforelse
            </ul>
        </nav>

        @if (filled($hotline))
            <div class="header__cta">
                <a class="btn" href="tel:{{ preg_replace('/\s+/', '', $hotline) }}">{{ $hotline }}</a>
            </div>
        @endif
    </div>
</header>
