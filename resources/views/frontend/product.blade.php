@extends('frontend.layout', [
    'title'       => data_get($product->seo, 'title', $product->name),
    'description' => data_get($product->seo, 'description', $product->tagline),
    'canonical'   => \App\Support\Url::absolute('product', $product->slug),
    'ogImage'     => catalog_image(data_get($product->hero, 'src')),
    'jsonld'      => \App\Support\JsonLd::forProduct($product),
])

@section('content')
    <article>
        @include('frontend.partials.hero', ['product' => $product])

        @if (catalog_feature('highlights') && filled($product->highlights))
            @include('frontend.partials.highlights', ['highlights' => $product->highlights])
        @endif

        <div class="wrap">
            <ol class="breadcrumb">
                <li><a href="{{ route('home') }}">Trang chủ</a></li>
                <li><a href="{{ route('products.index') }}">{{ catalog_label('product.plural') }}</a></li>
                @if ($product->category)
                    <li><a href="{{ route('categories.show', $product->category->slug) }}">{{ $product->category->name }}</a></li>
                @endif
                <li>{{ $product->name }}</li>
            </ol>
        </div>

        @if (catalog_feature('variants') && $product->variants->isNotEmpty())
            <section class="section">
                <div class="wrap">
                    <div class="section__head"><h2>{{ catalog_label('variant.plural') }}</h2></div>
                    @include('frontend.partials.variants', ['variants' => $product->variants])
                </div>
            </section>
        @endif

        @if (catalog_feature('options') && $product->options->isNotEmpty())
            <section class="section">
                <div class="wrap">
                    <div class="section__head"><h2>{{ catalog_label('option.plural') }}</h2></div>
                    @include('frontend.partials.options', ['options' => $product->options])
                </div>
            </section>
        @endif

        {{-- Phần biến thiên: các mục do người nhập tự đặt tên. --}}
        @include('frontend.partials.sections', ['sections' => $sections])

        @if (catalog_feature('specs') && filled($product->specs))
            <section class="section">
                <div class="wrap">
                    <div class="section__head"><h2>{{ catalog_label('specs') }}</h2></div>
                    @include('frontend.partials.specs', ['specs' => $product->specs])
                </div>
            </section>
        @endif

        @if (catalog_feature('fuel_calc') && $fuelCalc)
            <section class="section block--soft">
                <div class="wrap">
                    <div class="section__head">
                        <h2>So sánh chi phí nhiên liệu</h2>
                        <p>{{ $product->name }} so với xe động cơ đốt trong tương đương — số liệu tham khảo.</p>
                    </div>
                    @include('frontend.partials.fuel-calculator', ['product' => $product, 'fuelCalc' => $fuelCalc])
                </div>
            </section>
        @endif

        {{-- Form(s) cuối trang: khoá khai ở config('catalog.frontend.product_forms'). --}}
        @foreach ($forms as $f)
            <section class="section block--soft">
                <div class="wrap">
                    <div class="section__head">
                        <h2>{{ $f->name }}</h2>
                        <p>{{ $f->description ?: 'Để lại thông tin, tư vấn viên sẽ liên hệ lại.' }}</p>
                    </div>
                    @include('frontend.partials.lead-form', ['form' => $f, 'product' => $product])
                </div>
            </section>
        @endforeach
    </article>
@endsection
