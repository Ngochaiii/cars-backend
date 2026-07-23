@extends('frontend.layout')

@section('content')
    <h1>{{ catalog_label('product.plural') }}</h1>

    <ul>
        @foreach ($products as $product)
            <li>
                <a href="{{ route('products.show', $product->slug) }}">
                    {{ $product->name }}
                </a>
                @if ($product->tagline) — {{ $product->tagline }} @endif
            </li>
        @endforeach
    </ul>
@endsection
