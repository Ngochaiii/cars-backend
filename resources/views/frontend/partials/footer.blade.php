@php
    $items    = catalog_menu(config('catalog.frontend.menus.footer', 'footer'));
    $siteName = catalog_setting('site_name', config('app.name'));
    $brandSub = catalog_setting('brand_sub');
    $hotline  = catalog_setting('hotline');
    $email    = catalog_setting('email');
    $address  = catalog_setting('address');

    // Mạng xã hội khai ở Cài đặt → chỉ hiện cái nào đã điền.
    $socials = collect([
        'Facebook' => catalog_setting('facebook'),
        'YouTube'  => catalog_setting('youtube'),
        'TikTok'   => catalog_setting('tiktok'),
        'Zalo'     => catalog_setting('zalo'),
    ])->filter();
@endphp

<footer class="site-footer">
    <div class="wrap site-footer__cols">
        <div class="site-footer__brand">
            <div class="brand">
                <span class="brand__name">{{ $siteName }}</span>
                @if (filled($brandSub))
                    <span class="brand__sub">{{ $brandSub }}</span>
                @endif
            </div>
            @if (filled($address))
                <p>{{ $address }}</p>
            @endif
            @if (filled($hotline))
                <p>Hotline: <a href="tel:{{ preg_replace('/\s+/', '', $hotline) }}">{{ $hotline }}</a></p>
            @endif
            @if (filled($email))
                <p>Email: <a href="mailto:{{ $email }}">{{ $email }}</a></p>
            @endif
        </div>

        <div class="site-footer__links">
            <div>
                <h3>{{ catalog_label('product.plural') }}</h3>
                <ul>
                    <li><a href="{{ route('products.index') }}">Tất cả {{ Str::lower(catalog_label('product.plural')) }}</a></li>
                    @if (catalog_feature('posts'))
                        <li><a href="{{ route('posts.index') }}">Tin tức &amp; ưu đãi</a></li>
                    @endif
                </ul>
            </div>

            @if ($items->isNotEmpty())
                <div>
                    <h3>Liên kết</h3>
                    <ul>
                        @foreach ($items as $item)
                            <li>
                                @if ($url = $item->resolvedUrl())
                                    <a href="{{ $url }}">{{ $item->label }}</a>
                                @else
                                    {{ $item->label }}
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if ($socials->isNotEmpty())
                <div>
                    <h3>Kết nối</h3>
                    <ul>
                        @foreach ($socials as $label => $value)
                            <li>
                                @if (str_starts_with((string) $value, 'http'))
                                    <a href="{{ $value }}" rel="noopener" target="_blank">{{ $label }}</a>
                                @else
                                    {{ $label }}: {{ $value }}
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>

    <div class="site-footer__bottom">
        <div class="wrap">
            <span>© {{ now()->year }} {{ $siteName }}@if (filled(catalog_setting('tax_code'))). MST {{ catalog_setting('tax_code') }}@endif</span>
            @if (filled($hotline))
                <span>Hotline {{ $hotline }}</span>
            @endif
        </div>
    </div>
</footer>
