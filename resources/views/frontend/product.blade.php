@extends('frontend.layout', [
    'title'       => data_get($product->seo, 'title', $product->name),
    'description' => data_get($product->seo, 'description', $product->tagline),
    'canonical'   => \App\Support\Url::absolute('product', $product->slug),
    'jsonld'      => \App\Support\JsonLd::forProduct($product),
])

@section('content')
    <article>
        <h1>{{ $product->name }}</h1>
        @if ($product->tagline)
            <p>{{ $product->tagline }}</p>
        @endif

        @if ($product->price_from)
            <p>{{ catalog_label('product.single') }} từ
                {{ number_format((float) $product->price_from, 0, ',', '.') }} đ</p>
        @endif

        @if (catalog_feature('highlights') && $product->highlights)
            <ul>
                @foreach ($product->highlights as $h)
                    <li><strong>{{ $h['value'] }} {{ $h['unit'] ?? '' }}</strong> — {{ $h['label'] }}</li>
                @endforeach
            </ul>
        @endif

        @includeWhen(catalog_feature('variants') && $product->variants->isNotEmpty(),
            'frontend.partials.variants', ['variants' => $product->variants])

        @include('frontend.partials.sections', ['sections' => $sections])

        @if (catalog_feature('specs') && $product->specs)
            @foreach ($product->specs as $group)
                <h3>{{ $group['group'] }}</h3>
                <table>
                    @foreach ($group['rows'] as $row)
                        <tr><th>{{ $row['label'] }}</th><td>{{ $row['value'] }}</td></tr>
                    @endforeach
                </table>
            @endforeach
        @endif
    </article>
@endsection
