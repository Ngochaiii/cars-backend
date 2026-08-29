{{--
    Ảnh bảng thông số và tài liệu PDF.

    Đặt dưới lưới thông số, không thay thế nó — ba cách khai (lưới, ảnh, PDF)
    độc lập nhau, khai cái nào thì hiện cái đó.

    Ảnh bấm được để mở bản gốc: bảng thông số thường có chữ nhỏ, khách cần
    phóng to đọc chứ không chỉ nhìn lướt.
--}}
@php
    $specImages = collect($specImages ?? [])->filter()->values();
    $pdfUrl = catalog_image($specPdf ?? null);

    // Dung lượng file để khách biết trước khi bấm — nhất là trên 4G.
    $pdfSize = null;
    if ($pdfUrl && filled($specPdf) && ! Str::startsWith($specPdf, ['http://', 'https://'])) {
        $disk = Storage::disk('public');
        $pdfSize = $disk->exists($specPdf) ? $disk->size($specPdf) : null;
    }
@endphp

@if ($specImages->isNotEmpty() || $pdfUrl)
    <div class="spec-media">
        @foreach ($specImages as $img)
            @php $imgUrl = catalog_image($img); @endphp
            @if ($imgUrl)
                <a class="spec-media__sheet" href="{{ $imgUrl }}" target="_blank" rel="noopener"
                   aria-label="Mở ảnh thông số cỡ đầy đủ">
                    <x-img :src="$imgUrl" alt="Bảng thông số kỹ thuật" sizes="(max-width: 960px) 100vw, 1232px" />
                    <span class="spec-media__zoom" aria-hidden="true">Bấm để phóng to</span>
                </a>
            @endif
        @endforeach

        @if ($pdfUrl)
            <a class="spec-media__file" href="{{ $pdfUrl }}" target="_blank" rel="noopener" download>
                <span class="spec-media__icon" aria-hidden="true">PDF</span>
                <span class="spec-media__meta">
                    <b>{{ $specPdfLabel ?: 'Tải thông số kỹ thuật (PDF)' }}</b>
                    @if ($pdfSize)
                        {{-- Dưới 1 MB thì đổi sang KB: "0,0 MB" trông như file hỏng.
                             Ghép chuỗi trong PHP thay vì @if giữa thẻ — xuống dòng
                             trong Blade thành khoảng trắng thừa: "1  KB". --}}
                        <span>{{ $pdfSize >= 1048576
                            ? number_format($pdfSize / 1048576, 1, ',', '.').' MB'
                            : max(1, (int) round($pdfSize / 1024)).' KB' }}</span>
                    @endif
                </span>
            </a>
        @endif
    </div>
@endif
