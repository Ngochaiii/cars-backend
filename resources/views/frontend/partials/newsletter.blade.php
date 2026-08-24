{{--
    Băng đăng ký nhận tin, ngay trên footer.

    Chỉ hiện khi có form khai ở config('catalog.frontend.newsletter_form') và
    form đó đang bật — POST thẳng vào LeadController như mọi form khác, nên
    honeypot/chống trùng/mail y hệt, không cần route riêng.
--}}
@php
    $key  = config('catalog.frontend.newsletter_form');
    $form = null;

    if (filled($key) && catalog_feature('forms')) {
        $form = \App\Support\Catalog::query('form')->where('key', $key)->where('is_active', true)->first();
    }

    $sent = $form && session('lead_success') && session('lead_form_key') === $form->key;
@endphp

@if ($form)
    <section class="newsletter">
        <div class="wrap newsletter__inner">
            <div>
                <div class="newsletter__title">{{ $form->name }}</div>
                <div class="newsletter__sub">{{ $form->description ?: 'Ưu đãi và tin dịch vụ từ đại lý — 1–2 email mỗi tháng.' }}</div>
            </div>

            @if ($sent)
                <div class="newsletter__sub" style="color:#7ee2a8;font-weight:600">
                    ✓ {{ session('lead_success') }}
                </div>
            @else
                <form class="newsletter__form" method="POST" action="{{ route('leads.store', $form->key) }}">
                    @csrf
                    <div class="honeypot" aria-hidden="true">
                        <input type="text" name="{{ config('catalog.leads.honeypot', 'website') }}" tabindex="-1" autocomplete="off">
                    </div>
                    <label class="honeypot" for="newsletter-email">Email</label>
                    <input id="newsletter-email" type="email" name="email" placeholder="Nhập email của bạn" required>
                    <button class="btn btn--light" type="submit">Đăng ký</button>
                </form>
            @endif
        </div>
    </section>
@endif
