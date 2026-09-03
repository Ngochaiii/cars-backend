{{--
    Hero của trang chi tiết. `hero` là {type, src, poster}:
      - type = image → ảnh nền, chữ trắng đè lên, có lớp phủ tối cho dễ đọc
      - type = video → nhúng video (YouTube/Vimeo/mp4) làm nền
      - không có hero → khối chữ trên nền xám nhạt, không để trang trống hốc

    Ảnh hero gộp với các ảnh banner khai trong `hero.banners` thành một băng
    chuyền — không cần thêm cột `gallery` vào bảng products. Chỉ một ảnh thì
    không dựng băng chuyền, khỏi thừa nút.
--}}
@php
    $hero    = $product->hero ?? [];
    $src     = $hero['src'] ?? null;
    $isVideo = ($hero['type'] ?? 'image') === 'video';
    $media   = $isVideo ? \App\Support\Media::embed($src) : catalog_image($src);
    $mobileMedia = $isVideo ? null : catalog_image($hero['mobile_src'] ?? null);

    $variant  = $product->variants->firstWhere('is_default', true) ?? $product->variants->first();
    $priceNow = $product->price_from ?: $variant?->price;
    $priceWas = $variant?->price_original;
    $heroHotline = catalog_setting('hotline');

    // Mỗi câu chữ đầu trang có ĐÚNG MỘT nguồn. Trước đây câu này cũng lùi về
    // mô tả SEO giống khối mở đầu, nên xe nào chưa điền là in y hệt một câu
    // hai lần cách nhau vài dòng.
    $lede = $hero['lede'] ?? null;

    // Băng chuyền chỉ dựng cho hero ẢNH — hero video thì để video chạy yên.
    // Khai banner rồi thì hero chạy ĐÚNG những ảnh banner đó, ảnh hero không
    // xuất hiện nữa (nó vẫn còn việc riêng: ảnh chia sẻ mạng xã hội và thumbnail
    // ở thanh đặt cọc). Chưa khai banner nào thì lùi về đúng ảnh hero, đứng yên
    // một tấm, không băng chuyền và không nút.
    //
    // Trước đây chỗ này tự quét ảnh của mọi mục kiểu `media` trong trang, nên
    // banner toàn ảnh cận cảnh đèn, mâm xe — thứ không bao giờ hợp làm ảnh phủ
    // trọn màn hình.
    $shots = collect();

    if (! $isVideo) {
        foreach ((array) ($hero['banners'] ?? []) as $banner) {
            if ($img = catalog_image($banner['image'] ?? null)) {
                $shots->push(['src' => $img, 'label' => $banner['label'] ?? $product->name]);
            }
        }

        $shots = $shots->unique('src')->values();
    }

    $hasBanners = $shots->isNotEmpty();

    // Khai banner là mặc nhiên "chỉ hiện ảnh": ảnh banner luôn có bố cục và chữ
    // riêng, phủ gradient tối rồi đè tiêu đề của site lên là làm đục thiết kế
    // của nó. Khối chữ không mất — nó tụt xuống ngay dưới banner.
    //
    // Công tắc `bare` dành cho trường hợp không khai banner mà chính ẢNH HERO
    // đã là một tấm thiết kế sẵn.
    $bareHero = ! $isVideo && ($hasBanners || ($hero['bare'] ?? false));

    if ($hasBanners) {
        // Có banner thì banner là nền của hero — kể cả khi xe chưa có ảnh hero.
        // Bản mobile riêng là của ảnh hero, không được ghép nhầm dưới banner.
        $media = $shots->first()['src'];
        $mobileMedia = null;
    } elseif ($media && ! $isVideo) {
        $shots->push(['src' => $media, 'label' => $product->name]);
    }
@endphp

<section class="hero hero--product {{ $media ? ($bareHero ? 'hero--bare' : 'hero--overlay') : 'hero--plain' }}" id="tong-quan"
         data-story-hero @if ($shots->count() > 1) data-gallery @endif>
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
                    @if ($i === 0 && $mobileMedia)
                        <picture class="hero__shot is-on" data-gal-slide
                                 data-gal-label="{{ $shot['label'] }}" aria-hidden="false">
                            <source media="(max-width: 680px)" srcset="{{ $mobileMedia }}">
                            <x-img :src="$shot['src']" :alt="$shot['label']" sizes="100vw" eager />
                        </picture>
                    @else
                        <x-img class="hero__shot {{ $i === 0 ? 'is-on' : '' }}"
                               data-gal-slide data-gal-label="{{ $shot['label'] }}"
                               :src="$shot['src']" :alt="$shot['label']"
                               aria-hidden="{{ $i === 0 ? 'false' : 'true' }}"
                               sizes="100vw" :eager="$i === 0" />
                    @endif
                @endforeach
            @else
                @php $only = $shots->first(); @endphp
                <picture>
                    @if ($mobileMedia)
                        <source media="(max-width: 680px)" srcset="{{ $mobileMedia }}">
                    @endif
                    <x-img :src="$only['src']" :alt="$only['label']" sizes="100vw" eager />
                </picture>
            @endif
        </div>
    @endif

    @unless ($bareHero)
        <div class="hero__body">
            <div class="wrap">
                @include('frontend.partials.hero-body')
            </div>
        </div>
    @endunless

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

    @if ($media)
        <div class="hero__scroll-cue" aria-hidden="true">
            <span>Cuộn để khám phá</span>
            <i></i>
        </div>
    @endif
</section>

@if ($bareHero)
    {{-- Bảng điều khiển nối trực tiếp với chân banner: ảnh quảng bá vẫn nguyên
         tính chất, còn tên xe, giá và các CTA nằm trong cùng một nhịp nhìn. --}}
    <div class="hero-caption">
        <div class="wrap hero-caption__wrap">
            <div class="hero-caption__panel">
                @include('frontend.partials.hero-body')
            </div>
        </div>
    </div>
@endif
