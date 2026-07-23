<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Contracts\View\View;

class ProductController extends Controller
{
    public function __invoke(Product $product): View
    {
        abort_unless($product->status === 'published', 404);

        $product->load(['category', 'variants', 'options']);

        return view('frontend.product', [
            'product'  => $product,
            'sections' => $product->renderableSections(),
        ]);
    }
}
