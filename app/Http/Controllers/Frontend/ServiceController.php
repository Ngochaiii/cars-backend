<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

/**
 * Trang "Trạm sạc & dịch vụ" — /tram-sac-dich-vu (tiền tố lấy từ config).
 *
 * Không có bảng trạm sạc riêng: danh sách trạm và thẻ dịch vụ đều là khoá
 * Cài đặt dạng bảng nhỏ (xem catalog_rows). Khoá trống thì khối tự ẩn.
 */
class ServiceController extends Controller
{
    public function __invoke(): View
    {
        return view('frontend.services', [
            'stations' => catalog_rows(catalog_setting('stations'), 5),
            'services' => catalog_rows(catalog_setting('services'), 4),
        ]);
    }
}
