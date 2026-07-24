<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Support\Catalog;
use Illuminate\Contracts\View\View;

/**
 * Danh mục mặt hàng — /danh-muc/{slug}. Dùng lại đúng view danh sách của
 * /san-pham, chỉ khác điều kiện lọc và tiêu đề.
 */
class CategoryController extends Controller
{
    public function __invoke(Category $category): View
    {
        return view('frontend.products', [
            'heading'    => $category->name,
            'intro'      => $category->description,
            'category'   => $category,
            'categories' => Catalog::query('category')->orderBy('sort')->get(),
            'seo'        => $category->seo,
            'canonical'  => \App\Support\Url::absolute('category', $category->slug),
            'products'   => $category->products()
                ->published()
                ->with('category')
                ->orderBy('sort')
                ->paginate((int) config('catalog.frontend.per_page', 12)),
        ]);
    }
}
