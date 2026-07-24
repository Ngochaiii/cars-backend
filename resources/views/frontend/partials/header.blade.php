@php
    $logo    = catalog_image(catalog_setting('logo'));
    $hotline = catalog_setting('hotline');
    $items   = catalog_menu(config('catalog.frontend.menus.header', 'header'));
@endphp

<header class="site-header">
    <div class="wrap site-header__inner">
        <a class="brand" href="{{ route('home') }}">
            @if ($logo)
                <img src="{{ $logo }}" alt="{{ catalog_setting('site_name', config('app.name')) }}">
            @else
                {{ catalog_setting('site_name', config('app.name')) }}
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
                <a class="btn btn--ghost" href="tel:{{ preg_replace('/\s+/', '', $hotline) }}">{{ $hotline }}</a>
            </div>
        @endif
    </div>
</header>
