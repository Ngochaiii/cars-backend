<?php

namespace Database\Seeders;

use Catalog\Core\Support\Catalog;
use Illuminate\Database\Seeder;

/**
 * Dữ liệu mẫu để soi thử admin + API. Xoá khi lên production.
 */
class CatalogDemoSeeder extends Seeder
{
    public function run(): void
    {
        $category = Catalog::query('category')->firstOrCreate(
            ['slug' => 'suv'],
            ['name' => 'SUV'],
        );

        $product = Catalog::query('product')->updateOrCreate(
            ['slug' => 'lexus-gx-550'],
            [
                'name'         => 'Lexus GX 550',
                'tagline'      => 'Bản lĩnh chinh phục',
                'category_id'  => $category->id,
                'price_from'   => 5_990_000_000,
                'status'       => 'published',
                'published_at' => now(),
                'hero'         => ['type' => 'image', 'src' => 'catalog/hero/gx550.webp'],
                'highlights'   => [
                    ['value' => '349', 'unit' => 'mã lực', 'label' => 'Công suất'],
                    ['value' => '3.4', 'unit' => 'L', 'label' => 'Dung tích'],
                ],
                'sections' => [
                    [
                        'title'  => 'Thư viện',
                        'intro'  => '',
                        'type'   => 'media',
                        'layout' => 'slider',
                        'items'  => [
                            ['image' => 'gallery-01.webp', 'label' => '', 'desc' => ''],
                            ['image' => 'gallery-02.webp', 'label' => '', 'desc' => ''],
                        ],
                    ],
                    [
                        'title'  => 'Mâm xe',
                        'intro'  => 'Mâm hợp kim nhôm đặc trưng cho từng phiên bản.',
                        'type'   => 'media',
                        'layout' => 'cols-2',
                        'items'  => [
                            [
                                'image' => 'wheel-01.webp',
                                'label' => 'Mâm hợp kim — Luxury',
                                'desc'  => 'Tối ưu cho cả đô thị và off-road.',
                            ],
                        ],
                    ],
                ],
                'specs' => [
                    [
                        'group' => 'Động Cơ & Hiệu Năng',
                        'rows'  => [
                            ['label' => 'Loại động cơ', 'value' => 'V35A-FTS, 6 xi lanh chữ V'],
                            ['label' => 'Dung tích', 'value' => '3,445 cm³'],
                        ],
                    ],
                ],
                'seo' => ['title' => 'Lexus GX 550 — giá và thông số'],
            ],
        );

        $product->variants()->delete();
        $product->variants()->createMany([
            ['name' => 'GX 550 Premium', 'price' => 5_990_000_000, 'sort' => 1, 'is_default' => true],
            ['name' => 'GX 550 Luxury', 'price' => 6_890_000_000, 'sort' => 2],
        ]);

        $product->options()->delete();
        $product->options()->createMany([
            ['name' => 'Caviar Black', 'hex' => '#111111', 'sort' => 1],
            ['name' => 'Zenith Grey', 'hex' => '#8A8D8F', 'sort' => 2],
        ]);

        $form = Catalog::query('form')->updateOrCreate(
            ['key' => 'dat-lich-lai-thu'],
            ['name' => 'Đặt lịch lái thử', 'success_message' => 'Cảm ơn bạn, tư vấn viên sẽ gọi lại trong 15 phút.'],
        );

        $form->fields()->delete();
        $form->fields()->createMany([
            ['key' => 'name', 'label' => 'Họ tên', 'type' => 'text', 'rules' => ['required'], 'sort' => 1, 'width' => 'half'],
            ['key' => 'phone', 'label' => 'Điện thoại', 'type' => 'tel', 'rules' => ['required'], 'sort' => 2, 'width' => 'half'],
            ['key' => 'email', 'label' => 'Email', 'type' => 'email', 'rules' => ['nullable'], 'sort' => 3],
        ]);
    }
}
