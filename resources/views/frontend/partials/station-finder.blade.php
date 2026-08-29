{{--
    Công cụ tìm trạm sạc — panel dùng chung.

    Đang gắn ở băng "Pin & trạm sạc" (trang chủ, nửa phải của stage); include
    lại được ở trang Trạm sạc & dịch vụ mà không phải sửa CSS.

    Luồng: khách nhập vị trí (hoặc bấm nút định vị) → danh sách trạm sắp theo
    khoảng cách → mỗi trạm một nút "Chỉ đường" mở Google Maps kèm điểm đi.

    ── Nguồn dữ liệu ───────────────────────────────────────────────────────
    Hiện đọc khoá Cài đặt `stations`, mỗi dòng 5 cột (cột 4, 5 tuỳ chọn):

        Tên | Trạng thái | Thông tin | ok|warn | Địa chỉ HOẶC "lat,lng"

    Có toạ độ ở cột 5 thì tính được khoảng cách tới khách và chỉ đường chính
    xác tới điểm; chỉ có địa chỉ dạng chữ thì Google Maps tự tra theo tên +
    địa chỉ (không có khoảng cách).

    ── Chỗ gắn API sau này ─────────────────────────────────────────────────
    Điền URL vào khoá Cài đặt `stations_api` (hoặc truyền biến $endpoint khi
    include) là xong, KHÔNG phải sửa JS/CSS. Khi có endpoint, frontend gọi:

        GET {endpoint}?q=<chữ khách gõ>&lat=<vĩ độ>&lng=<kinh độ>

    và chờ JSON dạng:

        { "data": [ { "name": "Trạm Vincom",
                      "status": "Còn 6/8 cổng",
                      "tone": "ok",              // ok | warn
                      "info": "DC 150 kW · Mở 24/7",
                      "address": "Đường Xương Giang, TP. Bắc Giang",
                      "lat": 21.2731, "lng": 106.1946,
                      "distance": 1.2 } ] }      // km, tuỳ chọn — thiếu thì
                                                 // tự tính từ lat/lng
    Mảng trần (không bọc "data") cũng nhận. Trường thiếu thì bỏ trống, không
    vỡ thẻ. Endpoint hỏng/timeout thì panel tự quay về lọc danh sách sẵn có
    chứ không để khung trắng.

    Danh sách dưới đây render sẵn từ server nên TẮT JS vẫn đọc được trạm và
    bấm chỉ đường; form khi đó submit sang trang Trạm sạc & dịch vụ.

    Biến (đều tuỳ chọn): $endpoint · $footLinks (collection label/url)
--}}
@php
    $finderRows = catalog_rows(catalog_setting('stations'), 5);
    $finderApi = trim((string) ($endpoint ?? catalog_setting('stations_api')));
    $finderAction = Route::has('services') ? route('services') : url()->current();
    $finderLinks = collect($footLinks ?? [])->filter(fn ($link) => filled(data_get($link, 'url')));

    $finderStations = $finderRows->map(function (array $row) {
        [$name, $status, $info, $tone, $where] = $row;

        // Cột 5 vừa nhận toạ độ vừa nhận địa chỉ chữ — phân biệt bằng dạng số.
        $isCoords = (bool) preg_match('/^-?\d{1,3}(\.\d+)?\s*,\s*-?\d{1,3}(\.\d+)?$/', $where);
        $coords = $isCoords ? array_map('trim', explode(',', $where, 2)) : null;

        return [
            'name' => $name,
            'status' => $status,
            'tone' => $tone === 'warn' ? 'warn' : 'ok',
            'info' => $info,
            'address' => $coords ? '' : $where,
            'lat' => $coords ? (float) $coords[0] : null,
            'lng' => $coords ? (float) $coords[1] : null,
        ];
    })->values();

    // Điểm đến ưu tiên toạ độ; không có thì để Google tra theo tên + địa chỉ.
    $finderDirections = function (array $station) {
        $dest = $station['lat'] !== null
            ? $station['lat'].','.$station['lng']
            : trim($station['name'].' '.$station['address']);

        return 'https://www.google.com/maps/dir/?api=1&destination='.rawurlencode($dest);
    };
