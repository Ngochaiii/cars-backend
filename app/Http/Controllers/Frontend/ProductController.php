<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Support\Catalog;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;

class ProductController extends Controller
{
    public function __invoke(Product $product): View
    {
        abort_unless($product->status === 'published', 404);

        $product->load(['category', 'variants', 'options']);

        $sections = $product->renderableSections();

        return view('frontend.product', [
            'product'  => $product,
            'sections' => $sections,
            'form'     => $this->form($sections),
        ]);
    }

    /**
     * Form đặt ở cuối trang chi tiết. Khoá khai trong config; form không tồn
     * tại hoặc đã tắt thì trả null và trang chỉ thiếu đúng khối đó.
     *
     * @param  array<int, array<string, mixed>>  $sections
     */
    protected function form(array $sections): ?Model
    {
        $key = config('catalog.frontend.product_form');

        if (blank($key) || ! Catalog::feature('forms')) {
            return null;
        }

        // Người nhập đã tự chèn form này vào giữa trang bằng mục kiểu `form`
        // thì không dựng lại lần nữa ở cuối — hai form giống hệt nhau, id
        // trong DOM trùng, và khách không biết bấm cái nào.
        $embedded = collect($sections)
            ->filter(fn (array $section) => ($section['type'] ?? null) === 'form')
            ->pluck('form_key')
            ->contains($key);

        if ($embedded) {
            return null;
        }

        return Catalog::query('form')
            ->with('fields')
            ->where('key', $key)
            ->where('is_active', true)
            ->first();
    }
}
