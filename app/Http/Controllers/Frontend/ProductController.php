<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Support\Catalog;
use App\Support\FuelCalculator;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __invoke(Product $product, Request $request): View
    {
        abort_unless($product->status === 'published', 404);

        $product->load(['category', 'variants', 'options']);

        $sections = $product->renderableSections();

        return view('frontend.product', [
            'product'  => $product,
            'sections' => $sections,
            'forms'    => $this->forms($sections),
            'fuelCalc' => $this->fuelCalc($product, $request),
        ]);
    }

    /**
     * Form(s) đặt ở cuối trang chi tiết. Khoá khai trong config; form không
     * tồn tại, đã tắt, hoặc đã nhúng giữa trang rồi thì bỏ qua đúng cái đó.
     *
     * @param  array<int, array<string, mixed>>  $sections
     * @return Collection<int, \App\Models\Form>
     */
    protected function forms(array $sections): Collection
    {
        $keys = (array) config('catalog.frontend.product_forms', []);

        if (empty($keys) || ! Catalog::feature('forms')) {
            return new Collection();
        }

        // Người nhập đã tự chèn form này vào giữa trang bằng mục kiểu `form`
        // thì không dựng lại lần nữa ở cuối — hai form giống hệt nhau, id
        // trong DOM trùng, và khách không biết bấm cái nào.
        $embedded = collect($sections)
            ->filter(fn (array $section) => ($section['type'] ?? null) === 'form')
            ->pluck('form_key');

        $keys = array_diff($keys, $embedded->all());

        if (empty($keys)) {
            return new Collection();
        }

        return Catalog::query('form')
            ->with('fields')
            ->whereIn('key', $keys)
            ->where('is_active', true)
            ->get()
            ->sortBy(fn ($form) => array_search($form->key, $keys, true))
            ->values();
    }

    /**
     * So sánh chi phí nhiên liệu — đọc quãng đường/tháng, loại nhiên liệu và
     * mức tiêu thụ từ query string (form GET, tải lại trang là ra kết quả
     * mới, không cần JS). null khi tắt feature hoặc biến thể thiếu dữ liệu.
     *
     * @return array<string, mixed>|null
     */
    protected function fuelCalc(Product $product, Request $request): ?array
    {
        if (! Catalog::feature('fuel_calc')) {
            return null;
        }

        $variant = FuelCalculator::variantFor($product);

        if (! FuelCalculator::usable($variant)) {
            return null;
        }

        $fuel = $request->query('fuel') === 'dau' ? 'dau' : 'xang';
        $cons = (float) str_replace(',', '.', (string) $request->query('cons', '8'));
        $km = (float) str_replace(',', '.', (string) $request->query('km', '3000'));

        return array_merge(
            ['variant' => $variant, 'fuel' => $fuel, 'cons' => $cons, 'km' => $km],
            FuelCalculator::compare($variant, $fuel, $cons, $km),
        );
    }
}
