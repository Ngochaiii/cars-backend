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

        {{-- Form cuối trang: khoá form khai ở config('catalog.frontend.product_form'). --}}
        @if (! empty($form))
            <section class="section block--soft">
                <div class="wrap">
                    <div class="section__head">
                        <h2>{{ $form->name }}</h2>
                        <p>Để lại thông tin, tư vấn viên sẽ liên hệ lại.</p>
                    </div>
                    @include('frontend.partials.lead-form', ['form' => $form, 'product' => $product])
                </div>
            </section>
        @endif
    </article>
@endsection
