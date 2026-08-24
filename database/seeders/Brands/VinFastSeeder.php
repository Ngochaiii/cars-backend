<?php

namespace Database\Seeders\Brands;

/**
 * VinFast — 6 mẫu xe điện đang bán tại Việt Nam.
 *
 * ⚠️ SỐ LIỆU LÀ THAM KHẢO, KHÔNG PHẢI BÁO GIÁ CHÍNH THỨC.
 *    Giá và thông số ghi theo thông tin công bố tại thời điểm viết seeder;
 *    hãng đổi giá và chính sách pin liên tục. Trước khi đưa lên trang thật
 *    phải đối chiếu lại với bảng giá chính hãng.
 *
 * Giá `price_from` và giá phiên bản là **giá thuê pin**. Mua kèm pin cao hơn,
 * ghi ở `note` của từng phiên bản.
 *
 * Chạy riêng:
 *   php artisan db:seed --class="Database\Seeders\Brands\VinFastSeeder"
 */
class VinFastSeeder extends BrandSeeder
{
    protected function brand(): string
    {
        return 'VinFast';
    }

    protected function categories(): array
    {
        return [
            'mini-suv'  => 'Mini SUV',
            'suv-co-a'  => 'SUV cỡ A',
            'suv-co-b'  => 'SUV cỡ B',
            'suv-co-c'  => 'SUV cỡ C',
            'suv-co-d'  => 'SUV cỡ D',
            'suv-co-e'  => 'SUV cỡ E',
        ];
    }

    protected function products(): array
    {
        return [
            $this->vf3(),
            $this->vf5(),
            $this->vf6(),
            $this->vf7(),
            $this->vf8(),
            $this->vf9(),
        ];
    }

    // ── Từng mẫu xe ──────────────────────────────────────────────────────

    protected function vf3(): array
    {
        return [
            'slug'       => 'vinfast-vf-3',
            'name'       => 'VinFast VF 3',
            'tagline'    => 'Mini SUV điện cho phố nhỏ',
            'category'   => 'mini-suv',
            'price_from' => 240_000_000,
            'hero'       => true,

            'highlights' => [
                ['value' => '215', 'unit' => 'km', 'label' => 'Quãng đường mỗi lần sạc (NEDC)'],
                ['value' => '43',  'unit' => 'mã lực', 'label' => 'Công suất tối đa'],
                ['value' => '4',   'unit' => 'chỗ', 'label' => 'Số chỗ ngồi'],
            ],

            'media' => [
                'Thư viện' => [
                    'layout' => 'carousel',
                    'items'  => ['Góc trước', 'Góc sau', 'Khoang lái'],
                ],
                'Ngoại thất' => [
                    'intro'  => 'Kiểu dáng vuông vức, bán kính quay vòng nhỏ, hợp ngõ hẹp.',
                    'layout' => 'cols-2',
                    'items'  => ['Đèn LED chữ V', 'Mâm 16 inch'],
                ],
            ],

            'story' => [
                'Vận hành' => "Động cơ điện 32 kW, mô-men xoắn tức thời 110 Nm.\n"
                    .'Chế độ lái Eco và Sport, hệ thống hỗ trợ đỗ xe.',
            ],

            'tables' => [
                'Thời gian sạc' => [
                    'Sạc nhanh DC (10–70%)' => 'khoảng 36 phút',
                    'Sạc AC 7,4 kW (0–100%)' => 'khoảng 4 giờ 30 phút',
                ],
            ],

            'colors' => [
                'Trắng'  => '#F2F2F2',
                'Đen'    => '#1A1A1A',
                'Xám'    => '#8A8D8F',
                'Vàng'   => '#E8B93B',
                'Xanh lá' => '#2F6E4E',
            ],

            'variants' => [
                [
                    'name'        => 'VF 3 tiêu chuẩn',
                    'price'       => 240_000_000,
                    'note'        => 'Giá thuê pin. Mua kèm pin khoảng 322 triệu.',
                    'battery_kwh' => 18.64,
                    'range_km'    => 215,
                ],
            ],

            'specs' => [
                'Động cơ & hiệu năng' => [
                    'Công suất tối đa'  => '32 kW (43 mã lực)',
                    'Mô-men xoắn'       => '110 Nm',
                    'Dẫn động'          => 'Cầu sau',
                    'Tốc độ tối đa'     => '100 km/h',
                ],
                'Pin & sạc' => [
                    'Dung lượng pin'    => '18,64 kWh',
                    'Quãng đường (NEDC)' => '215 km',
                    'Sạc nhanh DC'      => '10–70% trong khoảng 36 phút',
                ],
                'Kích thước' => [
                    'Dài × Rộng × Cao'  => '3.190 × 1.679 × 1.622 mm',
                    'Chiều dài cơ sở'   => '2.075 mm',
                    'Khoảng sáng gầm'   => '191 mm',
                    'Số chỗ ngồi'       => '4',
                ],
            ],

            'seo' => [
                'title'       => 'VinFast VF 3 — giá, thông số và quãng đường',
                'description' => 'Mini SUV điện 4 chỗ, quãng đường 215 km mỗi lần sạc.',
            ],
        ];
    }

