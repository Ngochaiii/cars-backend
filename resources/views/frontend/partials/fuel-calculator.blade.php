{{--
    So sánh chi phí nhiên liệu xe điện với xe xăng/dầu. Form GET thường —
    bấm "So sánh" là tải lại đúng trang này kèm query string, không cần JS.
    Biến: $product · $fuelCalc (từ ProductController@fuelCalc)
--}}
<div class="fuel-calc">
    <form method="GET" class="fuel-calc__form">
        <div class="field">
            <label>Loại nhiên liệu</label>
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
            <label for="fuel-calc-cons">Mức tiêu thụ nhiên liệu / 100 km</label>
            <input type="text" inputmode="decimal" id="fuel-calc-cons" name="cons"
                   value="{{ $fuelCalc['cons'] }}" placeholder="8">
        </div>

        <div class="field">
            <label for="fuel-calc-km">Quãng đường di chuyển / tháng (km)</label>
            <input type="text" inputmode="decimal" id="fuel-calc-km" name="km"
                   value="{{ $fuelCalc['km'] }}" placeholder="3000">
        </div>

        <div class="field field--full">
            <button class="btn" type="submit">So sánh</button>
        </div>
    </form>

    <div class="fuel-calc__result">
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
