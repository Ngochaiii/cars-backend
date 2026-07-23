@extends('frontend.layout', [
    'title'       => data_get($post->seo, 'title', $post->title),
    'description' => data_get($post->seo, 'description', $post->excerpt),
    'canonical'   => \App\Support\Url::absolute('post', $post->slug),
    'jsonld'      => \App\Support\JsonLd::forPost($post),
])

@section('content')
    <article>
        <h1>{{ $post->title }}</h1>
        @if ($post->excerpt)<p>{{ $post->excerpt }}</p>@endif
        @include('frontend.partials.sections', ['sections' => $sections])
    </article>
@endsection
