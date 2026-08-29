@php
    $bookingUrl = $bookingUrl ?? (Route::has('booking')
        ? route('booking', ['xe' => $product->slug ?? null])
        : '#dang-ky-tu-van');
@endphp

<ul class="variants">
    @foreach ($variants as $v)
        @php
            // Phiên bản chưa có ảnh riêng thì mượn ảnh chính của xe — thẻ có
            // ảnh vẫn hơn thẻ trống, và hai phiên bản cùng ảnh không sai.
            $vImage = catalog_image($v->image) ?: catalog_image(data_get($product, 'hero.src'));
        @endphp
        <li class="variant {{ $v->is_default ? 'variant--default' : '' }}">
            @if ($v->is_default)
                <span class="variant__badge">Phổ biến</span>
            @endif
            <div class="variant__name">{{ $v->name }}</div>

            @if ($vImage)
                <div class="variant__media">
                    <x-img :src="$vImage" :alt="$v->name" sizes="(max-width: 680px) 100vw, 380px" />
                </div>
            @endif

            @if ($v->price)
                <div class="variant__price">
                    <span class="variant__price-label">Giá bán từ</span>
                    {{ catalog_money($v->price) }}
                    @if ($v->price_original && $v->price_original > $v->price)
                        <s>{{ catalog_money($v->price_original) }}</s>
                    @endif
                </div>
            @endif

            @if ($v->note)
                <p class="variant__note">{{ $v->note }}</p>
            @endif

            @if ($v->battery_kwh || $v->range_km)
                <dl class="variant__facts">
                    @if ($v->range_km)
                        <div><dt>Quãng đường</dt><dd>{{ rtrim(rtrim(number_format((float) $v->range_km, 1, ',', '.'), '0'), ',') }} km</dd></div>
                    @endif
                    @if ($v->battery_kwh)
                        <div><dt>Dung lượng pin</dt><dd>{{ rtrim(rtrim(number_format((float) $v->battery_kwh, 2, ',', '.'), '0'), ',') }} kWh</dd></div>
                    @endif
                </dl>
            @endif

            @php
                $datCoc = Route::has('booking')
                    ? route('booking', ['xe' => $product->slug ?? null, 'phien-ban' => $v->name])
                    : $bookingUrl;
                $laiThu = Route::has('booking')
                    ? route('booking', ['xe' => $product->slug ?? null, 'phien-ban' => $v->name, 'loai' => 'lai-thu'])
                    : $bookingUrl;
            @endphp

            {{-- Hai lối đi thay vì một: khách sẵn sàng thì đặt cọc, còn phân vân
                 thì đăng ký lái thử — trước đây chỉ có một nút cho cả hai. --}}
            <div class="variant__actions">
                <a class="btn btn--accent btn--block" href="{{ $datCoc }}">Đặt cọc</a>
                <a class="btn btn--outline btn--block" href="{{ $laiThu }}">Đăng ký lái thử</a>
            </div>
        </li>
    @endforeach
</ul>
