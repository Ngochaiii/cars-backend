{{--
    Một hình thức đặt (đặt cọc / lái thử) dựng thành wizard 3 bước.

    Cả ba bước nằm trong CÙNG một <form>: JS chỉ ẩn/hiện từng bước, nên tắt
    JS là ba bước xếp liền nhau và nút Gửi vẫn hoạt động — không có nút chết,
    đúng nguyên tắc của frontend này.

    Ô nào lên bước 1 và ô select nào hiện thành lưới thẻ bấm đều khai ở
    config('catalog.frontend.booking') — thêm/bớt trường trong admin không
    phải sửa view.

    Trên điện thoại (JS bật, ≤960px) hai bước hiện cùng lúc thành một màn
    ngắn: các ô KHÔNG bắt buộc của bước 2 gấp vào <details> "Thêm thông tin"
    để khách chỉ phải điền tên + số điện thoại rồi bấm Gửi. Trên desktop
    <details> luôn mở và summary ẩn — bố cục cũ không đổi.

    Biến: $form (kèm fields) · $products · $selected · $hotline
--}}
@php
    $cfg      = (array) config('catalog.frontend.booking', []);
    $honeypot = config('catalog.leads.honeypot', 'website');
    $deposit  = $cfg['deposit'] ?? null;

    $sent = session('lead_success') && session('lead_form_key') === $form->key;

    // Lỗi validate tách theo bag $form->key (xem StoreLead) — old() không có
    // khái niệm bag nên chỉ đọc lại giá trị cũ khi CHÍNH form này vừa lỗi.
    $isThisForm = $errors->{$form->key}->any();
    $old = fn (string $key, mixed $default = null) => $isThisForm ? old($key, $default) : $default;

    $fields = $form->fields->reject(fn ($f) => $f->type === 'hidden');
    $step1  = $fields->filter(fn ($f) => in_array($f->key, (array) ($cfg['step1_fields'] ?? []), true));
    $step2  = $fields->reject(fn ($f) => $step1->contains($f));

    // Ô bắt buộc (tên, điện thoại) luôn hiện; ô còn lại gấp được trên mobile.
    $isRequired = fn ($f) => in_array('required', (array) $f->rules, true);
    $step2Must  = $step2->filter($isRequired);
    $step2More  = $step2->reject($isRequired);
    // Ô không bắt buộc đang lỗi (VD email sai) thì mục gấp phải mở sẵn.
    $moreHasError = $step2More->contains(fn ($f) => $errors->{$form->key}->has($f->key));

    // Ô select ít lựa chọn hiện thành lưới thẻ bấm như bản thiết kế; nhiều
    // hơn thì về dropdown cho khỏi vỡ lưới.
    $asCards = fn ($field) => $field->type === 'select'
        && in_array($field->key, (array) ($cfg['card_fields'] ?? []), true)
        && count((array) $field->options) <= (int) ($cfg['card_max_options'] ?? 4);
@endphp

{{-- ── Bước 3: màn cảm ơn, chỉ hiện sau khi gửi xong ───────────────── --}}
@if ($sent)
    <div class="booking__done">
        <div class="lead-done__mark" aria-hidden="true">&check;</div>
        <div class="booking__kicker">{{ $form->name }} — bước 3/3</div>
        <h2>Đã nhận yêu cầu của bạn</h2>
        <p>{{ session('lead_success') }}</p>

        <div class="booking__actions">
            <a class="btn btn--sm btn--outline" href="{{ route('booking', ['hinh-thuc' => $form->key]) }}">Tạo yêu cầu khác</a>
            <a class="btn btn--sm" href="{{ route('home') }}">Về trang chủ</a>
        </div>
    </div>
