{{--
    Tư vấn viên đồng hành — băng navy tối giữa trang chủ.

    Bố cục: chân dung dọc bên trái (bảng tên đè góc dưới, huy hiệu 24/7 nổi
    góc phải) · bên phải là tiêu đề hai dòng, tên + chức danh, đoạn giới
    thiệu, câu trích dẫn, ba cam kết và ba nút: gọi trực tiếp, Zalo, yêu cầu
    gọi lại.

    Khác các băng khác, khối này có sẵn nội dung mặc định (ảnh ở
    public/assets/images) để đại lý nhìn thấy ngay; mọi dòng chữ đều ghi đè
    được ở Cài đặt → Trang chủ. Bật `advisor_off` là cả khối biến mất.

    Biến: $lead (mặt hàng đầu, có thể null)
--}}
@unless (catalog_setting('advisor_off'))
@php
    $siteName = catalog_setting('site_name', config('app.name'));

    $advisorName  = trim((string) catalog_setting('advisor_name')) ?: 'Chuyên viên tư vấn';
    $advisorRole  = trim((string) catalog_setting('advisor_role')) ?: 'Tư vấn bán hàng';
    $advisorPhone = trim((string) (catalog_setting('advisor_phone') ?: catalog_setting('hotline')));
    $advisorZalo  = trim((string) (catalog_setting('advisor_zalo') ?: catalog_setting('zalo')));
    $advisorImage = catalog_image(catalog_setting('advisor_image')) ?: asset('assets/images/tu-van-vien.jpg');

    $title1 = trim((string) catalog_setting('advisor_title')) ?: 'Một người đồng hành.';
    $title2 = trim((string) catalog_setting('advisor_title_2')) ?: 'Trọn vẹn mọi trải nghiệm.';

    // Tên gọi thân mật ("em Lập") chỉ dùng được khi đã khai tên thật.
    $short = catalog_setting('advisor_name')
        ? Str::of($advisorName)->explode(' ')->last()
        : 'tư vấn viên';
    $text = trim((string) catalog_setting('advisor_text'))
        ?: $advisorName.' luôn sẵn sàng hỗ trợ Quý khách 24/7. Mọi thắc mắc về các dòng xe, phiên bản, giá lăn bánh và chính sách ưu đãi, hãy liên hệ '.$short.' để nhận được sự hỗ trợ sớm nhất.';
    $quote = trim((string) catalog_setting('advisor_quote'))
        ?: 'Trân trọng được đồng hành và hỗ trợ Quý khách trên mọi hành trình.';

    $points = catalog_rows(catalog_setting('advisor_points'));
    if ($points->isEmpty()) {
        $points = collect([
            ['Chọn xe phù hợp', 'Tư vấn mẫu xe và phiên bản'],
            ['Chính sách rõ ràng', 'Giá xe, tài chính và ưu đãi'],
            ['Đồng hành tận tâm', 'Từ lái thử đến bàn giao xe'],
        ]);
    }
    $points = $points->take(3);

    $phoneHref = preg_replace('/\D+/', '', $advisorPhone);
    $zaloHref = str_starts_with($advisorZalo, 'http')
        ? $advisorZalo
        : ($advisorZalo !== '' ? 'https://zalo.me/'.preg_replace('/\D+/', '', $advisorZalo) : null);
    $callbackUrl = Route::has('booking')
        ? route('booking', ['hinh-thuc' => 'dat-lich-lai-thu'])
        : ($lead ? route('products.show', $lead->slug).'#form-dat-lich-lai-thu' : null);

    // Chữ viết tắt trên bảng tên: "Nguyễn Hữu Lập" → "HL". Chưa khai tên
    // thật thì bỏ ô này — "TV" của "tư vấn" chẳng nói lên điều gì.
    $initials = catalog_setting('advisor_name')
        ? Str::of($advisorName)->explode(' ')->filter()->take(-2)
            ->map(fn ($w) => Str::upper(Str::substr($w, 0, 1)))->implode('')
        : '';
@endphp

