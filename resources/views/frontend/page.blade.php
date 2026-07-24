@extends('frontend.layout', [
    'title'       => data_get($page->seo, 'title', $page->title),
    'description' => data_get($page->seo, 'description'),
    'canonical'   => \App\Support\Url::absolute('page', $page->slug),
])

@section('content')
    <article>
        <div class="wrap">
            <ol class="breadcrumb">
                <li><a href="{{ route('home') }}">Trang chủ</a></li>
                <li>{{ $page->title }}</li>
            </ol>
        </div>

        <header class="block">
            <div class="wrap wrap--narrow">
                <h1>{{ $page->title }}</h1>
            </div>
        </header>

        @include('frontend.partials.sections', ['sections' => $sections])
    </article>
@endsection
