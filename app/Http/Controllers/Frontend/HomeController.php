<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Support\Catalog;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        return view('frontend.home', [
            // Banner hero. Chưa khai banner nào thì view lùi về dùng ảnh mặt
            // hàng — site mới dựng chưa kịp nhập banner vẫn có hero tử tế.
            'banners' => Catalog::feature('banners')
                ? Catalog::query('banner')->active()->orderBy('sort')->get()
                : collect(),

            'products' => Catalog::query('product')
                ->published()
                ->notInCategory(config('catalog.frontend.accessory_category'))
                ->with(['category', 'variants'])
                ->orderBy('sort')
                ->take((int) config('catalog.frontend.home.products', 8))
                ->get(),

            // Tin tức tắt được qua config('catalog.features.posts').
            'posts' => Catalog::feature('posts')
                ? Catalog::query('post')
                    ->published()
                    ->with('category')
                    ->latest('published_at')
                    ->take((int) config('catalog.frontend.home.posts', 3))
                    ->get()
                : collect(),
        ]);
    }
}