<section class="home-advisor home-section" data-home-section aria-labelledby="home-advisor-title"
         data-watermark="{{ Str::upper(Str::before($siteName, ' ')) }}">
    <div class="wrap home-advisor__inner">
        <div class="home-advisor__media" data-home-reveal>
            <figure class="home-advisor__photo">
                <x-img :src="$advisorImage" :alt="$advisorName.' — '.$advisorRole.' '.$siteName"
                       sizes="(max-width: 960px) 100vw, 40vw" />

                <figcaption class="home-advisor__plate">
                    @if ($initials !== '')
                        <span class="home-advisor__initials" aria-hidden="true">{{ $initials }}</span>
                    @endif
                    <span>
                        <b>{{ $advisorName }}</b>
                        <small>{{ $advisorRole }} · {{ $siteName }}</small>
                    </span>
                </figcaption>
            </figure>

            <div class="home-advisor__badge" aria-hidden="true">
                <svg viewBox="0 0 24 24"><path d="M4 13a8 8 0 0 1 16 0M4 13v4a2 2 0 0 0 2 2h1v-6H6a2 2 0 0 0-2 2ZM20 13v4a2 2 0 0 1-2 2h-1v-6h1a2 2 0 0 1 2 2ZM17 19v1a2 2 0 0 1-2 2h-3"/></svg>
                <span><b>24/7</b>Hỗ trợ trực tiếp</span>
            </div>
        </div>

        <div class="home-advisor__body" data-home-reveal>
            <span class="eyebrow">Tư vấn viên của quý khách</span>
            <h2 id="home-advisor-title">
                {{ $title1 }}
                <em>{{ $title2 }}</em>
            </h2>

            <div class="home-advisor__who">
                <div>
                    <b>{{ $advisorName }}</b>
                    <span>{{ $advisorRole }} · {{ $siteName }}</span>
                </div>
                <span class="home-advisor__live"><i></i>Thông tin trực tiếp</span>
            </div>

            <p class="home-advisor__text">{{ $text }}</p>

            <blockquote class="home-advisor__quote">
                “{{ $advisorName }} — {{ $quote }}”
            </blockquote>

            <ul class="home-advisor__points">
                @foreach ($points as $i => [$head, $sub])
                    <li>
                        <span class="home-advisor__icon" aria-hidden="true">
                            @if ($i === 0)
                                <svg viewBox="0 0 24 24"><path d="M5 13l1.6-4.2A2 2 0 0 1 8.5 7.5h7a2 2 0 0 1 1.9 1.3L19 13M5 13h14v4H5zM7 17v2M17 17v2M8 15h.01M16 15h.01"/></svg>
                            @elseif ($i === 1)
                                <svg viewBox="0 0 24 24"><path d="M7 3h7l4 4v14H7zM14 3v4h4M9.5 12h5M9.5 15.5h5"/></svg>
                            @else
                                <svg viewBox="0 0 24 24"><path d="M7.5 12.5a3.5 3.5 0 1 1 0-7 3.5 3.5 0 0 1 0 7ZM11 9h9l-1.5 2 1.5 2h-2l-1-1-1 1h-2l-1-1"/></svg>
                            @endif
                        </span>
                        <span>
                            <b>{{ $head }}</b>
                            @if (filled($sub))
                                <small>{{ $sub }}</small>
                            @endif
                        </span>
                    </li>
                @endforeach
            </ul>

            <div class="home-advisor__actions">
                @if ($advisorPhone !== '')
                    <a class="home-advisor__call" href="tel:{{ $phoneHref }}">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7.2 3.5 9.7 8l-2 1.7a15.5 15.5 0 0 0 6.6 6.6l1.7-2 4.5 2.5-.8 3.2c-.2.8-.9 1.3-1.7 1.3C9.6 20.7 3.3 14.4 2.7 6c-.1-.8.5-1.5 1.3-1.7l3.2-.8Z"/></svg>
                        <span><small>Gọi trực tiếp {{ $short }}</small><b>{{ $advisorPhone }}</b></span>
                    </a>
                @endif
                @if ($zaloHref)
                    <a class="btn btn--ghost" href="{{ $zaloHref }}" target="_blank" rel="noopener noreferrer">Nhắn Zalo</a>
                @endif
                @if ($callbackUrl)
                    <a class="btn btn--ghost" href="{{ $callbackUrl }}">Yêu cầu gọi lại <span aria-hidden="true">↗</span></a>
                @endif
            </div>

            @if ($advisorPhone !== '')
                <p class="home-advisor__note"><i></i>Kết nối trực tiếp với {{ $advisorName }} — không qua tổng đài.</p>
            @endif
        </div>
    </div>
</section>
@endunless
