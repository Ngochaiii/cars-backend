<?php

namespace App\Support;

/**
 * Số điện thoại Việt Nam trong form lead.
 *
 * Khách gõ đủ kiểu: "098 765 4321", "098.765.4321", "+84 987 654 321".
 * Trước khi validate và lưu, đưa hết về một dạng "0xxxxxxxxx" để dedupe
 * theo số và tìm kiếm ở admin không bị lệch vì dấu cách.
 */
class Phone
{
    /** 0 + 9 chữ số: di động (03x/05x/07x/08x/09x) và số bàn (02xx). */
    public const PATTERN = '/^0\d{9}$/';

    /** Gỡ dấu cách/chấm/gạch/ngoặc; "+84…" và "84…" đổi về "0…". Không đụng chuỗi rỗng. */
    public static function normalize(?string $raw): ?string
    {
        if ($raw === null || trim($raw) === '') {
            return $raw;
        }

        $digits = preg_replace('/[\s.\-()]+/', '', trim($raw));

        if (str_starts_with($digits, '+84')) {
            $digits = '0'.substr($digits, 3);
        } elseif (str_starts_with($digits, '84') && strlen($digits) === 11) {
            $digits = '0'.substr($digits, 2);
        }

        return $digits;
    }

    public static function isValid(?string $normalized): bool
    {
        return is_string($normalized) && preg_match(self::PATTERN, $normalized) === 1;
    }
}
