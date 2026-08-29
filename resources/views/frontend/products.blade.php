{{--
    Danh sách mặt hàng. Dùng cho cả /san-pham và /danh-muc/{slug} — chỉ khác
    tiêu đề và điều kiện lọc, nên không dựng hai view.

    Biến: $heading · $products (paginator) · $categories · $category? · $intro?
--}}
@extends('frontend.layout', [
    'title'       => data_get($seo ?? null, 'title', $heading),
    'description' => data_get($seo ?? null, 'description', $intro ?? null),
    'canonical'   => $canonical ?? null,
    'bodyClass'   => 'vehicle-index-page',
])

@section('content')
    <section class="vehicle-index-hero">
        <div class="wrap">
            <ol class="breadcrumb vehicle-index-hero__breadcrumb">
                <li><a href="{{ route('home') }}">Trang chủ</a></li>
                @isset($category)
                    <li><a href="{{ route('products.index') }}">{{ catalog_label('product.plural') }}</a></li>
                @endisset
                <li>{{ $heading }}</li>
            </ol>

            <div class="vehicle-index-hero__grid">
                <div class="vehicle-index-hero__copy">
                    <span class="eyebrow">Bộ sưu tập xe điện</span>
                    <h1>{{ $heading }}</h1>

                    <p class="lede">
                        {{ filled($intro ?? null)
                            ? $intro
                            : 'Khám phá thiết kế, quãng đường và công nghệ của từng mẫu xe để tìm lựa chọn phù hợp nhất.' }}
                    </p>
                </div>

                <div class="vehicle-index-hero__summary" aria-label="Tóm tắt danh mục">
                    <span class="vehicle-index-hero__count">{{ str_pad((string) $products->total(), 2, '0', STR_PAD_LEFT) }}</span>
                    <span>mẫu xe đang hiển thị</span>

                    @if (Route::has('compare'))
                        <a href="{{ route('compare') }}">Mở công cụ so sánh <b aria-hidden="true">↗</b></a>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <section class="block vehicle-index-catalog">
        <div class="wrap">
            @if ($categories->isNotEmpty())
                <div class="vehicle-index-toolbar">
                    <span class="vehicle-index-toolbar__label">Lọc theo phân khúc</span>
                    <ul class="chips">
                        <li>
                            <a class="chip {{ empty($category) ? 'chip--on' : '' }}" href="{{ route('products.index') }}">
                                Tất cả <span>{{ $products->total() }}</span>
                            </a>
                        </li>
                        @foreach ($categories as $item)
                            <li>
                                <a class="chip {{ (isset($category) && $category->is($item)) ? 'chip--on' : '' }}"
                                   href="{{ route('categories.show', $item->slug) }}">{{ $item->name }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if ($products->isEmpty())
                <p class="empty">Chưa có {{ Str::lower(catalog_label('product.plural')) }} nào ở đây.</p>
            @else
                <ul class="cards cards--2 vehicle-index-grid">
                    @each('frontend.partials.product-card', $products, 'product')
                </ul>

                <div class="pagination-wrap">{{ $products->links('frontend.partials.pagination') }}</div>
            @endif
        </div>
    </section>
@endsection
