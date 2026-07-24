{{--
    Layout chung của trang khách xem.

    Biến nhận vào (đều tuỳ chọn):
      $title · $description · $canonical · $ogImage · $jsonld · $bodyClass

    CSS là file tĩnh public/css/frontend.css — không vite, không build.
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

    {{-- CSS demo, cố ý tối thiểu. Giao diện thật dán đè vào file này. --}}
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

    @include('frontend.partials.footer')
</body>
</html>
