{{--
    Render mảng `sections` đã bỏ field trống (renderableSections()).
    Dùng chung cho sản phẩm, bài viết, trang tĩnh — cùng một cơ chế mục.

    Tiêu đề/đoạn mở đầu trống thì không render (quy tắc mục 3 tài liệu).
    Thân mục tách theo `type` — mỗi kiểu một partial trong partials/section/.
    Kiểu lạ (dự án tự thêm vào config) mà chưa có partial thì bỏ qua, không nổ.

    $bare = true → bỏ khung <section class="section"><div class="wrap">, dùng
    khi mục đã nằm sẵn trong một cột có padding riêng (VD trang tĩnh 2 cột).

    $defaultWidth → bề rộng cho mục không tự khai `width`. Bài viết truyền
    'narrow' để mọi khối thẳng hàng với cột chữ; sản phẩm và trang tĩnh giữ
    'wide' như cũ. Người nhập chọn lại từng mục trong admin.
--}}
@php
    $bare = $bare ?? false;
    $defaultWidth = $defaultWidth ?? 'wide';
@endphp

@foreach ($sections as $section)
    @php
        $type = $section['type'] ?? 'media';

        // Bố cục "chia đôi" đặt tiêu đề + đoạn mở đầu vào ĐÚNG cột chữ, cạnh
        // ảnh — nên phần đầu do partial media tự dựng. Render nó ở đây nữa
        // thì chữ nằm trên, cột chữ bỏ trống, ảnh co lại còn 40% (đúng chỗ
        // trang chi tiết từng lệch khỏi bản thiết kế).
        // Mục "thông báo" đặt tên mục làm nhãn nhỏ BÊN TRONG hộp, nên phần
        // đầu do partial notice tự dựng — render ở đây nữa là có hai tiêu đề.
        $headInside = $type === 'notice'
            || ($type === 'media' && in_array($section['layout'] ?? '', ['split', 'split-alt'], true));

        $width = $section['width'] ?? $defaultWidth;

        // 'full' bỏ hẳn khung .wrap: mục chiếm trọn bề ngang màn hình.
        $wrapClass = match ($width) {
            'narrow' => 'wrap wrap--narrow',
            'full' => 'section__full',
            default => 'wrap',
        };
    @endphp

    @if ($bare)
        <div class="section-bare section-bare--{{ $width }}" @isset($section['title']) id="{{ Str::slug($section['title']) }}" @endisset>
            @if (! $headInside && (isset($section['title']) || isset($section['intro'])))
                <div class="section__head">
                    @isset($section['title'])<h2>{{ $section['title'] }}</h2>@endisset
                    @isset($section['intro'])<p>{{ $section['intro'] }}</p>@endisset
                </div>
            @endif

            @includeIf('frontend.partials.section.'.$type, ['section' => $section])
        </div>
    @else
        <section class="section story-section story-section--{{ $type }} section--{{ $width }}
                        @if ($type === 'media') story-section--{{ $section['layout'] ?? 'cols-3' }} @endif
                        {{ $loop->even ? 'story-section--alt' : '' }}"
                 data-story-section
                 @isset($section['title']) id="{{ Str::slug($section['title']) }}" @endisset>
            <div class="{{ $wrapClass }}">
                @if (! $headInside && (isset($section['title']) || isset($section['intro'])))
                    <div class="section__head">
                        @isset($section['title'])<h2>{{ $section['title'] }}</h2>@endisset
                        @isset($section['intro'])<p>{{ $section['intro'] }}</p>@endisset
                    </div>
                @endif

                @includeIf('frontend.partials.section.'.$type, ['section' => $section])
            </div>
        </section>
    @endif
@endforeach
