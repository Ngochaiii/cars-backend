<?php

use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\PageController;
use App\Http\Controllers\Frontend\PostController;
use App\Http\Controllers\Frontend\ProductController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| SEO
|--------------------------------------------------------------------------
*/
if (config('catalog.seo.sitemap', true)) {
    Route::get('sitemap.xml', SitemapController::class)->name('sitemap');
}

/*
|--------------------------------------------------------------------------
| Frontend — trang khách xem, render bằng Blade
|--------------------------------------------------------------------------
| Tiền tố URL lấy từ config('catalog.routes') để khớp với sitemap và
| redirect. Đổi hình dạng URL chỉ sửa config.
*/
Route::get('/', HomeController::class)->name('home');

Route::get(trim(config('catalog.routes.product'), '/').'/{product:slug}', ProductController::class)
    ->name('products.show');

Route::get(trim(config('catalog.routes.post'), '/').'/{post:slug}', PostController::class)
    ->name('posts.show');

// Trang tĩnh nằm ở gốc (/gioi-thieu) nên đặt CUỐI cùng để không nuốt route khác.
Route::get('/{page:slug}', PageController::class)->name('pages.show');
