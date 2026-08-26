<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Support\Catalog;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * So sánh xe — /so-sanh?xe=slug1,slug2
 *
 * Chọn xe bằng query string chứ không lưu session: link gửi cho khách được, và
 * không cần JS để "thêm vào so sánh".
 *
 * Tối đa 3 xe — quá số đó là bảng tràn ngang trên điện thoại và chẳng ai đọc.
 */
class CompareController extends Controller
{
    /** Nhiều hơn con số này thì bảng không còn đọc được trên điện thoại. */
    protected const MAX = 3;

    public function __invoke(Request $request): View
    {
        // Nhận cả hai dạng: chuỗi ngăn phẩy từ link chia sẻ (?xe=a,b) và mảng
        // từ form chọn xe ở ngay trên trang (?xe[]=a&xe[]=b).
        $raw = $request->query('xe');
        $raw = is_array($raw) ? implode(',', $raw) : (string) $raw;

        $slugs = collect(explode(',', $raw))
            ->map(fn (string $s) => trim($s))
            ->filter()
            ->unique()
            ->take(self::MAX)
            ->values();

        $cars = $slugs->isEmpty()
            ? new Collection
            : Catalog::query('product')
                ->published()
                ->whereIn('slug', $slugs->all())
                ->with('category')
                ->get()
                // Giữ đúng thứ tự khách gõ trên URL, không theo thứ tự DB.
                ->sortBy(fn ($p) => $slugs->search($p->slug))
                ->values();

        return view('frontend.compare', [
            'cars' => $cars,
            'rows' => $this->rows($cars),
            'all' => Catalog::query('product')
                ->published()
                ->notInCategory(config('catalog.frontend.accessory_category'))
                ->orderBy('sort')
                ->get(['id', 'name', 'slug']),
        ]);
    }

    /**
     * Gộp nhãn thông số của mọi xe thành một danh sách dòng, giữ thứ tự xuất
     * hiện. Xe nào không khai nhãn đó thì ô để trống — KHÔNG được đẩy giá trị
     * của xe khác sang, đó là cách bảng so sánh nói dối.
     *
     * @return array<int, array{label: string, values: array<int, ?string>}>
     */
    protected function rows(Collection $cars): array
    {
        $labels = [];

        foreach ($cars as $car) {
            foreach ((array) $car->specs as $group) {
                foreach ($group['rows'] ?? [] as $row) {
                    $label = trim((string) ($row['label'] ?? ''));
                    if ($label !== '' && ! in_array($label, $labels, true)) {
                        $labels[] = $label;
                    }
                }
            }
        }

        return array_map(fn (string $label) => [
            'label' => $label,
            'values' => $cars->map(fn ($car) => $this->valueFor($car, $label))->all(),
        ], $labels);
    }

    protected function valueFor(object $car, string $label): ?string
    {
        foreach ((array) $car->specs as $group) {
            foreach ($group['rows'] ?? [] as $row) {
                if (trim((string) ($row['label'] ?? '')) === $label) {
                    return $row['value'] ?? null;
                }
            }
        }

        return null;
    }
}
