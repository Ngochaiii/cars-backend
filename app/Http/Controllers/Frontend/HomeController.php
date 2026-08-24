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
            'products' => Catalog::query('product')
                ->published()
                ->notInCategory(config('catalog.frontend.accessory_category'))
                ->with('category')
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
