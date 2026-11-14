{{--
    Tư vấn viên cuối bài — bản gọn của khối ở trang chủ, vừa đúng cột chữ.

    Đặt sau phần hỏi đáp: đọc xong bài và câu trả lời là lúc khách muốn hỏi
    người thật nhất. Nội dung lấy từ Cài đặt → Trang chủ (dùng chung với khối
    home-advisor), KHÔNG do AI viết trong từng bài — 50 bài mà 50 đoạn giới
    thiệu na ná nhau thì Google coi là nội dung lặp.

    Bật `advisor_off` là khối biến mất ở cả trang chủ lẫn trang bài.
--}}
@unless (catalog_setting('advisor_off'))
@php
    $siteName = catalog_setting('site_name', config('app.name'));

    $name  = trim((string) catalog_setting('advisor_name')) ?: 'Chuyên viên tư vấn';
    $role  = trim((string) catalog_setting('advisor_role')) ?: 'Tư vấn bán hàng';
    $phone = trim((string) (catalog_setting('advisor_phone') ?: catalog_setting('hotline')));
    $zalo  = trim((string) (catalog_setting('advisor_zalo') ?: catalog_setting('zalo')));
    $photo = catalog_image(catalog_setting('advisor_image')) ?: asset('assets/images/tu-van-vien.jpg');

    // Tên gọi thân mật ("em Lập") chỉ dùng được khi đã khai tên thật.
    $short = catalog_setting('advisor_name')
        ? Str::of($name)->explode(' ')->last()
        : 'tư vấn viên';

    $phoneHref = preg_replace('/\D+/', '', $phone);
    $zaloHref = str_starts_with($zalo, 'http')
        ? $zalo
        : ($zalo !== '' ? 'https://zalo.me/'.preg_replace('/\D+/', '', $zalo) : null);
@endphp

<aside class="article-advisor" id="tu-van-vien" aria-label="Tư vấn viên của quý khách">
    <div class="article-advisor__photo">
        <x-img :src="$photo" :alt="$name.' — '.$role.' '.$siteName" sizes="96px" />
    </div>

    <div class="article-advisor__body">
        <span class="article-advisor__eyebrow">Cần tư vấn thêm?</span>
        <p class="article-advisor__who"><b>{{ $name }}</b> · {{ $role }} {{ $siteName }}</p>
        <p class="article-advisor__text">
            Hỏi {{ $short }} về phiên bản phù hợp, giá lăn bánh, trả góp và lịch lái thử — trả lời trực tiếp, không qua tổng đài.
        </p>

        <div class="article-advisor__actions">
            @if ($phoneHref !== '')
                <a class="btn" href="tel:{{ $phoneHref }}">Gọi {{ $phone }}</a>
            @endif
            @if ($zaloHref)
                <a class="btn btn--outline" href="{{ $zaloHref }}" target="_blank" rel="noopener noreferrer">Nhắn Zalo</a>
            @endif
        </div>
    </div>
</aside>
@endunless
