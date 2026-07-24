{{-- Kiểu mặc định: ảnh kèm nhãn/mô tả tuỳ chọn, bố cục theo `layout`. --}}
<div class="items layout-{{ $section['layout'] ?? 'cols-3' }}">
    @foreach ($section['items'] ?? [] as $item)
        <figure class="item">
            @if ($src = catalog_image($item['image'] ?? null))
                <img src="{{ $src }}" alt="{{ $item['label'] ?? '' }}" loading="lazy">
            @endif

            @isset($item['label'])
                <figcaption>{{ $item['label'] }}</figcaption>
            @endisset

            @isset($item['desc'])
                <p>{{ $item['desc'] }}</p>
            @endisset
        </figure>
    @endforeach
</div>
