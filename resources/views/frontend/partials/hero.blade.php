{{--
    Hero của trang chi tiết. `hero` là {type, src, poster}:
      - type = image → ảnh nền, chữ trắng đè lên, có lớp phủ tối cho dễ đọc
      - type = video → nhúng video (YouTube/Vimeo/mp4) làm nền
      - không có hero → khối chữ trên nền xám nhạt, không để trang trống hốc

    Ảnh hero gộp với ảnh của các mục kiểu `media` thành một băng chuyền —
    không cần thêm cột `gallery` vào bảng products. Chỉ một ảnh thì không
    dựng băng chuyền, khỏi thừa nút.
--}}
@php
    $hero    = $product->hero ?? [];
    $src     = $hero['src'] ?? null;
    $isVideo = ($hero['type'] ?? 'image') === 'video';
    $media   = $isVideo ? \App\Support\Media::embed($src) : catalog_image($src);

    $variant  = $product->variants->firstWhere('is_default', true) ?? $product->variants->first();
    $priceNow = $product->price_from ?: $variant?->price;
    $priceWas = $variant?->price_original;

    // Mỗi câu chữ đầu trang có ĐÚNG MỘT nguồn. Trước đây câu này cũng lùi về
    // mô tả SEO giống khối mở đầu, nên xe nào chưa điền là in y hệt một câu
    // hai lần cách nhau vài dòng.
    $lede = $hero['lede'] ?? null;

    // Băng chuyền chỉ dựng cho hero ẢNH — hero video thì để video chạy yên.
    $shots = collect();

    if ($media && ! $isVideo) {
        $shots->push(['src' => $media, 'label' => $product->name]);

        foreach ($product->renderableSections() as $sec) {
            if (($sec['type'] ?? 'media') !== 'media') {
                continue;
            }
            foreach ($sec['items'] ?? [] as $item) {
                if ($img = catalog_image($item['image'] ?? null)) {
                    $shots->push(['src' => $img, 'label' => $item['label'] ?? ($sec['title'] ?? '')]);
                }
            }
        }

        $shots = $shots->unique('src')->take(5)->values();
    }
@endphp

<section class="hero {{ $media ? 'hero--overlay' : 'hero--plain' }}" @if ($shots->count() > 1) data-gallery @endif>
    @if ($media)
        <div class="hero__media">
            @if ($isVideo)
                @if (\App\Support\Media::isFile($media))
                    <video src="{{ $media }}" poster="{{ catalog_image($hero['poster'] ?? null) }}"
                           autoplay muted loop playsinline></video>
                @else
                    <iframe src="{{ $media }}" title="{{ $product->name }}" loading="lazy" allowfullscreen></iframe>
                @endif
            @elseif ($shots->count() > 1)
                @foreach ($shots as $i => $shot)
                    <img class="hero__shot {{ $i === 0 ? 'is-on' : '' }}"
                         data-gal-slide data-gal-label="{{ $shot['label'] }}"
                         src="{{ $shot['src'] }}" alt="{{ $shot['label'] }}"
                         aria-hidden="{{ $i === 0 ? 'false' : 'true' }}"
                         @if ($i === 0) fetchpriority="high" @else loading="lazy" @endif>
                @endforeach
            @else
                <img src="{{ $media }}" alt="{{ $product->name }}" fetchpriority="high">
            @endif
        </div>
    @endif

    <div class="hero__body">
        <div class="wrap">
            <div class="hero__inner">
                <span class="eyebrow">
                    {{ $product->name }}@if ($product->category) · {{ $product->category->name }}@endif
                </span>

                {{-- Bản thiết kế lấy CÂU TAGLINE làm tiêu đề lớn, tên xe đã
                     nằm ở dòng nhỏ phía trên rồi. Xe chưa đặt tagline thì lùi
                     về tên, khỏi để hero cụt đầu. --}}
                <h1>{{ $product->tagline ?: $product->name }}</h1>

                @if ($lede)
                    <p class="hero__lede">{{ $lede }}</p>
                @endif

                @if ($priceNow)
                    {{-- Giá rút gọn ("799 triệu") như thiết kế — hero để đọc
                         lướt, con số đầy đủ nằm ở phiên bản và form. --}}
                    <div class="hero__price">
                        <span class="hero__price-label">{{ catalog_label('product.single') }} từ</span>
                        <span class="hero__price-now">{{ catalog_money_short($priceNow) }}</span>
                        @if ($priceWas && $priceWas > $priceNow)
                            <span class="hero__price-was">{{ catalog_money_short($priceWas) }}</span>
                        @endif
                    </div>
                @endif

                {{-- Bản thiết kế có hai nút: đặt cọc (nhấn) và lái thử. Có
                     trang /dat-coc thì trỏ thẳng vào đó kèm xe đang xem; chưa
                     có thì lùi về neo tới form nằm cuối trang. --}}
                <div class="hero__actions">
                    @if (Route::has('booking'))
                        <a class="btn btn--accent" href="{{ route('booking', ['xe' => $product->slug]) }}">Đặt cọc</a>
                        <a class="btn btn--ghost"
                           href="{{ route('booking', ['xe' => $product->slug, 'hinh-thuc' => 'dat-lich-lai-thu']) }}">Đăng ký lái thử</a>
                    @else
                        @foreach ($heroForms ?? [] as $i => $hf)
                            <a class="btn {{ $i === 0 ? 'btn--accent' : 'btn--ghost' }}" href="#form-{{ $hf->key }}">{{ $hf->name }}</a>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if ($shots->count() > 1)
        <button type="button" class="arrow arrow--float arrow--float-prev" data-gal-prev aria-label="Ảnh trước">‹</button>
        <button type="button" class="arrow arrow--float arrow--float-next" data-gal-next aria-label="Ảnh sau">›</button>

        <div class="hero__gal">
            <div class="hero__gal-dots">
                @foreach ($shots as $i => $shot)
                    <button type="button" class="hero__gal-dot {{ $i === 0 ? 'is-on' : '' }}" data-gal-dot="{{ $i }}">
                        <span class="sr-only">{{ $shot['label'] }}</span>
                    </button>
                @endforeach
            </div>
            <span class="hero__gal-label" data-gal-label>{{ $shots->first()['label'] }}</span>
        </div>
    @endif
</section>
