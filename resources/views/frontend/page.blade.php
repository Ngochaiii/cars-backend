{{--
    Trang tĩnh (/gioi-thieu…). Cài đặt có địa chỉ/hotline/email thì dựng
    theo thiết kế "Về chúng tôi": nội dung bên trái, thẻ thông tin liên hệ
    bên phải. Thẻ đó lấy thẳng từ Cài đặt nên trang nào cũng dùng lại được.
--}}
@extends('frontend.layout', [
    'title'       => data_get($page->seo, 'title', $page->title),
    'description' => data_get($page->seo, 'description'),
    'canonical'   => \App\Support\Url::absolute('page', $page->slug),
])

@section('content')
    @php
        $address = catalog_setting('address');
        $hotline = catalog_setting('hotline');
        $email   = catalog_setting('email');
        $hours   = catalog_setting('opening_hours');
        $mapImg  = catalog_image(catalog_setting('map_image'));
        $mapUrl  = catalog_setting('map_url');
        $hasInfo = filled($address) || filled($hotline) || filled($email) || filled($hours);
    @endphp

    <article>
        <div class="wrap">
            <ol class="breadcrumb">
                <li><a href="{{ route('home') }}">Trang chủ</a></li>
                <li>{{ $page->title }}</li>
            </ol>
        </div>

        <section class="block" style="padding-top:32px">
            <div class="wrap">
                <h1 style="max-width:720px">{{ $page->title }}</h1>

                @if ($hasInfo)
                    <div class="page-split" style="margin-top:40px">
                        <div>
                            @include('frontend.partials.sections', ['sections' => $sections, 'bare' => true])
                        </div>

                        <aside class="info-card">
                            <h3>{{ catalog_setting('visit_title', 'Ghé thăm showroom') }}</h3>
                            <p>
                                @if (filled($address)){{ $address }}<br>@endif
                                @if (filled($hours)){{ $hours }}<br>@endif
                                @if (filled($hotline))Hotline: {{ $hotline }}<br>@endif
                                @if (filled($email)){{ $email }}@endif
                            </p>

                            @if ($mapImg)
                                <div class="info-card__map">
                                    <img src="{{ $mapImg }}" alt="Bản đồ chỉ đường" loading="lazy">
                                </div>
                            @endif

                            @if (filled($mapUrl))
                                <a class="btn btn--sm" href="{{ $mapUrl }}" rel="noopener" target="_blank">Chỉ đường trên Google Maps</a>
                            @endif
                        </aside>
                    </div>
                @endif
            </div>
        </section>

        @unless ($hasInfo)
            @include('frontend.partials.sections', ['sections' => $sections])
        @endunless
    </article>
@endsection
