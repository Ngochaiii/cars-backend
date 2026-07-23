<?php

namespace App\Filament\Concerns;

trait HasCatalogNavigation
{
    /**
     * Filament mặc định chạy Str::ucwords() lên nhãn điều hướng
     * (Resource/Concerns/HasLabels.php), ra "Dòng Xe", "Bài Viết",
     * "Chuyển Hướng". Tiếng Việt không viết hoa giữa câu như vậy, mà nhãn
     * lại do người dùng đặt trong config nên phải giữ nguyên từng chữ.
     */
    public static function getNavigationLabel(): string
    {
        return static::getPluralModelLabel();
    }
}
