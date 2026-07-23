{{--
    Render mảng `sections` đã bỏ field trống (renderableSections()).
    Dùng chung cho sản phẩm, bài viết, trang tĩnh — cùng cơ chế mục.
--}}
@foreach ($sections as $section)
    <section>
        @isset($section['title'])
            <h2>{{ $section['title'] }}</h2>
        @endisset

        @isset($section['intro'])
            <p>{{ $section['intro'] }}</p>
        @endisset

        @if (($section['type'] ?? 'media') === 'text')
            <div>{!! nl2br(e($section['body'] ?? '')) !!}</div>
        @else
            <div class="items layout-{{ $section['layout'] ?? 'cols-3' }}">
                @foreach ($section['items'] ?? [] as $item)
                    <figure>
                        @isset($item['image'])
                            <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($item['image']) }}"
                                 alt="{{ $item['label'] ?? '' }}" loading="lazy">
                        @endisset
                        @isset($item['label'])
                            <figcaption>{{ $item['label'] }}</figcaption>
                        @endisset
                        @isset($item['desc'])
                            <p>{{ $item['desc'] }}</p>
                        @endisset
                    </figure>
                @endforeach
            </div>
        @endif
    </section>
@endforeach