    protected function vf5(): array
    {
        return [
            'slug'       => 'vinfast-vf-5-plus',
            'name'       => 'VinFast VF 5 Plus',
            'tagline'    => 'SUV điện cỡ A cho gia đình trẻ',
            'category'   => 'suv-co-a',
            'price_from' => 458_000_000,
            'hero'       => true,

            'highlights' => [
                ['value' => '326', 'unit' => 'km', 'label' => 'Quãng đường mỗi lần sạc (NEDC)'],
                ['value' => '134', 'unit' => 'mã lực', 'label' => 'Công suất tối đa'],
                ['value' => '5',   'unit' => 'chỗ', 'label' => 'Số chỗ ngồi'],
            ],

            'media' => [
                'Thư viện' => [
                    'layout' => 'carousel',
                    'items'  => ['Góc trước', 'Góc sau', 'Nội thất'],
                ],
                'Nội thất' => [
                    'intro'  => 'Màn hình giải trí 8 inch, điều hoà tự động.',
                    'layout' => 'cols-2',
                    'items'  => ['Khoang lái', 'Hàng ghế sau'],
                ],
            ],

            'story' => [
                'Vận hành' => "Động cơ điện 100 kW đặt cầu trước, tăng tốc mượt trong phố.\n"
                    .'Ba chế độ lái, phanh tái sinh năng lượng.',
            ],

            'tables' => [
                'Thời gian sạc' => [
                    'Sạc nhanh DC (10–70%)'  => 'khoảng 30 phút',
                    'Sạc AC 11 kW (0–100%)'  => 'khoảng 4 giờ',
                ],
            ],

            'colors' => [
                'Trắng' => '#F2F2F2',
                'Đen'   => '#1A1A1A',
                'Xám'   => '#8A8D8F',
                'Đỏ'    => '#A32020',
            ],

            'variants' => [
                [
                    'name'        => 'VF 5 Plus',
                    'price'       => 458_000_000,
                    'note'        => 'Giá thuê pin. Mua kèm pin khoảng 538 triệu.',
                    'battery_kwh' => 37.23,
                    'range_km'    => 326,
                ],
            ],

            'specs' => [
                'Động cơ & hiệu năng' => [
                    'Công suất tối đa' => '100 kW (134 mã lực)',
                    'Mô-men xoắn'      => '135 Nm',
                    'Dẫn động'         => 'Cầu trước',
                    'Tăng tốc 0–50 km/h' => '4,4 giây',
                ],
                'Pin & sạc' => [
                    'Dung lượng pin'     => '37,23 kWh',
                    'Quãng đường (NEDC)' => '326 km',
                    'Sạc nhanh DC'       => '10–70% trong khoảng 30 phút',
                ],
                'Kích thước' => [
                    'Dài × Rộng × Cao' => '3.967 × 1.723 × 1.579 mm',
                    'Chiều dài cơ sở'  => '2.514 mm',
                    'Khoang hành lý'   => '285 lít',
                    'Số chỗ ngồi'      => '5',
                ],
            ],

            'seo' => ['title' => 'VinFast VF 5 Plus — giá và thông số kỹ thuật'],
        ];
    }

