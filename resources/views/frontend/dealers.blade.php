{{--
    Hệ thống đại lý, nhóm theo tỉnh.

    Giờ mở cửa là json tự do (["T2–T7: 8:00–19:00", "CN: 8:00–17:00"]) nên view
    chỉ việc liệt kê từng dòng, không đoán cấu trúc.

    Biến: $provinces (đã eager-load dealers)
--}}
@extends('frontend.layout', [
    'title'     => 'Hệ thống đại lý — '.catalog_setting('site_name', config('app.name')),
    'canonical' => route('dealers'),
])

@section('content')
    <div class="wrap">
        <ol class="breadcrumb">
            <li><a href="{{ route('home') }}">Trang chủ</a></li>
            <li>Hệ thống đại lý</li>
        </ol>
    </div>

    <section class="block" style="padding-top:32px">
        <div class="wrap">
            <h1>Hệ thống đại lý</h1>

            @if ($provinces->isEmpty())
                <p class="empty">Chưa có đại lý nào được đăng.</p>
            @else
                @foreach ($provinces as $province)
                    <div class="dealer-group">
                        <h2 class="dealer-group__name">{{ $province->name }}</h2>

                        <div class="dealer-grid">
                            @foreach ($province->dealers as $dealer)
                                <article class="dealer">
                                    <h3>{{ $dealer->name }}</h3>

                                    @if (filled($dealer->address))
                                        <p class="dealer__address">{{ $dealer->address }}</p>
                                    @endif

                                    @if (filled($dealer->opening_hours))
                                        <ul class="dealer__hours">
                                            @foreach ((array) $dealer->opening_hours as $line)
                                                <li>{{ is_array($line) ? implode(' ', $line) : $line }}</li>
                                            @endforeach
                                        </ul>
                                    @endif

                                    <div class="dealer__actions">
                                        @if (filled($dealer->phone))
                                            <a class="btn btn--sm" href="tel:{{ preg_replace('/\s+/', '', $dealer->phone) }}">{{ $dealer->phone }}</a>
                                        @endif

                                        {{-- Nút chỉ đường cần đủ cả hai toạ độ, thiếu một
                                             cái là link mở ra giữa biển. --}}
                                        @if ($dealer->lat && $dealer->lng)
                                            <a class="btn btn--sm btn--outline" rel="noopener" target="_blank"
                                               href="https://www.google.com/maps/search/?api=1&query={{ $dealer->lat }},{{ $dealer->lng }}">Chỉ đường</a>
                                        @endif
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </section>
@endsection
