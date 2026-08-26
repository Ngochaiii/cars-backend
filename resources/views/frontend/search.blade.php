{{--
    Kết quả tìm kiếm. Chưa gõ gì thì chỉ hiện ô nhập, KHÔNG báo "không tìm
    thấy" — vừa vào trang đã bị báo lỗi là vô lý.

    Biến: $q · $products · $posts
--}}
@extends('frontend.layout', [
    'title' => filled($q) ? 'Tìm "'.$q.'"' : 'Tìm kiếm',
])

@section('content')
    <div class="wrap">
        <ol class="breadcrumb">
            <li><a href="{{ route('home') }}">Trang chủ</a></li>
            <li>Tìm kiếm</li>
        </ol>
    </div>

    <section class="block" style="padding-top:32px">
        <div class="wrap">
            <h1>Tìm kiếm</h1>

            <form class="search-box" method="GET" action="{{ route('search') }}" role="search">
                <label class="sr-only" for="search-q">Từ khoá</label>
                <input id="search-q" type="search" name="q" value="{{ $q }}"
                       placeholder="Tên xe, phụ kiện hoặc tin tức…" autofocus>
                <button class="btn" type="submit">Tìm</button>
            </form>

            @if (filled($q))
                @php $total = $products->count() + $posts->count(); @endphp

                <p class="search-count">{{ $total }} kết quả cho “{{ $q }}”</p>

                @if ($total === 0)
                    <p class="empty">Không tìm thấy nội dung nào khớp. Thử từ khoá ngắn hơn.</p>
                @endif

                @if ($products->isNotEmpty())
                    <div class="section__head" style="margin-top:40px">
                        <h2>{{ catalog_label('product.plural') }}</h2>
                    </div>
                    <ul class="cards cards--3">
                        @each('frontend.partials.product-card', $products, 'product')
                    </ul>
                @endif

                @if ($posts->isNotEmpty())
                    <div class="section__head" style="margin-top:48px"><h2>Tin tức</h2></div>
                    <ul class="cards cards--3">
                        @each('frontend.partials.post-card', $posts, 'post')
                    </ul>
                @endif
            @endif
        </div>
    </section>
@endsection
