{{--
    Layout chung của trang khách xem.

    Biến nhận vào (đều tuỳ chọn):
      $title · $description · $canonical · $ogImage · $jsonld · $bodyClass

    CSS là file tĩnh public/css/frontend.css — không vite, không build.
    Font nạp bằng <link> chứ không @import trong CSS: @import bắt trình duyệt
    tải xong frontend.css, parse, rồi mới đi lấy font — chậm hơn hẳn.
--}}
@php
    $siteName        = catalog_setting('site_name', config('app.name'));
    $favicon         = catalog_image(catalog_setting('favicon'));
    $pageTitle       = $title ?? $siteName;
    $pageDescription = $description ?? null;
    $pageCanonical   = $canonical ?? request()->url();
    $pageImage       = $ogImage ?? null;
    $pageType        = $ogType ?? 'website';
    $frontendCssVersion = filemtime(public_path('css/frontend.css'));
@endphp
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $pageTitle }}</title>
    <meta name="theme-color" content="#ffffff">

    @if (filled($pageDescription))
        <meta name="description" content="{{ $pageDescription }}">
    @endif
    <link rel="canonical" href="{{ $pageCanonical }}">

    <meta property="og:locale" content="vi_VN">
    <meta property="og:type" content="{{ $pageType }}">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:url" content="{{ $pageCanonical }}">
    @if (filled($pageDescription))
        <meta property="og:description" content="{{ $pageDescription }}">
    @endif
    @if (filled($pageImage))
        <meta property="og:image" content="{{ $pageImage }}">
        <meta property="og:image:alt" content="{{ $pageTitle }}">
    @endif

    <meta name="twitter:card" content="{{ filled($pageImage) ? 'summary_large_image' : 'summary' }}">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    @if (filled($pageDescription))
        <meta name="twitter:description" content="{{ $pageDescription }}">
    @endif
    @if (filled($pageImage))
        <meta name="twitter:image" content="{{ $pageImage }}">
        <meta name="twitter:image:alt" content="{{ $pageTitle }}">
    @endif

    @if ($favicon)
        <link rel="icon" href="{{ $favicon }}">
    @endif

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@300;400;500;600;700;800&display=swap">

    <link rel="stylesheet" href="{{ asset('css/frontend.css') }}?v={{ $frontendCssVersion }}">

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

    @include('frontend.partials.popup')

    <script src="{{ asset('js/frontend.js') }}" defer></script>
</body>
</html>
