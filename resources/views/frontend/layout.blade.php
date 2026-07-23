<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name') }}</title>
    @isset($description)
        <meta name="description" content="{{ $description }}">
    @endisset
    @isset($canonical)
        <link rel="canonical" href="{{ $canonical }}">
    @endisset
    @isset($jsonld)
        <script type="application/ld+json">{!! json_encode($jsonld, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
    @endisset
</head>
<body>
    <header>
        <a href="{{ route('home') }}">{{ \App\Models\Setting::get('site_name', config('app.name')) }}</a>
    </header>

    <main>
        @yield('content')
    </main>

    <footer>
        <p>{{ \App\Models\Setting::get('hotline') }}</p>
    </footer>
</body>
</html>