@endphp

<form class="finder"
      data-finder
      data-home-reveal
      data-endpoint="{{ $finderApi }}"
      action="{{ $finderAction }}"
      method="get">
    <span class="finder__label">Tìm trạm sạc</span>
    <p class="finder__lead">Nhập vị trí của bạn để xem trạm sạc gần nhất và chỉ đường tới đó.</p>

    <div class="finder__field">
        <span class="finder__box">
            <label class="sr-only" for="finder-q">Vị trí của bạn</label>
            <input class="finder__input"
                   id="finder-q"
                   name="vi-tri"
                   type="search"
                   inputmode="search"
                   autocomplete="street-address"
                   placeholder="Số nhà, phường/xã, thành phố…"
                   value="{{ request('vi-tri') }}"
                   data-finder-input>
            <button class="finder__locate" type="button" data-finder-locate
                    title="Dùng vị trí hiện tại" aria-label="Dùng vị trí hiện tại">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor"
                     stroke-width="1.6" aria-hidden="true">
                    <circle cx="12" cy="12" r="7" />
                    <circle cx="12" cy="12" r="1.8" fill="currentColor" stroke="none" />
                    <path d="M12 1.5v3M12 19.5v3M1.5 12h3M19.5 12h3" stroke-linecap="round" />
                </svg>
            </button>
        </span>
        <button class="finder__submit" type="submit">Tìm</button>
    </div>

    <p class="finder__status" data-finder-status role="status" aria-live="polite">
        @if ($finderStations->isEmpty())
            Chưa có trạm nào được cập nhật.
        @else
            {{ $finderStations->count() }} trạm trong khu vực
        @endif
    </p>

    {{-- Cùng một mảng $finderStations dùng cho cả HTML render sẵn lẫn JSON
         cho JS, để hai bên không bao giờ lệch nhau. Vẫn escape < > & ' " —
         tên trạm là chữ admin gõ, có "</script>" trong đó là thoát ra khỏi
         thẻ; JSON_UNESCAPED_UNICODE chỉ để tiếng Việt không thành \uXXXX. --}}
    <script type="application/json" data-finder-seed>@json($finderStations, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT)</script>

    <ul class="finder__results" data-finder-results>
        @foreach ($finderStations as $station)
            <li class="finder__item">
                <b class="finder__name">{{ $station['name'] }}</b>
                @if (filled($station['status']))
                    <span class="finder__badge {{ $station['tone'] === 'warn' ? 'is-warn' : '' }}">{{ $station['status'] }}</span>
                @endif
                @if (filled($station['info']) || filled($station['address']))
                    <span class="finder__info">{{ $station['info'] ?: $station['address'] }}</span>
                @endif
                <span class="finder__row">
                    <em class="finder__dist" hidden></em>
                    <a class="finder__go" href="{{ $finderDirections($station) }}" target="_blank" rel="noopener">
                        Chỉ đường <span aria-hidden="true">↗</span>
                    </a>
                </span>
            </li>
        @endforeach
    </ul>

    {{-- Khung thẻ cho JS vẽ lại kết quả (lọc tại chỗ hoặc trả về từ API) —
         giữ markup ở một chỗ duy nhất thay vì nhân bản chuỗi HTML trong JS. --}}
    <template data-finder-template>
        <li class="finder__item">
            <b class="finder__name" data-f="name"></b>
            <span class="finder__badge" data-f="status"></span>
            <span class="finder__info" data-f="info"></span>
            <span class="finder__row">
                <em class="finder__dist" data-f="dist" hidden></em>
                <a class="finder__go" data-f="go" href="#" target="_blank" rel="noopener">
                    Chỉ đường <span aria-hidden="true">↗</span>
                </a>
            </span>
        </li>
    </template>

    @if ($finderLinks->isNotEmpty())
        <div class="finder__foot">
            @foreach ($finderLinks as $link)
                <a href="{{ data_get($link, 'url') }}">{{ data_get($link, 'label') }} ›</a>
            @endforeach
        </div>
    @endif
</form>
