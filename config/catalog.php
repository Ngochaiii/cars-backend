<?php

/*
|--------------------------------------------------------------------------
| Cấu hình mặc định của core
|--------------------------------------------------------------------------
|
| Mỗi dự án publish file này ra config/catalog.php của mình rồi sửa
| `labels`, `features`, `section_presets`. Đây là thứ thay cho migration
| khi đổi hãng hoặc đổi mặt hàng — xem mục 5 của tài liệu kiến trúc.
|
*/

return [

    // Chữ hiển thị trong admin. Không bao giờ lấy từ tên cột.
    'labels' => [
        'product'  => ['single' => 'Xe',        'plural' => 'Dòng xe'],
        'variant'  => ['single' => 'Phiên bản', 'plural' => 'Phiên bản'],
        'option'   => ['single' => 'Màu xe',    'plural' => 'Bảng màu'],
        'sections' => 'Chi tiết xe',
        'specs'    => 'Thông số kỹ thuật',
    ],

    // Bật/tắt từng khối. Admin ẩn khối tắt, API không trả field tương ứng.
    'features' => [
        'variants'   => true,
        'options'    => true,
        'specs'      => true,
        'highlights' => true,
        'posts'      => true,
        'pages'      => true,
        'forms'      => true,
        'dealers'    => true,
        'fee_calc'   => true,

        // So sánh chi phí nhiên liệu xe điện vs xe xăng/dầu ở trang chi tiết
        // xe — khác `fee_calc` (lệ phí lăn bánh). Cần biến thể có battery_kwh
        // + range_km, không thì mục tự ẩn dù bật.
        'fuel_calc'  => true,
    ],

    // Gợi ý tên khi bấm "Thêm mục" trong repeater sections.
    'section_presets' => ['Thư viện', 'Ngoại thất', 'Nội thất', 'Mâm xe', 'Vận hành'],

    // Layout cho phép chọn trong một mục.
    'section_layouts' => [
        'slider' => 'Slider',
        'cols-1' => '1 cột',
        'cols-2' => '2 cột',
        'cols-3' => '3 cột',
    ],

    // Kiểu mục. 9/10 lần chỉ dùng `media`.
    'section_types' => [
        'media'  => 'Ảnh',
        'text'   => 'Văn bản',
        'video'  => 'Video',
        'table'  => 'Bảng',
        'form'   => 'Form',
        'custom' => 'Khối riêng',
    ],

    /*
    |--------------------------------------------------------------------------
    | Settings
    |--------------------------------------------------------------------------
    |
    | Màn hình Cài đặt dựng từ khai báo này — cần thêm mục thì khai báo ở đây,
    | KHÔNG thêm cột. Giá trị lưu vào bảng `settings` dạng key/value.
    |
    | Kiểu ô: text · textarea · url · email · number · image · toggle · color
    |
    */
    'settings' => [
        'general' => [
            'label'  => 'Chung',
            'fields' => [
                'site_name' => ['label' => 'Tên website', 'type' => 'text'],

                // Meta description của trang chủ.
                'site_description' => ['label' => 'Mô tả ngắn', 'type' => 'textarea'],

                'hotline'   => ['label' => 'Hotline', 'type' => 'text'],
                'email'     => ['label' => 'Email liên hệ', 'type' => 'email'],
                'address'   => ['label' => 'Địa chỉ', 'type' => 'textarea'],
                'logo'      => ['label' => 'Logo', 'type' => 'image'],
                'favicon'   => ['label' => 'Favicon', 'type' => 'image'],
            ],
        ],
        'social' => [
            'label'  => 'Mạng xã hội',
            'fields' => [
                'facebook' => ['label' => 'Facebook', 'type' => 'url'],
                'youtube'  => ['label' => 'YouTube', 'type' => 'url'],
                'tiktok'   => ['label' => 'TikTok', 'type' => 'url'],
                'zalo'     => ['label' => 'Zalo', 'type' => 'text'],
            ],
        ],
        'tracking' => [
            'label'  => 'Đo lường',
            'fields' => [
                'gtm_id'         => ['label' => 'Google Tag Manager ID', 'type' => 'text'],
                'facebook_pixel' => ['label' => 'Facebook Pixel ID', 'type' => 'text'],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Model
    |--------------------------------------------------------------------------
    | Core không hardcode Product::class. Dự án nào cần thêm hành vi thì
    | extend model của core rồi trỏ lại ở đây.
    */
    'models' => [
        'product'       => \App\Models\Product::class,
        'variant'       => \App\Models\ProductVariant::class,
        'option'        => \App\Models\ProductOption::class,
        'category'      => \App\Models\Category::class,
        'post'          => \App\Models\Post::class,
        'post_category' => \App\Models\PostCategory::class,
        'page'          => \App\Models\Page::class,
        'menu'          => \App\Models\Menu::class,
        'menu_item'     => \App\Models\MenuItem::class,
        'setting'       => \App\Models\Setting::class,
        'redirect'      => \App\Models\Redirect::class,
        'form'          => \App\Models\Form::class,
        'form_field'    => \App\Models\FormField::class,
        'lead'          => \App\Models\Lead::class,
        'template'      => \App\Models\Template::class,
        'province'      => \App\Models\Province::class,
        'dealer'        => \App\Models\Dealer::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | API
    |--------------------------------------------------------------------------
    */
    'api' => [
        'enabled'    => true,
        'prefix'     => 'api/v1',
        'middleware' => ['api'],
        'per_page'   => 24,
    ],

    /*
    |--------------------------------------------------------------------------
    | Lead (form khách gửi)
    |--------------------------------------------------------------------------
    */
    'leads' => [
        // Ô bẫy bot: form frontend thêm một input ẩn tên này. Người thật để
        // trống; bot điền vào thì bỏ qua lặng lẽ, vẫn trả 201 để bot không dò được.
        'honeypot' => 'website',

        // Cùng form + cùng số điện thoại trong bao nhiêu phút thì coi là trùng,
        // không tạo lead mới. 0 = tắt.
        'dedupe_minutes' => 5,
    ],

    /*
    |--------------------------------------------------------------------------
    | Frontend (Blade)
    |--------------------------------------------------------------------------
    |
    | Số lượng và khoá menu/form của trang khách xem. Mỗi hãng đổi ở đây,
    | không sửa controller.
    |
    */
    'frontend' => [
        'per_page' => 12,       // danh sách sản phẩm / tin tức

        'home' => [
            'products' => 8,    // số thẻ sản phẩm trên trang chủ
            'posts'    => 3,    // số tin mới nhất
        ],

        // Khoá menu dựng ở màn hình Menu. Chưa tạo thì phần đó không render.
        'menus' => [
            'header' => 'header',
            'footer' => 'footer',
        ],

        // Form hiện ở cuối trang chi tiết sản phẩm — key nào đã nhúng giữa
        // trang qua mục kiểu `form` (VD "dat-lich-lai-thu" gắn tự động cho
        // mọi xe trong BrandSeeder) thì không lặp lại ở đây. [] = không hiện.
        'product_forms' => ['dat-coc'],

        // Form ở băng đăng ký nhận tin ngay trên footer — chỉ cần ô email.
        // null = ẩn hẳn băng đó.
        'newsletter_form' => 'dang-ky-nhan-tin',
    ],

    /*
    |--------------------------------------------------------------------------
    | Fuel calculator (giá điện/nhiên liệu tham khảo)
    |--------------------------------------------------------------------------
    |
    | Đổi theo giá điện/xăng dầu thực tế của thời điểm — không phải báo giá
    | chính thức. electricity_price tính cho sạc tại nhà.
    */
    'fuel_calc' => [
        'electricity_price' => 3900,   // đ/kWh
        'petrol_price'       => 24500, // đ/lít xăng
        'diesel_price'       => 21500, // đ/lít dầu
    ],

    /*
    |--------------------------------------------------------------------------
    | Admin (Filament)
    |--------------------------------------------------------------------------
    */
    'admin' => [
        'navigation_group' => 'Nội dung',
    ],

    /*
    |--------------------------------------------------------------------------
    | Đường dẫn frontend & SEO
    |--------------------------------------------------------------------------
    |
    | Tiền tố URL của từng loại trang. Dùng để:
    |   - suy ra link cho mục menu (target_type → url)
    |   - tự tạo redirect 301 khi đổi slug bản đã publish
    |   - dựng sitemap.xml
    |
    | Đây là hình dạng URL của frontend, mỗi hãng đổi được mà không sửa core.
    */
    'routes' => [
        'product'  => '/san-pham',
        'category' => '/danh-muc',
        'post'     => '/tin-tuc',
        'post_category' => '/chuyen-muc',
        'page'     => '',   // trang tĩnh nằm ngay gốc: /gioi-thieu
    ],

    'seo' => [
        // Bật sitemap.xml tại {APP_URL}/sitemap.xml
        'sitemap' => true,

        // Loại trang đưa vào sitemap
        'sitemap_includes' => ['product', 'category', 'post', 'page'],

        // Tổ chức đứng sau — dùng cho JSON-LD Organization
        'organization' => [
            'name'    => null,   // null thì lấy settings('site_name')
            'logo'    => null,   // null thì lấy settings('logo')
            'sameAs'  => [],     // link mạng xã hội
        ],
    ],

];
