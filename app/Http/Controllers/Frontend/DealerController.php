<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Support\Catalog;
use Illuminate\Contracts\View\View;

/**
 * Hệ thống đại lý — /he-thong-dai-ly (tiền tố lấy từ config).
 *
 * Nhóm theo tỉnh vì khách tìm showroom gần nhà chứ không tìm theo tên. Tỉnh
 * chưa có đại lý nào thì không liệt kê, khỏi để tên tỉnh trơ ra không có gì.
 */
class DealerController extends Controller
{
    public function __invoke(): View
    {
        return view('frontend.dealers', [
            'provinces' => Catalog::query('province')
                ->has('dealers')
                ->with('dealers')
                ->orderBy('name')
                ->get(),
        ]);
    }
}
