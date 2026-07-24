{{--
    Render mảng `sections` đã bỏ field trống (renderableSections()).
    Dùng chung cho sản phẩm, bài viết, trang tĩnh — cùng một cơ chế mục.

    Tiêu đề/đoạn mở đầu trống thì không render (quy tắc mục 3 tài liệu).
    Thân mục tách theo `type` — mỗi kiểu một partial trong partials/section/.
    Kiểu lạ (dự án tự thêm vào config) mà chưa có partial thì bỏ qua, không nổ.
--}}
@foreach ($sections as $section)
    @php $type = $section['type'] ?? 'media'; @endphp

    <section class="section" @isset($section['title']) id="{{ Str::slug($section['title']) }}" @endisset>
        <div class="wrap">
            @if (isset($section['title']) || isset($section['intro']))
                <div class="section__head">
                    @isset($section['title'])
                        <h2>{{ $section['title'] }}</h2>
                    @endisset
                    @isset($section['intro'])
                        <p>{{ $section['intro'] }}</p>
                    @endisset
                </div>
            @endif

            @includeIf('frontend.partials.section.'.$type, ['section' => $section])
        </div>
    </section>
@endforeach