@else
    <form class="booking__form" method="POST" action="{{ route('leads.store', $form) }}" data-booking-form>
        @csrf

        {{-- Bẫy bot: ẩn bằng CSS, người thật không thấy nên luôn để trống. --}}
        <div class="honeypot" aria-hidden="true">
            <label for="bk-{{ $form->key }}-{{ $honeypot }}">Bỏ trống ô này</label>
            <input type="text" id="bk-{{ $form->key }}-{{ $honeypot }}" name="{{ $honeypot }}" tabindex="-1" autocomplete="off">
        </div>

        {{-- ── Bước 1: chọn xe + ô thuộc bước 1 ────────────────────── --}}
        <fieldset class="booking__fieldset" data-booking-pane-step="1">
            <legend class="sr-only">Bước 1 — chọn xe</legend>

            @if ($products->isNotEmpty())
                <div class="field field--full">
                    <span class="field__label">Chọn mẫu xe</span>
                    {{-- Trên mobile lưới này thành dải vuốt ngang (xem .booking__cars). --}}
                    <ul class="pick-grid booking__cars">
                        @foreach ($products as $car)
                            @php $checked = (int) $old('product_id', $selected?->id) === $car->id; @endphp
                            <li>
                                <label class="pick {{ $checked ? 'is-on' : '' }}">
                                    <input type="radio" name="product_id" value="{{ $car->id }}"
                                           data-pick-name="{{ $car->name }}" @checked($checked)>
                                    <b>{{ $car->name }}</b>
                                    @if ($car->price_from)
                                        <span>Từ {{ catalog_money($car->price_from) }}</span>
                                    @endif
                                </label>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @foreach ($step1 as $field)
                @include('frontend.partials.booking-field', [
                    'form' => $form, 'field' => $field, 'old' => $old, 'asCards' => $asCards,
                ])
            @endforeach

            <div class="booking__actions">
                <button class="btn btn--sm" type="button" data-booking-next>Tiếp tục — bước 2/3</button>
            </div>
        </fieldset>

        {{-- ── Bước 2: thông tin liên hệ + ô còn lại ───────────────── --}}
        <fieldset class="booking__fieldset" data-booking-pane-step="2">
            <legend class="sr-only">Bước 2 — thông tin liên hệ</legend>

            <div class="booking__fields">
                @foreach ($step2Must as $field)
                    @include('frontend.partials.booking-field', [
                        'form' => $form, 'field' => $field, 'old' => $old, 'asCards' => $asCards,
                    ])
                @endforeach
            </div>

            @if ($step2More->isNotEmpty())
                <details class="booking__more" data-booking-more open
                         @if ($moreHasError) data-booking-more-error @endif>
                    <summary>
                        <span>Thêm thông tin <small>(không bắt buộc)</small></span>
                        <i aria-hidden="true"></i>
                    </summary>
                    <div class="booking__fields">
                        @foreach ($step2More as $field)
                            @include('frontend.partials.booking-field', [
                                'form' => $form, 'field' => $field, 'old' => $old, 'asCards' => $asCards,
                            ])
                        @endforeach
                    </div>
                </details>
            @endif

            @if ($deposit && $form->key === ($cfg['forms'][0] ?? null))
                <div class="booking__deposit">
                    <div>
                        <span>Số tiền đặt cọc</span>
                        <b>{{ catalog_money($deposit) }}</b>
                    </div>
                    @if ($form->description)
                        <p>{{ $form->description }}</p>
                    @endif
                </div>
            @endif

            @if ($isThisForm)
                <p class="field__error booking__error">Vui lòng kiểm tra lại các ô còn thiếu để tiếp tục.</p>
            @endif

            <div class="booking__actions booking__actions--submit">
                <button class="btn btn--sm btn--outline" type="button" data-booking-prev>← Quay lại</button>
                <button class="btn btn--sm btn--accent" type="submit">{{ $form->name }}</button>
            </div>

            {{-- Hộp hỗ trợ ở cột trái ẩn trên mobile — để lại một dòng gọi ở đây. --}}
            @if (filled($hotline ?? null))
                <p class="booking__alt">
                    Hoặc gọi ngay <a href="tel:{{ preg_replace('/\s+/', '', $hotline) }}">{{ $hotline }}</a> để được tư vấn trực tiếp.
                </p>
            @endif
        </fieldset>
    </form>
@endif
