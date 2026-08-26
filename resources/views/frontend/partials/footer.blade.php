@php
    $items    = catalog_menu(config('catalog.frontend.menus.footer', 'footer'));
    $siteName = catalog_setting('site_name', config('app.name'));
    $brandSub = catalog_setting('brand_sub');
    $hotline  = catalog_setting('hotline');
    $email    = catalog_setting('email');
    $address  = catalog_setting('address');

    // Link tới từng hình thức đặt (đặt cọc / lái thử) — lấy tên form thay vì
    // gõ cứng, admin đổi tên form là footer đổi theo.
    $bookingKeys  = array_values((array) config('catalog.frontend.booking.forms', []));
    $bookingForms = Route::has('booking')
        ? \App\Support\Catalog::query('form')
            ->whereIn('key', $bookingKeys)
            ->where('is_active', true)
            ->get()
            ->sortBy(fn ($form) => array_search($form->key, $bookingKeys, true))
            ->values()
        : collect();

    // Cột "Đại lý" gộp link tin tức với menu footer. Menu đã trỏ sẵn tới
    // /tin-tuc thì bỏ link cứng đi, không thì cột hiện hai dòng cùng đích.
    //
    // So bằng ĐƯỜNG DẪN chứ không so nguyên chuỗi: mục menu trả về đường dẫn
    // tương đối ('/tin-tuc') còn route() trả về URL tuyệt đối, so thẳng thì
    // không bao giờ khớp và dòng trùng vẫn lọt.
    $path = fn (?string $url) => trim(parse_url((string) $url, PHP_URL_PATH) ?: '', '/');

    $postsUrl     = catalog_feature('posts') ? route('posts.index') : null;
    $menuHasPosts = $postsUrl && $items->contains(
        fn ($item) => $path($item->resolvedUrl()) === $path($postsUrl)
    );

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
                <h3>Sản phẩm</h3>
                <ul>
                    <li><a href="{{ route('products.index') }}">Tất cả {{ Str::lower(catalog_label('product.plural')) }}</a></li>
                    @if (Route::has('accessories'))
                        <li><a href="{{ route('accessories') }}">Phụ kiện xe</a></li>
                    @endif
                </ul>
            </div>

            @if (Route::has('booking') || Route::has('services'))
                <div>
                    <h3>Dịch vụ</h3>
                    <ul>
                        @foreach ($bookingForms as $bookingForm)
                            <li><a href="{{ route('booking', ['hinh-thuc' => $bookingForm->key]) }}">{{ $bookingForm->name }}</a></li>
                        @endforeach
                        @if (Route::has('services'))
                            <li><a href="{{ route('services') }}">Trạm sạc &amp; bảo dưỡng</a></li>
                        @endif
                    </ul>
                </div>
            @endif

            <div>
                <h3>Đại lý</h3>
                <ul>
                    @if (Route::has('dealers'))
                        <li><a href="{{ route('dealers') }}">Hệ thống đại lý</a></li>
                    @endif
                    @if ($postsUrl && ! $menuHasPosts)
                        <li><a href="{{ $postsUrl }}">Tin tức &amp; ưu đãi</a></li>
                    @endif
                    @foreach ($items as $item)
                        @if ($url = $item->resolvedUrl())
                            <li><a href="{{ $url }}">{{ $item->label }}</a></li>
                        @endif
                    @endforeach
                </ul>
            </div>

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
