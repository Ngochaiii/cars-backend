{{-- Kiểu mặc định: ảnh kèm nhãn/mô tả tuỳ chọn, bố cục theo `layout`. --}}
<div class="items layout-{{ $section['layout'] ?? 'cols-3' }}">
    @foreach ($section['items'] ?? [] as $item)
        <figure class="item">
            <div class="item__media">
                @if ($src = catalog_image($item['image'] ?? null))
                    <img src="{{ $src }}" alt="{{ $item['label'] ?? '' }}" loading="lazy">
                @else
                    <div class="ph" style="height:100%">[ {{ $item['label'] ?? 'ảnh' }} ]</div>
                @endif
            </div>

            @isset($item['label'])
                <figcaption>{{ $item['label'] }}</figcaption>
            @endisset

            @isset($item['desc'])
                <p>{{ $item['desc'] }}</p>
            @endisset
        </figure>
    @endforeach
</div>
