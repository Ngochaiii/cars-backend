{{--
    Trả góp — form GET thường, tính bằng PHP, không cần JS (giống bộ so sánh
    chi phí nhiên liệu). Bấm Tính là tải lại đúng trang này kèm query string.

    Dư nợ giảm dần: gốc chia đều, lãi tính trên dư nợ còn lại, nên tiền trả
    tháng đầu cao nhất rồi giảm dần.

    Biến: $product · $loan (từ ProductController@loan)
--}}
<div class="loan">
    <form class="loan__panel" method="GET" action="#tra-gop">
        <div class="field">
            <label for="loan-down">Số tiền trả trước</label>
            <input type="text" inputmode="numeric" id="loan-down" name="down"
                   value="{{ number_format($loan['down'], 0, ',', '.') }}">
            <p class="field__hint">Giá xe {{ catalog_money($loan['price']) }}</p>
        </div>

        <div class="field">
            <label for="loan-months">Thời hạn vay</label>
            <select id="loan-months" name="months">
                @foreach ($loan['month_options'] as $m)
                    <option value="{{ $m }}" @selected($loan['months'] === $m)>{{ $m }} tháng</option>
                @endforeach
            </select>
        </div>

        <div class="field">
            <label for="loan-rate">Lãi suất (%/năm)</label>
            <input type="text" inputmode="decimal" id="loan-rate" name="rate"
                   value="{{ rtrim(rtrim(number_format($loan['rate'], 2, ',', ''), '0'), ',') }}">
        </div>

        <button class="btn btn--accent" type="submit">Tính trả góp</button>
    </form>

    <div class="loan__result">
        <div class="loan__lead">
            <span>Trả tháng đầu</span>
            <b>{{ catalog_money($loan['first_payment']) }}</b>
            <span class="loan__fall">giảm dần về {{ catalog_money($loan['last_payment']) }} ở tháng cuối</span>
        </div>

        <dl class="loan__rows">
            <div><dt>Số tiền vay</dt><dd>{{ catalog_money($loan['principal']) }}</dd></div>
            <div><dt>Gốc mỗi tháng</dt><dd>{{ catalog_money($loan['monthly_principal']) }}</dd></div>
            <div><dt>Tổng lãi phải trả</dt><dd>{{ catalog_money($loan['total_interest']) }}</dd></div>
            <div><dt>Tổng gốc và lãi</dt><dd>{{ catalog_money($loan['total_paid']) }}</dd></div>
        </dl>

        <p class="loan__note">
            (*) Tính theo dư nợ giảm dần, chỉ gồm khoản vay và lãi. Số liệu tham khảo,
            không phải cam kết cho vay — lãi suất và điều kiện duyệt hồ sơ do ngân hàng quyết định.
        </p>
    </div>
</div>
