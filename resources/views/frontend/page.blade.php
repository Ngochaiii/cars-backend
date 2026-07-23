@extends('frontend.layout', [
    'title'     => data_get($page->seo, 'title', $page->title),
    'canonical' => \App\Support\Url::absolute('page', $page->slug),
])

@section('content')
    <article>
        <h1>{{ $page->title }}</h1>
        @include('frontend.partials.sections', ['sections' => $sections])
    </article>
@endsection
