{{--
    Ô "Nhận báo giá lăn bánh" ngay dưới giá ở đầu trang xe: chỉ tên + số điện
    thoại, gửi vào form có sẵn ($quickQuote, khoá ở config) — khách từ quảng
    cáo không phải bấm sang trang khác mới để lại số được.

    Dùng chung data-lead-form với form cuối trang nên gửi bằng fetch, báo lỗi
    từng ô và bắn chuyển đổi Google Ads y hệt (frontend.js). Ô phụ của form gốc
    (email, ghi chú…) không kéo lên đây: càng ít ô càng dễ gửi.

    Biến: $quickQuote (Form) · $product · $quickQuoteUtm (nguồn khách)
--}}
@php
    $honeypot = config('catalog.leads.honeypot', 'website');
    $qqSent = session('lead_success') && session('lead_form_key') === $quickQuote->key;
    $qqId = 'qq-'.$quickQuote->key;
@endphp

<div class="quick-quote" data-quick-quote>
    <p class="quick-quote__title">
        <strong>Nhận báo giá lăn bánh {{ $product->name }}</strong>
        <span>Để lại số, tư vấn bán hàng gọi lại báo giá tại Bắc Giang, Bắc Ninh.</span>
    </p>

    @if ($qqSent)
        <p class="quick-quote__done" role="status">{{ session('lead_success') }}</p>
    @endif

    <form class="quick-quote__form" method="POST" action="{{ route('leads.store', $quickQuote) }}" data-lead-form>
        @csrf
        <input type="hidden" name="product_id" value="{{ $product->id }}">
        @foreach ($quickQuoteUtm ?? [] as $utmKey => $utmValue)
            <input type="hidden" name="{{ $utmKey }}" value="{{ $utmValue }}">
        @endforeach

        <div class="honeypot" aria-hidden="true">
            <label for="{{ $qqId }}-{{ $honeypot }}">Bỏ trống ô này</label>
            <input type="text" id="{{ $qqId }}-{{ $honeypot }}" name="{{ $honeypot }}" tabindex="-1" autocomplete="off">
        </div>

        <div class="field">
            <label class="sr-only" for="{{ $qqId }}-name">Họ và tên</label>
            <input type="text" id="{{ $qqId }}-name" name="name" placeholder="Họ và tên"
                   autocomplete="name" maxlength="100" required>
        </div>

        <div class="field">
            <label class="sr-only" for="{{ $qqId }}-phone">Số điện thoại</label>
            <input type="tel" id="{{ $qqId }}-phone" name="phone" placeholder="Số điện thoại"
                   autocomplete="tel" inputmode="tel" maxlength="20" required>
        </div>

        <button class="btn btn--accent" type="submit">Nhận báo giá</button>

        <div class="lead-form__status" data-lead-status role="status" aria-live="polite" hidden></div>
    </form>
</div>
