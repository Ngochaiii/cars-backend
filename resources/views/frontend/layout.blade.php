{{--
    Layout chung của trang khách xem.

    Biến nhận vào (đều tuỳ chọn):
      $title · $description · $canonical · $ogImage · $jsonld · $bodyClass

    CSS là file tĩnh public/css/frontend.css — không vite, không build.
    Font nạp bằng <link> chứ không @import trong CSS: @import bắt trình duyệt
    tải xong frontend.css, parse, rồi mới đi lấy font — chậm hơn hẳn.
--}}
@php
    $siteName = catalog_setting('site_name', config('app.name'));
    $favicon  = catalog_image(catalog_setting('favicon'));
@endphp
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $title ?? $siteName }}</title>

    @isset($description)
        <meta name="description" content="{{ $description }}">
    @endisset
    @isset($canonical)
        <link rel="canonical" href="{{ $canonical }}">
    @endisset

    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:title" content="{{ $title ?? $siteName }}">
    @isset($description)
        <meta property="og:description" content="{{ $description }}">
    @endisset
    @if (! empty($ogImage))
        <meta property="og:image" content="{{ $ogImage }}">
    @endif

    @if ($favicon)
        <link rel="icon" href="{{ $favicon }}">
    @endif

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@300;400;500;600;700;800&display=swap">

    <link rel="stylesheet" href="{{ asset('css/frontend.css') }}">

    @isset($jsonld)
        <script type="application/ld+json">{!! json_encode($jsonld, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
    @endisset

    @include('frontend.partials.tracking')
</head>
<body class="{{ $bodyClass ?? '' }}">
    @include('frontend.partials.header')

    <main>
        @yield('content')
    </main>

    @include('frontend.partials.newsletter')
    @include('frontend.partials.footer')

    <script src="{{ asset('js/frontend.js') }}" defer></script>
</body>
</html>
