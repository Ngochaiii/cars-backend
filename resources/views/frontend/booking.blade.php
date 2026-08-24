{{--
    Trang "Đặt cọc & lái thử" — bám bố cục bản thiết kế:
    cột trái 480px (tóm tắt 3 bước, hộp hỗ trợ) · cột phải là wizard.

    Mỗi hình thức là một Form khai ở config('catalog.frontend.booking.forms')
    và POST thẳng vào /gui-form/{form} như mọi form khác — honeypot, chống
    trùng, mail y hệt. JS chỉ chuyển bước; tắt JS thì cả ba bước nằm liền
    nhau trong cùng một <form>, bấm Gửi vẫn chạy.

    Biến: $forms · $products · $mode (form mở sẵn) · $selected (xe chọn sẵn)
--}}
@php
    // Form vừa gửi hỏng phải là form mở sẵn, không thì người dùng bị ném về
    // tab kia và không bao giờ thấy lỗi của mình. Gửi xong (lead_success)
    // cũng vậy — bước 3 nằm trong form đó.
    $activeKey = $forms->first(fn ($form) => $errors->{$form->key}->any())?->key
        ?? ($forms->contains('key', session('lead_form_key')) ? session('lead_form_key') : null)
        ?? $mode->key;

    $hotline = catalog_setting('hotline');
    $hours   = catalog_setting('opening_hours');
    $zalo    = catalog_setting('zalo');
@endphp

@extends('frontend.layout', [
    'title'       => $mode->name.' — '.catalog_setting('site_name', config('app.name')),
    'description' => $mode->description,
    'canonical'   => route('booking'),
])

@section('content')
    <div class="booking">
        {{-- ── Cột trái: tiến trình ───────────────────────────────────── --}}
        <aside class="booking__rail">
            @foreach ($forms as $form)
                <div class="booking__intro {{ $form->key === $activeKey ? 'is-on' : '' }}"
                     data-booking-intro="{{ $form->key }}" @if ($form->key !== $activeKey) hidden @endif>
                    <div class="booking__kicker" data-booking-kicker>{{ $form->name }} — bước 1/3</div>
                    <h1>{{ $form->name }}</h1>
                    @if ($form->description)
                        <p class="booking__lede">{{ $form->description }}</p>
                    @endif
                </div>
            @endforeach

            <ol class="booking__steps" data-booking-steps>
                @foreach (['Chọn xe & hình thức', 'Thông tin liên hệ', 'Xác nhận'] as $i => $label)
                    <li class="booking__step {{ $i === 0 ? 'is-on' : '' }}" data-booking-step="{{ $i + 1 }}">
                        <span class="booking__step-num">{{ $i + 1 }}</span>
                        <span>
                            <b>{{ $label }}</b>
                            <em data-booking-step-sub>{{ ['Đặt cọc hoặc lái thử', 'Họ tên, điện thoại', 'Tư vấn viên gọi lại'][$i] }}</em>
                        </span>
                    </li>
                @endforeach
            </ol>

            @if (filled($hotline) || filled($zalo))
                <div class="booking__help">
                    <b>Cần hỗ trợ ngay?</b>
                    <p>
                        @if (filled($hotline))
                            Gọi <a href="tel:{{ preg_replace('/\s+/', '', $hotline) }}">{{ $hotline }}</a>@if (filled($hours)) ({{ $hours }})@endif<br>
                        @endif
                        @if (filled($zalo))
                            hoặc nhắn Zalo đại lý{{ str_starts_with($zalo, 'http') ? '' : ' '.$zalo }}.
                        @endif
                    </p>
                </div>
            @endif
        </aside>

        {{-- ── Cột phải: wizard ───────────────────────────────────────── --}}
        <div class="booking__main">
            @if ($forms->count() > 1)
                <div class="booking__modes" data-booking-modes>
                    @foreach ($forms as $form)
                        <a class="chip {{ $form->key === $activeKey ? 'chip--on' : '' }}"
                           href="{{ route('booking', ['hinh-thuc' => $form->key]) }}"
                           data-booking-mode="{{ $form->key }}">{{ $form->name }}</a>
                    @endforeach
                </div>
            @endif

            @foreach ($forms as $form)
                <div class="booking__pane" data-booking-pane="{{ $form->key }}"
                     @if ($form->key !== $activeKey) hidden @endif>
                    @include('frontend.partials.booking-form', [
                        'form'     => $form,
                        'products' => $products,
                        'selected' => $selected,
                    ])
                </div>
            @endforeach
        </div>
    </div>
@endsection
