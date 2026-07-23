<?php

namespace App\Support;

use Illuminate\Support\Collection;

/**
 * Quy tắc hiển thị (mục 3 tài liệu kiến trúc):
 *   - label trống  → không render nhãn
 *   - desc trống   → không render mô tả
 *   - intro trống  → không render đoạn mở đầu
 *
 * Nghĩa là mục "Thư viện" chỉ cần quăng ảnh vào, không phải điền gì thêm.
 */
class SectionCollection extends Collection
{
    /**
     * Bỏ hết field trống khỏi từng mục và từng item.
     * Frontend nhận về mảng chỉ chứa thứ thực sự có nội dung.
     */
    public function renderable(): array
    {
        return $this
            ->map(fn (array $section) => $this->clean($section))
            ->filter(fn (array $section) => filled($section['items'] ?? null) || filled($section['body'] ?? null))
            ->values()
            ->all();
    }

    protected function clean(array $section): array
    {
        $clean = array_filter([
            'title'  => $section['title'] ?? null,
            'intro'  => $section['intro'] ?? null,
            'type'   => $section['type'] ?? 'media',
            'layout' => $section['layout'] ?? 'cols-3',
            'body'   => $section['body'] ?? null,
        ], fn ($v) => filled($v));

        $items = collect($section['items'] ?? [])
            ->map(fn (array $item) => array_filter($item, fn ($v) => filled($v)))
            ->filter()
            ->values()
            ->all();

        if ($items) {
            $clean['items'] = $items;
        }

        return $clean;
    }
}
