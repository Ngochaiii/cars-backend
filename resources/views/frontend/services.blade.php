{{--
    Trang "Trạm sạc & dịch vụ" — bản đồ bên trái, danh sách trạm bên phải,
    dưới là dải thẻ dịch vụ tại showroom.

    Cả hai danh sách là khoá Cài đặt dạng bảng nhỏ (xem catalog_rows):
      stations  "Tên|Trạng thái|Thông tin|ok|lat,lng"  — cột 4 là ok | warn,
                cột 5 tuỳ chọn (toạ độ hoặc địa chỉ chữ) chỉ dùng cho công
                cụ tìm trạm ở trang chủ, trang này không in ra
      services  "Tên|Mô tả|Nhãn nút|Link"

    Khoá trống thì cả khối biến mất, không để khung rỗng.

    Biến: $stations · $services
--}}
@php
    $note  = catalog_setting('service_note', 'Trạm sạc & dịch vụ');
    $title = catalog_setting('service_title');
    $map   = catalog_image(catalog_setting('service_map'));

    $moreLabel = catalog_setting('stations_more');
    $moreUrl   = catalog_setting('stations_more_url');
@endphp

@extends('frontend.layout', [
    'title'     => $title ?: $note,
    'canonical' => route('services'),
])

@section('content')
    <section class="block" style="padding-block:56px 64px">
        <div class="wrap">
            <span class="eyebrow">{{ $note }}</span>
            <h1 style="max-width:640px">{{ $title ?: $note }}</h1>

            @if ($map || $stations->isNotEmpty())
                <div class="stations" style="margin-top:36px">
                    <div class="stations__map">
                        @if ($map)
                            <x-img :src="$map" alt="Bản đồ trạm sạc" sizes="(max-width: 960px) 100vw, 400px" />
                        @else
                            <div class="ph" style="height:100%">[ bản đồ trạm sạc ]</div>
                        @endif
                    </div>

                    @if ($stations->isNotEmpty())
                        <div class="stations__list">
                            @foreach ($stations as [$name, $status, $info, $tone])
                                <div class="station">
                                    <div class="station__head">
                                        <b>{{ $name }}</b>
                                        @if (filled($status))
                                            <span class="station__status {{ $tone === 'warn' ? 'is-warn' : '' }}">{{ $status }}</span>
                                        @endif
                                    </div>
                                    @if (filled($info))
                                        <div class="station__info">{{ $info }}</div>
                                    @endif
                                </div>
                            @endforeach

                            {{-- Nhãn không kèm link thì thành nút chết — ẩn luôn. --}}
                            @if (filled($moreLabel) && filled($moreUrl))
                                <a class="btn btn--sm btn--outline btn--block" style="margin-top:6px"
                                   href="{{ $moreUrl }}" rel="noopener" target="_blank">{{ $moreLabel }}</a>
                            @endif
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </section>

    @if ($services->isNotEmpty())
        <section class="block block--soft" style="padding-block:64px">
            <div class="wrap">
                <div class="section__head" style="margin-bottom:32px">
                    <h2>{{ catalog_setting('services_title', 'Dịch vụ tại showroom') }}</h2>
                </div>

                <div class="service-grid">
                    @foreach ($services as [$name, $text, $cta, $url])
                        <article class="service">
                            <h3>{{ $name }}</h3>
                            @if (filled($text))
                                <p>{{ $text }}</p>
                            @endif
                            @if (filled($cta))
                                <a class="link-underline"
                                   href="{{ $url ?: (Route::has('booking') ? route('booking') : route('products.index')) }}">{{ $cta }} →</a>
                            @endif
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
