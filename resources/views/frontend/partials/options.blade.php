{{--
    Bộ chọn màu theo bản thiết kế: khối render lớn ở giữa, tên màu bên dưới,
    dãy ô màu tròn để bấm. Có ảnh thì ô tròn và khối render dùng ảnh, không
    có thì dùng mã hex.

    Tắt JS: khối render giữ màu đầu tiên, các ô màu vẫn hiện đủ kèm tên —
    chỉ mất phần đổi màu khi bấm.
--}}
@php
    $first     = $options->first();
    $firstImg  = catalog_image($first?->image);
    $firstFill = $firstImg ? "center/cover url('".$firstImg."')" : ($first?->hex ?: '#1c1c1a');
@endphp

<div class="config">
    <div class="config__render" data-swatch-panel style="background: {{ $firstFill }}">
        @unless ($firstImg)
            <span class="config__hint">{{ $product->name ?? '' }}</span>
        @endunless
    </div>

    <p class="config__label" data-swatch-label>{{ $first?->name }}</p>

    <ul class="swatches" data-swatches>
        @foreach ($options as $option)
            @php $img = catalog_image($option->image); @endphp
            <li>
                <button type="button" class="swatch" data-swatch="{{ $option->hex }}"
                        data-swatch-name="{{ $option->name }}"
                        @if ($img) data-swatch-image="{{ $img }}" @endif
                        title="{{ $option->name }}">
                    <span class="swatch__chip"
                          @if ($img) style="background-image: url('{{ $img }}')"
                          @elseif ($option->hex) style="background-color: {{ $option->hex }}"
                          @endif
                          aria-hidden="true"></span>
                    <span class="swatch__name">{{ $option->name }}</span>
                </button>
            </li>
        @endforeach
    </ul>
</div>
