<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        return view('frontend.home', [
            'products' => Product::published()->with('category')->orderBy('sort')->take(12)->get(),
        ]);
    }
}