    protected function vf6(): array
    {
        return [
            'slug'       => 'vinfast-vf-6',
            'name'       => 'VinFast VF 6',
            'tagline'    => 'SUV điện cỡ B, hai phiên bản Eco và Plus',
            'category'   => 'suv-co-b',
            'price_from' => 689_000_000,
            'hero'       => true,

            'highlights' => [
                ['value' => '480', 'unit' => 'km', 'label' => 'Quãng đường mỗi lần sạc (NEDC)'],
                ['value' => '201', 'unit' => 'mã lực', 'label' => 'Công suất bản Plus'],
                ['value' => '59,6', 'unit' => 'kWh', 'label' => 'Dung lượng pin'],
            ],

            'media' => [
                'Thư viện' => [
                    'layout' => 'carousel',
                    'items'  => ['Góc trước', 'Góc sau', 'Nội thất', 'Khoang hành lý'],
                ],
                'Ngoại thất' => [
                    'intro'  => 'Dải đèn LED liền mạch, tay nắm cửa dạng ẩn.',
                    'layout' => 'cols-2',
                    'items'  => ['Đèn trước', 'Mâm 19 inch'],
                ],
                'Nội thất' => [
                    'layout' => 'cols-2',
                    'items'  => ['Màn hình 12,9 inch', 'Ghế chỉnh điện'],
                ],
                'Trải nghiệm mỗi ngày' => [
                    'intro'  => 'Bốn nâng cấp thấy rõ trong lúc dùng xe hằng ngày.',
                    'layout' => 'tabs',
                    'items'  => [
                        'Kiến trúc điện – điện tử',
                        'Hỗ trợ lái ADAS',
                        'Trợ lý ảo tiếng Việt',
                        'Hệ thống treo thích ứng',
                    ],
                ],
            ],

            'story' => [
                'Trang bị an toàn' => "Gói ADAS: ga tự động thích ứng, giữ làn, cảnh báo điểm mù.\n"
                    .'6 túi khí, camera 360 độ.',
            ],

            'tables' => [
                'Thời gian sạc' => [
                    'Sạc nhanh DC (10–70%)' => 'khoảng 24 phút',
                    'Sạc AC 11 kW (0–100%)' => 'khoảng 6 giờ 30 phút',
                ],
            ],

            'colors' => [
                'Trắng' => '#F2F2F2',
                'Đen'   => '#1A1A1A',
                'Xám'   => '#8A8D8F',
                'Đỏ'    => '#A32020',
                'Xanh'  => '#2C4F7C',
            ],

            'variants' => [
                [
                    'name'        => 'VF 6 Eco',
                    'price'       => 689_000_000,
                    'note'        => 'Giá thuê pin. 174 mã lực, quãng đường 480 km (NEDC).',
                    'battery_kwh' => 59.6,
                    'range_km'    => 480,
                ],
                [
                    'name'        => 'VF 6 Plus',
                    'price'       => 749_000_000,
                    'note'        => 'Giá thuê pin. 201 mã lực, thêm gói ADAS và mâm 19 inch.',
                    'battery_kwh' => 59.6,
                    'range_km'    => 460,
                ],
            ],

            'specs' => [
                'Động cơ & hiệu năng' => [
                    'Công suất (Eco / Plus)' => '130 kW (174 mã lực) / 150 kW (201 mã lực)',
                    'Mô-men xoắn'            => '250 Nm',
                    'Dẫn động'               => 'Cầu trước',
                ],
                'Pin & sạc' => [
                    'Dung lượng pin'     => '59,6 kWh',
                    'Quãng đường (NEDC)' => '480 km (Eco) / 460 km (Plus)',
                    'Sạc nhanh DC'       => '10–70% trong khoảng 24 phút',
                ],
                'Kích thước' => [
                    'Dài × Rộng × Cao' => '4.238 × 1.820 × 1.594 mm',
                    'Chiều dài cơ sở'  => '2.730 mm',
                    'Khoang hành lý'   => '423 lít',
                    'Số chỗ ngồi'      => '5',
                ],
            ],

            'seo' => ['title' => 'VinFast VF 6 — giá Eco và Plus, thông số kỹ thuật'],
        ];
    }

