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
    $fallbackImg = catalog_image(data_get($product, 'hero.src'));
    $firstFill = $firstImg
        ? "center/cover url('".$firstImg."')"
        : ($fallbackImg ? "center/cover url('".$fallbackImg."')" : ($first?->hex ?: '#1c1c1a'));
@endphp

<div class="config">
    <div class="config__render" data-swatch-panel role="img"
         aria-label="{{ $product->name ?? '' }} màu {{ $first?->name }}"
         data-product-name="{{ $product->name ?? '' }}"
         @if ($fallbackImg) data-swatch-fallback="{{ $fallbackImg }}" @endif
         style="background: {{ $firstFill }}; --swatch-accent: {{ $first?->hex ?: '#1464f4' }}">
        @unless ($firstImg)
            <span class="config__hint"><small>Màu tham khảo</small>{{ $product->name ?? '' }}</span>
        @endunless
    </div>

    <p class="config__label" data-swatch-label>{{ $first?->name }}</p>

    <ul class="swatches" data-swatches>
        @foreach ($options as $i => $option)
            @php $img = catalog_image($option->image); @endphp
            <li>
                <button type="button" class="swatch" data-swatch="{{ $option->hex }}"
                        data-swatch-name="{{ $option->name }}"
                        aria-pressed="{{ $i === 0 ? 'true' : 'false' }}"
                        @if ($img) data-swatch-image="{{ $img }}" @endif
                        title="{{ $option->name }}">
                    {{-- Ô tròn chỉ hiện MÀU, không nhét ảnh xe vào: ảnh thu nhỏ
                         xuống 28px thành một vệt xám không đọc được, mà nhiệm vụ
                         của ô này là cho biết màu. Ảnh vẫn dùng cho khối render
                         lớn phía trên qua data-swatch-image. Chỉ khi màu thiếu mã
                         hex mới đành lùi về ảnh, còn hơn ô trắng trơn. --}}
                    <span class="swatch__chip"
                          @if ($option->hex) style="background-color: {{ $option->hex }}"
                          @elseif ($img) style="background-image: url('{{ $img }}')"
                          @endif
                          aria-hidden="true"></span>
                    <span class="swatch__name">{{ $option->name }}</span>
                </button>
            </li>
        @endforeach
    </ul>
</div>
