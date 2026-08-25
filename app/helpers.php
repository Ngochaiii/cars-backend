<?php

use App\Models\MenuItem;
use App\Support\Catalog;
use App\Support\Media;
use App\Support\Money;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

if (! function_exists('catalog_model')) {
    /**
     * Core không hardcode Product::class — model tra qua config để dự án
     * nào cần thêm hành vi thì extend rồi trỏ lại.
     *
     * @return class-string<Model>
     */
    function catalog_model(string $key): string
    {
        return Catalog::model($key);
    }
}

if (! function_exists('catalog_label')) {
    /** catalog_label('product.plural') → "Dòng xe" */
    function catalog_label(string $key, ?string $default = null): string
    {
        return Catalog::label($key, $default);
    }
}

if (! function_exists('catalog_feature')) {
    /** catalog_feature('dealers') → true|false */
    function catalog_feature(string $key): bool
    {
        return Catalog::feature($key);
    }
}

if (! function_exists('catalog_setting')) {
    /** catalog_setting('hotline') — giá trị màn hình Cài đặt, có cache. */
    function catalog_setting(string $key, mixed $default = null): mixed
    {
        return Catalog::model('setting')::get($key, $default);
    }
}

if (! function_exists('catalog_menu')) {
    /**
     * catalog_menu('header') → các mục gốc kèm con cháu, đã sắp thứ tự.
     * Menu không có thì trả collection rỗng — layout không cần @if.
     *
     * @return Collection<int, MenuItem>
     */
    function catalog_menu(string $key): Collection
    {
        return Catalog::menu($key);
    }
}

if (! function_exists('catalog_image')) {
    /** catalog_image('catalog/sections/a.webp') → URL đầy đủ. Link ngoài trả nguyên. */
    function catalog_image(mixed $path): ?string
    {
        return Media::url($path);
    }
}

if (! function_exists('catalog_money')) {
    /** catalog_money(5990000000) → "5.990.000.000 đ" */
    function catalog_money(mixed $amount): ?string
    {
        return Money::format($amount);
    }
}

if (! function_exists('catalog_rows')) {
    /**
     * Đọc một khoá Cài đặt dạng "bảng nhỏ" thành các dòng đã tách cột.
     *
     * Vài khối của thiết kế (chỉ số chăm sóc chủ xe, danh sách trạm sạc,
     * thẻ dịch vụ) là bảng 2–4 cột nhưng quá nhỏ để đẻ ra một bảng DB.
     * Người nhập gõ mỗi dòng một mục, cột ngăn bằng `|`:
     *
     *     10 năm|Bảo hành xe và pin
     *     24/7|Cứu hộ lưu động toàn tỉnh
     *
     * Xuống dòng hoặc `;` đều tính là hết một dòng — ô textarea và ô một
     * dòng trong admin dùng chung được một khoá.
     *
     * @return Collection<int, array<int, string>>
     */
    function catalog_rows(?string $value, int $columns = 2): Collection
    {
        return collect(preg_split('/[\r\n;]+/', (string) $value))
            ->map(fn (string $row) => array_pad(
                array_map('trim', explode('|', trim($row), $columns)),
                $columns,
                '',
            ))
            ->filter(fn (array $row) => filled($row[0]))
            ->values();
    }
}

if (! function_exists('catalog_money_short')) {
    /**
     * Giá rút gọn kiểu bản thiết kế: 799.000.000 → "799 triệu",
     * 1.090.000.000 → "1,09 tỷ".
     *
     * Dùng ở hero trang chi tiết và thẻ chọn xe — chỗ cần đọc lướt. Bảng giá
     * và form vẫn dùng catalog_money() đầy đủ, không rút gọn tiền người ta
     * sắp trả.
     */
    function catalog_money_short(mixed $amount): ?string
    {
        if (blank($amount) || ! is_numeric($amount)) {
            return null;
        }

        $amount = (float) $amount;

        [$value, $unit] = $amount >= 1_000_000_000
            ? [$amount / 1_000_000_000, 'tỷ']
            : [$amount / 1_000_000, 'triệu'];

        // Bỏ số 0 thừa sau dấu phẩy: 1,00 → 1 · 1,09 → 1,09 · 799,0 → 799
        $text = rtrim(rtrim(number_format($value, 2, ',', '.'), '0'), ',');

        return $text.' '.$unit;
    }
}
