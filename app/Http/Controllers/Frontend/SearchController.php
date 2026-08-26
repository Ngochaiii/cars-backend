<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Support\Catalog;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Tìm kiếm — /tim-kiem?q=...
 *
 * Cố ý làm mộc: LIKE trên vài cột chữ, gộp mặt hàng với bài viết. Chưa đủ dữ
 * liệu để đáng dựng full-text index hay gắn công cụ tìm kiếm ngoài; khi nào đo
 * thấy chậm thật thì đổi, đừng đoán trước.
 */
class SearchController extends Controller
{
    public function __invoke(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));

        return view('frontend.search', [
            'q' => $q,
            'products' => $q === '' ? new Collection : $this->products($q),
            'posts' => $q === '' ? new Collection : $this->posts($q),
        ]);
    }

    protected function products(string $q)
    {
        return Catalog::query('product')
            ->published()
            ->with('category')
            ->where(fn (Builder $b) => $b
                ->where('name', 'like', "%{$q}%")
                ->orWhere('tagline', 'like', "%{$q}%"))
            ->orderBy('sort')
            ->limit(24)
            ->get();
    }

    protected function posts(string $q)
    {
        if (! catalog_feature('posts')) {
            return new Collection;
        }

        return Catalog::query('post')
            ->published()
            ->with('category')
            ->where(fn (Builder $b) => $b
                ->where('title', 'like', "%{$q}%")
                ->orWhere('excerpt', 'like', "%{$q}%"))
            ->latest('published_at')
            ->limit(12)
            ->get();
    }
}
