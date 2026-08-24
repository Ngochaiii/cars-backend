<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Support\Catalog;
use Illuminate\Contracts\View\View;

/**
 * Trang "Phụ kiện xe" — /phu-kien (tiền tố lấy từ config).
 *
 * Phụ kiện vẫn là mặt hàng trong DB, chỉ khác là nằm ở danh mục khai tại
 * config('catalog.frontend.accessory_category') và hiện bằng thẻ nhỏ 4 cột
 * thay vì thẻ xe. Danh mục đó cũng bị loại khỏi trang chủ và danh sách xe.
 */
class AccessoryController extends Controller
{
    public function __invoke(): View
    {
        $category = Catalog::query('category')
            ->where('slug', config('catalog.frontend.accessory_category'))
            ->first();

        abort_unless($category, 404);

        return view('frontend.accessories', [
            'category' => $category,
            'products' => Catalog::query('product')
                ->published()
                ->where('category_id', $category->id)
                ->orderBy('sort')
                ->paginate((int) config('catalog.frontend.per_page', 12)),
        ]);
    }
}
