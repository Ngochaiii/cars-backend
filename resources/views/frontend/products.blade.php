{{--
    Danh sách mặt hàng. Dùng cho cả /san-pham và /danh-muc/{slug} — chỉ khác
    tiêu đề và điều kiện lọc, nên không dựng hai view.

    Biến: $heading · $products (paginator) · $categories · $category? · $intro?
--}}
@extends('frontend.layout', [
    'title'       => data_get($seo ?? null, 'title', $heading),
    'description' => data_get($seo ?? null, 'description', $intro ?? null),
    'canonical'   => $canonical ?? null,
])

@section('content')
    <div class="wrap">
        <ol class="breadcrumb">
            <li><a href="{{ route('home') }}">Trang chủ</a></li>
            @isset($category)
                <li><a href="{{ route('products.index') }}">{{ catalog_label('product.plural') }}</a></li>
            @endisset
            <li>{{ $heading }}</li>
        </ol>
    </div>

    <section class="block">
        <div class="wrap">
            <h1>{{ $heading }}</h1>

            @if (filled($intro ?? null))
                <p class="lede">{{ $intro }}</p>
            @endif

            @if ($categories->isNotEmpty())
                <ul class="chips">
                    <li>
                        <a class="chip {{ empty($category) ? 'chip--on' : '' }}" href="{{ route('products.index') }}">
                            Tất cả
                        </a>
                    </li>
                    @foreach ($categories as $item)
                        <li>
                            <a class="chip {{ (isset($category) && $category->is($item)) ? 'chip--on' : '' }}"
                               href="{{ route('categories.show', $item->slug) }}">{{ $item->name }}</a>
                        </li>
                    @endforeach
                </ul>
            @endif

            @if ($products->isEmpty())
                <p class="empty">Chưa có {{ Str::lower(catalog_label('product.plural')) }} nào ở đây.</p>
            @else
                <ul class="cards">
                    @each('frontend.partials.product-card', $products, 'product')
                </ul>

                <div class="pagination-wrap">{{ $products->links('frontend.partials.pagination') }}</div>
            @endif
        </div>
    </section>
@endsection