    protected function vf7(): array
    {
        return [
            'slug'       => 'vinfast-vf-7',
            'name'       => 'VinFast VF 7',
            'tagline'    => 'SUV điện cỡ C, bản Plus dẫn động hai cầu',
            'category'   => 'suv-co-c',
            'price_from' => 799_000_000,
            'hero'       => true,

            'highlights' => [
                ['value' => '450', 'unit' => 'km', 'label' => 'Quãng đường mỗi lần sạc (NEDC)'],
                ['value' => '348', 'unit' => 'mã lực', 'label' => 'Công suất bản Plus'],
                ['value' => '5,8', 'unit' => 'giây', 'label' => 'Tăng tốc 0–100 km/h'],
            ],

            'media' => [
                'Thư viện' => [
                    'layout' => 'carousel',
                    'items'  => ['Góc trước', 'Góc bên', 'Góc sau', 'Nội thất'],
                ],
                'Ngoại thất' => [
                    'intro'  => 'Thiết kế thể thao, cửa sổ trời toàn cảnh trên bản Plus.',
                    'layout' => 'cols-2',
                    'items'  => ['Đèn trước', 'Mâm 20 inch'],
                ],
            ],

            'story' => [
                'Vận hành' => "Bản Plus dùng hai mô-tơ, dẫn động bốn bánh AWD.\n"
                    .'Bản Eco một mô-tơ cầu trước, ưu tiên quãng đường đi được.',
            ],

            'tables' => [
                'Thời gian sạc' => [
                    'Sạc nhanh DC (10–70%)' => 'khoảng 25 phút',
                    'Sạc AC 11 kW (0–100%)' => 'khoảng 8 giờ',
                ],
            ],

            'colors' => [
                'Trắng' => '#F2F2F2',
                'Đen'   => '#1A1A1A',
                'Xám'   => '#8A8D8F',
                'Đỏ'    => '#A32020',
                'Xanh'  => '#2C4F7C',
            ],

            'variants' => [
                [
                    'name'        => 'VF 7 Eco',
                    'price'       => 799_000_000,
                    'note'        => 'Giá thuê pin. 174 mã lực, dẫn động cầu trước.',
                    'battery_kwh' => 59.6,
                    'range_km'    => 450,
                ],
                [
                    'name'        => 'VF 7 Plus',
                    'price'       => 949_000_000,
                    'note'        => 'Giá thuê pin. 348 mã lực, dẫn động AWD, cửa sổ trời toàn cảnh.',
                    'battery_kwh' => 75.3,
                    'range_km'    => 431,
                ],
            ],

            'specs' => [
                'Động cơ & hiệu năng' => [
                    'Công suất (Eco / Plus)' => '130 kW (174 mã lực) / 260 kW (348 mã lực)',
                    'Mô-men xoắn (Plus)'     => '500 Nm',
                    'Dẫn động'               => 'Cầu trước (Eco) / Hai cầu AWD (Plus)',
                    'Tăng tốc 0–100 km/h'    => '5,8 giây (Plus)',
                ],
                'Pin & sạc' => [
                    'Dung lượng pin'     => '59,6 kWh (Eco) / 75,3 kWh (Plus)',
                    'Quãng đường (NEDC)' => '450 km (Eco) / 431 km (Plus)',
                    'Sạc nhanh DC'       => '10–70% trong khoảng 25 phút',
                ],
                'Kích thước' => [
                    'Dài × Rộng × Cao' => '4.545 × 1.890 × 1.635 mm',
                    'Chiều dài cơ sở'  => '2.840 mm',
                    'Số chỗ ngồi'      => '5',
                ],
            ],

            'seo' => ['title' => 'VinFast VF 7 — giá Eco và Plus, thông số kỹ thuật'],
        ];
    }

