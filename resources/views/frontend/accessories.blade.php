{{--
    Trang "Phụ kiện xe" — lưới 4 cột thẻ nhỏ theo bản thiết kế.

    Phụ kiện vẫn là mặt hàng trong DB, chỉ khác danh mục (khai ở
    config('catalog.frontend.accessory_category')) nên vẫn có trang chi tiết,
    ảnh và giá như mọi mặt hàng khác.

    Biến: $category · $products (paginator)
--}}
@extends('frontend.layout', [
    'title'       => data_get($category->seo, 'title', $category->name),
    'description' => data_get($category->seo, 'description', $category->description),
    'canonical'   => route('accessories'),
])

@section('content')
    <div class="wrap">
        <ol class="breadcrumb">
            <li><a href="{{ route('home') }}">Trang chủ</a></li>
            <li>{{ $category->name }}</li>
        </ol>
    </div>

    <section class="block" style="padding-top:32px">
        <div class="wrap">
            <h1>{{ $category->name }}</h1>

            @if (filled($category->description))
                <p class="lede" style="margin-top:16px">{{ $category->description }}</p>
            @endif

            @if ($products->isEmpty())
                <p class="empty">Chưa có phụ kiện nào được đăng.</p>
            @else
                <ul class="cards cards--4" style="margin-top:36px">
                    @each('frontend.partials.accessory-card', $products, 'product')
                </ul>

                <div class="pagination-wrap">{{ $products->links('frontend.partials.pagination') }}</div>
            @endif
        </div>
    </section>
@endsection
