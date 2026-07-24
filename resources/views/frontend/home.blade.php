@extends('frontend.layout', [
    'title'       => catalog_setting('site_name', config('app.name')),
    'description' => catalog_setting('site_description'),
    'canonical'   => rtrim(config('app.url'), '/').'/',
])

@section('content')
    <div class="hero hero--plain">
        <div class="hero__body">
            <div class="wrap">
                <span class="eyebrow">{{ catalog_setting('site_name', config('app.name')) }}</span>
                <h1>{{ catalog_label('product.plural') }}</h1>
                <p class="lede">
                    Toàn bộ {{ Str::lower(catalog_label('product.plural')) }} đang mở bán — giá và thông số
                    cập nhật theo từng {{ Str::lower(catalog_label('variant.single')) }}.
                </p>
                <p><a class="btn" href="{{ route('products.index') }}">Xem tất cả</a></p>
            </div>
        </div>
    </div>

    <section class="block">
        <div class="wrap">
            @if ($products->isEmpty())
                <p class="empty">Chưa có {{ Str::lower(catalog_label('product.plural')) }} nào được đăng.</p>
            @else
                <ul class="cards">
                    @each('frontend.partials.product-card', $products, 'product')
                </ul>
            @endif
        </div>
    </section>

    @if ($posts->isNotEmpty())
        <section class="block block--soft">
            <div class="wrap">
                <h2>Tin mới</h2>
                <ul class="cards">
                    @each('frontend.partials.post-card', $posts, 'post')
                </ul>
                <p class="pagination-wrap"><a href="{{ route('posts.index') }}">Xem tất cả tin tức →</a></p>
            </div>
        </section>
    @endif
@endsection
