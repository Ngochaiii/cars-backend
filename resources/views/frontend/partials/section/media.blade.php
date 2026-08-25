{{--
    Kiểu mặc định: ảnh kèm nhãn/mô tả tuỳ chọn, bố cục theo `layout`.

    Bốn bố cục dựng theo bản thiết kế cần markup riêng chứ không chỉ CSS:
      carousel — một ảnh lớn, mũi tên chuyển, có nhãn và bộ đếm
      tabs     — tab đánh số 01/02/03, mỗi tab một ảnh + tiêu đề + mô tả
      gallery  — 1 ảnh to bên trái, 2 ảnh nhỏ xếp chồng bên phải
      split    — đoạn mở đầu một bên, ảnh một bên

    Tắt JS: carousel hiện ảnh đầu rồi các ảnh sau xếp dọc, tabs hiện tất cả
    — vẫn đọc được hết, không có nút bấm chết.
--}}
@php
    $layout = $section['layout'] ?? 'cols-3';
    $items  = collect($section['items'] ?? [])->values();
@endphp

@if ($layout === 'carousel' && $items->count() > 1)
    <div class="gallery" data-gallery>
        <div class="gallery__stage">
            @foreach ($items as $i => $item)
                <figure class="gallery__slide {{ $i === 0 ? 'is-on' : '' }}"
                        data-gal-slide data-gal-label="{{ $item['label'] ?? '' }}"
                        aria-hidden="{{ $i === 0 ? 'false' : 'true' }}">
                    @if ($src = catalog_image($item['image'] ?? null))
                        <img src="{{ $src }}" alt="{{ $item['label'] ?? '' }}" loading="lazy">
                    @else
                        <div class="ph" style="height:100%">[ {{ $item['label'] ?? 'ảnh' }} ]</div>
                    @endif
                </figure>
            @endforeach
        </div>

        <div class="gallery__bar">
            <span class="gallery__label" data-gal-label>{{ $items->first()['label'] ?? '' }}</span>
            <div class="gallery__controls">
                <span class="gallery__count" data-gal-count>01 / {{ str_pad($items->count(), 2, '0', STR_PAD_LEFT) }}</span>
                <button type="button" class="arrow arrow--line" data-gal-prev aria-label="Ảnh trước">‹</button>
                <button type="button" class="arrow arrow--line" data-gal-next aria-label="Ảnh sau">›</button>
            </div>
        </div>
    </div>

@elseif ($layout === 'tabs' && $items->count() > 1)
    <div class="tabs" data-tabs>
        <div class="tabs__nav" role="tablist">
            @foreach ($items as $i => $item)
                <button type="button" class="tabs__tab {{ $i === 0 ? 'is-on' : '' }}"
                        data-tab="{{ $i }}" role="tab" aria-selected="{{ $i === 0 ? 'true' : 'false' }}">
                    <span class="tabs__num">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                    {{ $item['label'] ?? '' }}
                </button>
            @endforeach
        </div>

        @foreach ($items as $i => $item)
            <div class="tabs__panel {{ $i === 0 ? 'is-on' : '' }}" data-tab-panel role="tabpanel">
                <div class="tabs__media">
                    @if ($src = catalog_image($item['image'] ?? null))
                        <img src="{{ $src }}" alt="{{ $item['label'] ?? '' }}" loading="lazy">
                    @else
                        <div class="ph" style="height:100%">[ {{ $item['label'] ?? 'ảnh' }} ]</div>
                    @endif
                </div>
                <div class="tabs__body">
                    @isset($item['label'])<h3>{{ $item['label'] }}</h3>@endisset
                    @isset($item['desc'])<p>{{ $item['desc'] }}</p>@endisset
                </div>
            </div>
        @endforeach
    </div>

@elseif (in_array($layout, ['split', 'split-alt'], true))
    {{-- Chữ một cột, ảnh một cột. `split-alt` đảo bên: ảnh trước, chữ sau. --}}
    <div class="items layout-split {{ $layout === 'split-alt' ? 'split--media-first' : '' }}">
        <div class="split__body">
            @isset($section['title'])<h2>{{ $section['title'] }}</h2>@endisset
            @isset($section['intro'])<p>{{ $section['intro'] }}</p>@endisset
        </div>

        @php $item = $items->first() ?? []; @endphp
        <div class="item__media">
            @if ($src = catalog_image($item['image'] ?? null))
                <img src="{{ $src }}" alt="{{ $item['label'] ?? ($section['title'] ?? '') }}" loading="lazy">
            @else
                <div class="ph" style="height:100%">[ {{ $item['label'] ?? 'ảnh' }} ]</div>
            @endif
        </div>
    </div>

@else
    <div class="items layout-{{ $layout }}">
        @foreach ($items as $item)
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
@endif
