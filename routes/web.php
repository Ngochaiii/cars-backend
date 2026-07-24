<?php

use App\Http\Controllers\Frontend\CategoryController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\LeadController;
use App\Http\Controllers\Frontend\PageController;
use App\Http\Controllers\Frontend\PostCategoryController;
use App\Http\Controllers\Frontend\PostController;
use App\Http\Controllers\Frontend\PostIndexController;
use App\Http\Controllers\Frontend\ProductController;
use App\Http\Controllers\Frontend\ProductIndexController;
use App\Http\Controllers\SitemapController;
use App\Support\Url;
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
|
| Route của loại nội dung nào cũng gắn với feature của nó: tắt `posts` trong
| config là /tin-tuc trả 404, đúng như /dealers bên API.
*/
Route::get('/', HomeController::class)->name('home');

$productPrefix = trim(Url::prefix('product'), '/');

Route::get($productPrefix, ProductIndexController::class)->name('products.index');
Route::get($productPrefix.'/{product:slug}', ProductController::class)->name('products.show');

Route::get(trim(Url::prefix('category'), '/').'/{category:slug}', CategoryController::class)
    ->name('categories.show');

if (catalog_feature('posts')) {
    $postPrefix = trim(Url::prefix('post'), '/');

    Route::get($postPrefix, PostIndexController::class)->name('posts.index');
    Route::get($postPrefix.'/{post:slug}', PostController::class)->name('posts.show');

    Route::get(trim(Url::prefix('post_category'), '/').'/{postCategory:slug}', PostCategoryController::class)
        ->name('post-categories.show');
}

/*
|--------------------------------------------------------------------------
| Lead — form Blade gửi POST thường
|--------------------------------------------------------------------------
| Không cần JS: bấm Gửi là request thật, xong thì redirect về đúng trang cũ
| kèm câu cảm ơn. Cùng action với API nên honeypot/chống trùng y hệt.
*/
if (catalog_feature('forms')) {
    Route::post('gui-form/{form:key}', LeadController::class)
        ->middleware('throttle:10,1')
        ->name('leads.store');
}

// Trang tĩnh nằm ở gốc (/gioi-thieu) nên đặt CUỐI cùng để không nuốt route khác.
if (catalog_feature('pages')) {
    Route::get('/{page:slug}', PageController::class)->name('pages.show');
}
