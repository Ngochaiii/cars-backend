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
            // KHÔNG dùng ->numeric(): input type=number coi dấu chấm là dấu
            // thập phân, nên "853.100.000" bị đọc thành 853,1 và lưu vào cột
            // decimal thành 853.10 — sai một tỷ lần mà không báo lỗi gì.
            ->inputMode('numeric')
            ->suffix('đ')
            ->live(onBlur: true)
            ->dehydrateStateUsing(fn (mixed $state) => static::toNumber($state))
            ->helperText(fn (mixed $state): ?string => static::hint($state));
    }

    /**
     * "853.100.000" · "853,100,000" · "853 100 000" · "853100000" → 853100000
     *
     * Giá xe tính bằng đồng, không có phần lẻ, nên mọi ký tự không phải chữ số
     * đều là dấu phân cách người nhập gõ cho dễ đọc.
     */
    public static function toNumber(mixed $state): ?string
    {
        if (blank($state)) {
            return null;
        }

        $digits = preg_replace('/\D+/', '', (string) $state);

        return $digits === '' ? null : $digits;
    }

    protected static function hint(mixed $state): ?string
    {
        $number = static::toNumber($state);

        if ($number === null) {
            return null;
        }

        return Money::format($number).'  ·  '.Money::readable($number);
    }
}
