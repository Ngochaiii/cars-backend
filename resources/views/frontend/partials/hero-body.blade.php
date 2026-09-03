{{--
    Khối nhận diện và thao tác chính của trang chi tiết xe.

    Nằm riêng vì có hai chỗ dùng: đè lên ảnh (mặc định), hoặc xuống dưới banner
    khi xe bật "chỉ hiện ảnh" — lúc đó ảnh không bị phủ tối, chữ vẫn còn đủ.
--}}
<div class="hero__inner {{ ($bareHero ?? false) ? 'hero__inner--summary' : '' }}">
    <div class="hero__identity">
        <span class="eyebrow">
            {{ $product->category?->name ?: 'Khám phá dòng xe' }}
        </span>

        {{-- Tên xe là thông tin nhận diện quan trọng nhất và cũng là h1 duy
             nhất của trang; tagline trở thành lời hứa sản phẩm ở ngay dưới. --}}
        <h1>{{ $product->name }}</h1>

        @if (filled($product->tagline) && $product->tagline !== $product->name)
            <p class="hero__tagline">{{ $product->tagline }}</p>
        @endif

        @if ($lede)
            <p class="hero__lede">{{ $lede }}</p>
        @endif
    </div>

    <div class="hero__commerce">
        @if ($priceNow)
            {{-- Con số rút gọn giúp đọc nhanh; giá đầy đủ vẫn nằm ở phần phiên bản. --}}
            <div class="hero__price">
                <span class="hero__price-label">Giá khởi điểm</span>
                <span class="hero__price-now">{{ catalog_money_short($priceNow) }}</span>
                @if ($priceWas && $priceWas > $priceNow)
                    <span class="hero__price-was">{{ catalog_money_short($priceWas) }}</span>
                @endif
            </div>
        @endif

        <div class="hero__cta-cluster">
            <div class="hero__actions">
                @if (Route::has('booking'))
                    <a class="btn btn--accent" href="{{ route('booking', ['xe' => $product->slug]) }}">
                        <span>{{ catalog_label('cta.deposit') }}</span>
                        <span class="hero__action-icon" aria-hidden="true">→</span>
                    </a>
                    <a class="btn btn--ghost"
                       href="{{ route('booking', ['xe' => $product->slug, 'hinh-thuc' => 'dat-lich-lai-thu']) }}">Đăng ký lái thử</a>
                @else
                    @foreach ($heroForms ?? [] as $i => $hf)
                        <a class="btn {{ $i === 0 ? 'btn--accent' : 'btn--ghost' }}" href="#form-{{ $hf->key }}">{{ $hf->name }}</a>
                    @endforeach
                @endif
            </div>

            @if (filled($heroHotline ?? null))
                <a class="hero__support" href="tel:{{ preg_replace('/\s+/', '', $heroHotline) }}"
                   aria-label="Gọi tư vấn {{ $heroHotline }}">
                    <span>Cần tư vấn nhanh?</span>
                    <strong>{{ $heroHotline }}</strong>
                </a>
            @endif
        </div>
    </div>
</div>
