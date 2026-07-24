<?php

namespace App\Filament\Schemas;

use App\Support\Money;
use Filament\Forms\Components\TextInput;

/**
 * Ô nhập tiền. Giá xe là số 10 chữ số (5990000000) — gõ thiếu hay thừa một
 * số 0 thì mắt không bắt được, mà lỗi đó lộ ra ngoài trang bán hàng.
 *
 * Không dùng mask JS: bản Filament này không bundle plugin mask của Alpine,
 * nên `$money($input)` sẽ im lặng không chạy. Thay vào đó in lại con số đã
 * chấm phẩy ngay dưới ô để đối chiếu — "5.990.000.000 đ · 5,99 tỷ đ".
 */
class MoneyInput
{
    public static function make(string $name, string $label): TextInput
    {
        return TextInput::make($name)
            ->label($label)
            ->numeric()
            ->minValue(0)
            ->suffix('đ')
            ->live(onBlur: true)
            ->helperText(fn (mixed $state): ?string => static::hint($state));
    }

    protected static function hint(mixed $state): ?string
    {
        if (blank($state) || ! is_numeric($state)) {
            return null;
        }

        return Money::format($state).'  ·  '.Money::readable($state);
    }
}
