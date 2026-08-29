{{--
    So sánh chi phí nhiên liệu xe điện với xe xăng/dầu. Form GET thường —
    bấm "So sánh" là tải lại đúng trang này kèm query string, không cần JS.
    Biến: $product · $fuelCalc (từ ProductController@fuelCalc)
--}}
@php
    $heroImage = catalog_image(data_get($product->hero, 'src'));
@endphp

<div class="fuel-calc">
    <form class="fuel-calc__panel" method="GET" action="#fuel-calc">
        <p class="fuel-calc__intro">Vui lòng nhập thông tin xe động cơ đốt trong cần so sánh:</p>

        <div class="field">
            <span class="field__label">Loại nhiên liệu</span>
            <div class="segmented">
                <label class="segmented__option">
                    <input type="radio" name="fuel" value="xang" @checked($fuelCalc['fuel'] === 'xang')>
                    <span>Xăng</span>
                </label>
                <label class="segmented__option">
                    <input type="radio" name="fuel" value="dau" @checked($fuelCalc['fuel'] === 'dau')>
                    <span>Dầu</span>
                </label>
            </div>
        </div>

        <div class="field">
            <label for="fuel-calc-cons">Mức tiêu thụ nhiên liệu / 100 km *</label>
            <input type="text" inputmode="decimal" id="fuel-calc-cons" name="cons"
                   value="{{ $fuelCalc['cons'] }}" placeholder="8">
        </div>

        <div class="field">
            <label for="fuel-calc-km">Quãng đường di chuyển / tháng (km) *</label>
            <input type="text" inputmode="decimal" id="fuel-calc-km" name="km"
                   value="{{ $fuelCalc['km'] }}" placeholder="3000">
        </div>

        <button class="btn btn--accent btn--block" type="submit">So sánh</button>

        <p class="fuel-calc__note">
            (*) Nhập số, dùng dấu chấm "." cho phần thập phân. Ví dụ: 8.5 lít, 1023.5 km.
            Giá điện tạm tính {{ catalog_money(config('catalog.fuel_calc.electricity_price')) }}/kWh khi sạc tại nhà.
        </p>
    </form>

    <div class="fuel-calc__panel fuel-calc__result" id="fuel-calc">
        @if ($heroImage)
            <div class="fuel-calc__media">
                <x-img :src="$heroImage" :alt="$product->name" sizes="(max-width: 960px) 100vw, 50vw" />
            </div>
        @endif

        <div class="fuel-calc__title">Lợi thế chi phí nhiên liệu của {{ $product->name }}</div>

        <div class="fuel-calc__row">
            <span>Chi phí nhiên liệu xe xăng/dầu · tháng</span>
            <b>{{ catalog_money($fuelCalc['fuel_monthly']) }}</b>
        </div>
        <div class="fuel-calc__row">
            <span>Chi phí điện {{ $product->name }} · tháng</span>
            <b>{{ catalog_money($fuelCalc['ev_monthly']) }}</b>
        </div>
        <div class="fuel-calc__row fuel-calc__row--total">
            <span>Tiết kiệm mỗi năm</span>
            <b>{{ catalog_money($fuelCalc['save_yearly']) }}</b>
        </div>
    </div>
</div>