    protected function vf8(): array
    {
        return [
            'slug'       => 'vinfast-vf-8',
            'name'       => 'VinFast VF 8',
            'tagline'    => 'SUV điện cỡ D, hai cầu tiêu chuẩn',
            'category'   => 'suv-co-d',
            'price_from' => 1_109_000_000,
            'hero'       => true,

            'highlights' => [
                ['value' => '447', 'unit' => 'km', 'label' => 'Quãng đường mỗi lần sạc (NEDC)'],
                ['value' => '402', 'unit' => 'mã lực', 'label' => 'Công suất bản Plus'],
                ['value' => '82',  'unit' => 'kWh', 'label' => 'Dung lượng pin'],
            ],

            'media' => [
                'Thư viện' => [
                    'layout' => 'carousel',
                    'items'  => ['Góc trước', 'Góc bên', 'Góc sau', 'Nội thất', 'Khoang hành lý'],
                ],
                'Ngoại thất' => [
                    'intro'  => 'Dải đèn LED chạy ngang, gương chiếu hậu chỉnh điện gập tự động.',
                    'layout' => 'cols-2',
                    'items'  => ['Đèn trước', 'Mâm 21 inch'],
                ],
                'Nội thất' => [
                    'intro'  => 'Màn hình 15,6 inch, ghế da, cửa sổ trời toàn cảnh.',
                    'layout' => 'cols-2',
                    'items'  => ['Khoang lái', 'Hàng ghế sau'],
                ],
            ],

            'story' => [
                'Trang bị an toàn' => "11 túi khí, camera 360 độ, gói ADAS đầy đủ.\n"
                    .'Hỗ trợ giữ làn, cảnh báo va chạm, phanh khẩn cấp tự động.',
            ],

            'tables' => [
                'Thời gian sạc' => [
                    'Sạc nhanh DC (10–70%)' => 'khoảng 24 phút',
                    'Sạc AC 11 kW (0–100%)' => 'khoảng 9 giờ',
                ],
            ],

            'colors' => [
                'Trắng' => '#F2F2F2',
                'Đen'   => '#1A1A1A',
                'Xám'   => '#8A8D8F',
                'Đỏ'    => '#A32020',
                'Xanh'  => '#2C4F7C',
                'Bạc'   => '#C6C8CA',
            ],

            'variants' => [
                [
                    'name'        => 'VF 8 Eco',
                    'price'       => 1_109_000_000,
                    'note'        => 'Giá thuê pin. 349 mã lực, quãng đường 420 km (NEDC).',
                    'battery_kwh' => 82,
                    'range_km'    => 420,
                ],
                [
                    'name'        => 'VF 8 Plus',
                    'price'       => 1_269_000_000,
                    'note'        => 'Giá thuê pin. 402 mã lực, nội thất da, mâm 21 inch.',
                    'battery_kwh' => 82,
                    'range_km'    => 447,
                ],
            ],

            'specs' => [
                'Động cơ & hiệu năng' => [
                    'Công suất (Eco / Plus)' => '260 kW (349 mã lực) / 300 kW (402 mã lực)',
                    'Mô-men xoắn'            => '500 Nm (Eco) / 620 Nm (Plus)',
                    'Dẫn động'               => 'Hai cầu AWD',
                    'Tăng tốc 0–100 km/h'    => '5,5 giây (Plus)',
                ],
                'Pin & sạc' => [
                    'Dung lượng pin'     => '82 kWh',
                    'Quãng đường (NEDC)' => '420 km (Eco) / 447 km (Plus)',
                    'Sạc nhanh DC'       => '10–70% trong khoảng 24 phút',
                ],
                'Kích thước' => [
                    'Dài × Rộng × Cao' => '4.750 × 1.934 × 1.667 mm',
                    'Chiều dài cơ sở'  => '2.950 mm',
                    'Số chỗ ngồi'      => '5',
                ],
            ],

            'seo' => ['title' => 'VinFast VF 8 — giá Eco và Plus, thông số kỹ thuật'],
        ];
    }

