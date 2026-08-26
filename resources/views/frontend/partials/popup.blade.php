{{--
    Popup thu lead, hiện sau một khoảng thời gian.

    Chỉ dựng khi Cài đặt trỏ tới một form ĐANG BẬT. Mọi điều kiện (chậm bao
    lâu, im mấy ngày, hiện ở đâu) đều đọc từ Cài đặt — popup là thứ dễ làm
    khách đóng tab nhất, phải chỉnh được mà không sửa code.

    Khối này luôn `hidden` từ server. Chỉ JS mới mở nó ra, nên tắt JS là popup
    không bao giờ xuất hiện — đúng ý: không có JS thì cũng không có gì chắn
    đường người ta đọc trang.
--}}
@php
    $key  = catalog_setting('popup_form');
    $form = null;

    if (filled($key) && catalog_feature('forms')) {
        $form = \App\Support\Catalog::query('form')
            ->where('key', $key)
            ->where('is_active', true)
            ->with('fields')
            ->first();
    }

    // Mặc định chỉ trang chủ, trừ khi Cài đặt bảo hiện mọi trang.
    $onHome = request()->routeIs('home');
    $show   = $form && ($onHome || catalog_setting('popup_everywhere'));

    // Vừa gửi form xong thì đừng chào lại bằng chính cái popup đó.
    $justSent = session('lead_form_key') === $key;
@endphp

@if ($show && ! $justSent)
    <div class="popup" data-popup
         data-popup-key="{{ $key }}"
         data-popup-delay="{{ (int) catalog_setting('popup_delay', 10) }}"
         data-popup-days="{{ (int) catalog_setting('popup_days', 7) }}"
         hidden>
        <div class="popup__backdrop" data-popup-close></div>

        <div class="popup__panel" role="dialog" aria-modal="true" aria-labelledby="popup-title">
            <button type="button" class="popup__close" data-popup-close aria-label="Đóng">&times;</button>

            <div class="popup__head">
                <h2 id="popup-title">{{ catalog_setting('popup_title', $form->name) }}</h2>
                @if ($text = catalog_setting('popup_text', $form->description))
                    <p>{{ $text }}</p>
                @endif
            </div>

            @include('frontend.partials.lead-form', ['form' => $form])
        </div>
    </div>
@endif
