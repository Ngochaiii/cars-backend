<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Support\Catalog;
use Illuminate\Contracts\View\View;

/**
 * Danh sách toàn bộ mặt hàng — /san-pham (tiền tố lấy từ config).
 */
class ProductIndexController extends Controller
{
    public function __invoke(): View
    {
        return view('frontend.products', [
            'heading'    => catalog_label('product.plural'),
            'intro'      => null,
            'categories' => Catalog::query('category')->orderBy('sort')->get(),
            'products'   => Catalog::query('product')
                ->published()
                ->with('category')
                ->orderBy('sort')
                ->paginate((int) config('catalog.frontend.per_page', 12)),
        ]);
    }
}