    protected function vf9(): array
    {
        return [
            'slug'       => 'vinfast-vf-9',
            'name'       => 'VinFast VF 9',
            'tagline'    => 'SUV điện cỡ E, ba hàng ghế',
            'category'   => 'suv-co-e',
            'price_from' => 1_491_000_000,
            'hero'       => true,

            'highlights' => [
                ['value' => '438', 'unit' => 'km', 'label' => 'Quãng đường mỗi lần sạc (NEDC)'],
                ['value' => '402', 'unit' => 'mã lực', 'label' => 'Công suất tối đa'],
                ['value' => '7',   'unit' => 'chỗ', 'label' => 'Số chỗ ngồi'],
            ],

            'media' => [
                'Thư viện' => [
                    'layout' => 'carousel',
                    'items'  => ['Góc trước', 'Góc bên', 'Góc sau', 'Hàng ghế hai', 'Hàng ghế ba'],
                ],
                'Nội thất' => [
                    'intro'  => 'Ba hàng ghế, bản Plus có ghế thương gia chỉnh điện ở hàng hai.',
                    'layout' => 'cols-2',
                    'items'  => ['Khoang lái', 'Ghế thương gia'],
                ],
            ],

            'story' => [
                'Vận hành' => "Hai mô-tơ điện, dẫn động bốn bánh, hệ thống treo khí nén trên bản Plus.\n"
                    .'Bốn chế độ lái, cân bằng giữa êm ái và quãng đường đi được.',
            ],

            'tables' => [
                'Thời gian sạc' => [
                    'Sạc nhanh DC (10–70%)' => 'khoảng 35 phút',
                    'Sạc AC 11 kW (0–100%)' => 'khoảng 10 giờ',
                ],
            ],

            'colors' => [
                'Trắng' => '#F2F2F2',
                'Đen'   => '#1A1A1A',
                'Xám'   => '#8A8D8F',
                'Xanh'  => '#2C4F7C',
                'Bạc'   => '#C6C8CA',
            ],

            'variants' => [
                [
                    'name'        => 'VF 9 Eco',
                    'price'       => 1_491_000_000,
                    'note'        => 'Giá thuê pin. 7 chỗ, quãng đường 438 km (NEDC).',
                    'battery_kwh' => 92,
                    'range_km'    => 438,
                ],
                [
                    'name'        => 'VF 9 Plus',
                    'price'       => 1_769_000_000,
                    'note'        => 'Giá thuê pin. Ghế thương gia hàng hai, treo khí nén.',
                    'battery_kwh' => 92,
                    'range_km'    => 423,
                ],
            ],

            'specs' => [
                'Động cơ & hiệu năng' => [
                    'Công suất tối đa'  => '300 kW (402 mã lực)',
                    'Mô-men xoắn'       => '620 Nm',
                    'Dẫn động'          => 'Hai cầu AWD',
                ],
                'Pin & sạc' => [
                    'Dung lượng pin'     => '92 kWh',
                    'Quãng đường (NEDC)' => '438 km (Eco) / 423 km (Plus)',
                    'Sạc nhanh DC'       => '10–70% trong khoảng 35 phút',
                ],
                'Kích thước' => [
                    'Dài × Rộng × Cao' => '5.118 × 2.254 × 1.696 mm',
                    'Chiều dài cơ sở'  => '3.150 mm',
                    'Số chỗ ngồi'      => '6 hoặc 7',
                ],
            ],

            'seo' => ['title' => 'VinFast VF 9 — giá Eco và Plus, thông số kỹ thuật'],
        ];
    }
}
