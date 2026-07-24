{{-- Phiên bản / kích cỡ / dung lượng — nhãn tuỳ mặt hàng, lấy từ config. --}}
<ul class="variants">
    @foreach ($variants as $v)
        <li class="variant {{ $v->is_default ? 'variant--default' : '' }}">
            <div class="variant__name">{{ $v->name }}</div>

            @if ($v->price)
                <div class="variant__price">
                    {{ catalog_money($v->price) }}
                    @if ($v->price_original && $v->price_original > $v->price)
                        <s>{{ catalog_money($v->price_original) }}</s>
                    @endif
                </div>
            @endif

            @if ($v->note)
                <p class="variant__note">{{ $v->note }}</p>
            @endif
        </li>
    @endforeach
</ul>
