<?php

use App\Models\Banner;
use App\Models\Category;
use App\Models\Dealer;
use App\Models\Form;
use App\Models\FormField;
use App\Models\Lead;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Page;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\Product;
use App\Models\ProductOption;
use App\Models\ProductVariant;
use App\Models\Province;
use App\Models\Redirect;
use App\Models\Setting;
use App\Models\Template;

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
        'product' => ['single' => 'Xe',        'plural' => 'Dòng xe'],
        'variant' => ['single' => 'Phiên bản', 'plural' => 'Phiên bản'],
        'option' => ['single' => 'Màu xe',    'plural' => 'Bảng màu'],
        'sections' => 'Chi tiết xe',
        'specs' => 'Thông số kỹ thuật',
    ],

    // Bật/tắt từng khối. Admin ẩn khối tắt, API không trả field tương ứng.
    'features' => [
        'variants' => true,
        'options' => true,
        'specs' => true,
        'highlights' => true,
        'posts' => true,
        'pages' => true,
        'forms' => true,
        'dealers' => true,
        'fee_calc' => true,

        // So sánh chi phí nhiên liệu xe điện vs xe xăng/dầu ở trang chi tiết
        // xe — khác `fee_calc` (lệ phí lăn bánh). Cần biến thể có battery_kwh
        // + range_km, không thì mục tự ẩn dù bật.
        'fuel_calc' => true,

        // Banner hero trang chủ. Tắt thì hero lùi về dùng ảnh của mặt hàng.
        'banners' => true,

        // Bộ tính trả góp ở trang chi tiết xe. Chỉ tính khoản vay và lãi —
        // lệ phí lăn bánh do bộ phận khác lo, không thuộc phạm vi web này.
        'loan_calc' => true,
    ],

    // Gợi ý tên khi bấm "Thêm mục" trong repeater sections.
    'section_presets' => ['Thư viện', 'Ngoại thất', 'Nội thất', 'Mâm xe', 'Vận hành'],

    // Layout cho phép chọn trong một mục.
    'section_layouts' => [
        'slider' => 'Slider',
        'cols-1' => '1 cột',
        'cols-2' => '2 cột',
        'cols-3' => '3 cột',

        // Bố cục dựng theo bản thiết kế — xem partials/section/media.blade.php.
        'gallery' => 'Thư viện lớn (1 ảnh to + 2 ảnh nhỏ)',
        'split' => 'Chữ một bên, ảnh một bên',
        'split-alt' => 'Chữ một bên, ảnh một bên (ảnh trước)',
        'carousel' => 'Băng chuyền (mũi tên chuyển ảnh)',
        'tabs' => 'Tab đánh số (01, 02, 03…)',

        // Cho mục kiểu `table`: mỗi dòng thành một ô chỉ số lớn thay vì
        // hàng bảng — dùng ở trang "Về chúng tôi".
        'stats' => 'Dải chỉ số (số to, nhãn nhỏ)',
    ],

    // Kiểu mục. 9/10 lần chỉ dùng `media`.
    'section_types' => [
        'media' => 'Ảnh',
        'text' => 'Văn bản',
        'video' => 'Video',
        'table' => 'Bảng',
        'form' => 'Form',
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
            'label' => 'Chung',
            'fields' => [
                'site_name' => ['label' => 'Tên website', 'type' => 'text'],

                // Meta description của trang chủ.
                'site_description' => ['label' => 'Mô tả ngắn', 'type' => 'textarea'],

                'hotline' => ['label' => 'Hotline', 'type' => 'text'],
                'email' => ['label' => 'Email liên hệ', 'type' => 'email'],
                'address' => ['label' => 'Địa chỉ', 'type' => 'textarea'],
                'opening_hours' => ['label' => 'Giờ mở cửa', 'type' => 'text'],
                'company_name' => ['label' => 'Tên pháp nhân đầy đủ (hiện ở dòng bản quyền cuối trang)', 'type' => 'text'],
                'tax_code' => ['label' => 'Mã số thuế', 'type' => 'text'],
                'logo' => ['label' => 'Logo', 'type' => 'image'],
                'favicon' => ['label' => 'Favicon', 'type' => 'image'],
                'map_image' => ['label' => 'Ảnh bản đồ chỉ đường', 'type' => 'image'],
                'map_url' => ['label' => 'Link Google Maps', 'type' => 'url'],
                'visit_title' => ['label' => 'Tiêu đề thẻ liên hệ ở trang tĩnh', 'type' => 'text'],
                'brochure_url' => ['label' => 'Link brochure (nút ở trang chi tiết)', 'type' => 'url'],
            ],
        ],

        // Các khối nội dung của trang chủ. Khoá nào để trống thì cả khối tự
        // ẩn ở frontend — không có chữ mẫu chết trong view.
        'home' => [
            'label' => 'Trang chủ',
            'fields' => [
                'brand_sub' => ['label' => 'Dòng phụ cạnh tên (VD "Bắc Giang")', 'type' => 'text'],
                'header_cta' => ['label' => 'Nhãn nút bên phải header', 'type' => 'text'],
                'promo_text' => ['label' => 'Băng khuyến mãi trên cùng', 'type' => 'text'],
                'promo_url' => ['label' => 'Link băng khuyến mãi', 'type' => 'url'],

                'offer_note' => ['label' => 'Ưu đãi — dòng nhỏ', 'type' => 'text'],
                'offer_title' => ['label' => 'Ưu đãi — tiêu đề', 'type' => 'text'],
                'offer_text' => ['label' => 'Ưu đãi — mô tả', 'type' => 'textarea'],

                'charging_note' => ['label' => 'Pin & trạm sạc — dòng nhỏ', 'type' => 'text'],
                'charging_title' => ['label' => 'Pin & trạm sạc — tiêu đề', 'type' => 'text'],
                'charging_text' => ['label' => 'Pin & trạm sạc — mô tả', 'type' => 'textarea'],
                'charging_image' => ['label' => 'Pin & trạm sạc — ảnh', 'type' => 'image'],

                'care_note' => ['label' => 'Chăm sóc chủ xe — dòng nhỏ', 'type' => 'text'],
                'care_title' => ['label' => 'Chăm sóc chủ xe — tiêu đề', 'type' => 'text'],
                'care_image' => ['label' => 'Chăm sóc chủ xe — ảnh', 'type' => 'image'],
                'care_stats' => ['label' => 'Chăm sóc chủ xe — chỉ số (mỗi dòng "10 năm|Bảo hành xe và pin")', 'type' => 'textarea'],
            ],
        ],

        // Popup thu lead. Bỏ trống `popup_form` là tắt hẳn.
        'popup' => [
            'label' => 'Popup thu lead',
            'fields' => [
                'popup_form' => ['label' => 'Khoá form dùng cho popup (VD dang-ky-nhan-tin)', 'type' => 'text'],
                'popup_title' => ['label' => 'Tiêu đề popup', 'type' => 'text'],
                'popup_text' => ['label' => 'Mô tả ngắn', 'type' => 'textarea'],
                'popup_delay' => ['label' => 'Hiện sau bao nhiêu giây', 'type' => 'number'],
                'popup_days' => ['label' => 'Đóng rồi thì im bao nhiêu ngày', 'type' => 'number'],
                'popup_everywhere' => ['label' => 'Hiện ở mọi trang (mặc định chỉ trang chủ)', 'type' => 'toggle'],
            ],
        ],

        // Trang Trạm sạc & dịch vụ — xem frontend/services.blade.php.
        'service' => [
            'label' => 'Trạm sạc & dịch vụ',
            'fields' => [
                'service_note' => ['label' => 'Dòng nhỏ trên tiêu đề', 'type' => 'text'],
                'service_title' => ['label' => 'Tiêu đề trang', 'type' => 'text'],
                'service_map' => ['label' => 'Ảnh bản đồ trạm sạc', 'type' => 'image'],

                'stations' => ['label' => 'Danh sách trạm (mỗi dòng "Tên|Trạng thái|Thông tin|ok hoặc warn")', 'type' => 'textarea'],
                'stations_more' => ['label' => 'Nhãn nút xem thêm trạm', 'type' => 'text'],
                'stations_more_url' => ['label' => 'Link nút xem thêm trạm', 'type' => 'url'],

                'services_title' => ['label' => 'Tiêu đề khối dịch vụ', 'type' => 'text'],
                'services' => ['label' => 'Dịch vụ (mỗi dòng "Tên|Mô tả|Nhãn nút|Link")', 'type' => 'textarea'],
            ],
        ],
        'social' => [
            'label' => 'Mạng xã hội',
            'fields' => [
                'facebook' => ['label' => 'Facebook', 'type' => 'url'],
                'youtube' => ['label' => 'YouTube', 'type' => 'url'],
                'tiktok' => ['label' => 'TikTok', 'type' => 'url'],
                'zalo' => ['label' => 'Zalo', 'type' => 'text'],
            ],
        ],
        'tracking' => [
            'label' => 'Đo lường',
            'fields' => [
                'gtm_id' => ['label' => 'Google Tag Manager ID', 'type' => 'text'],
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
        'product' => Product::class,
        'banner' => Banner::class,
        'variant' => ProductVariant::class,
        'option' => ProductOption::class,
        'category' => Category::class,
        'post' => Post::class,
        'post_category' => PostCategory::class,
        'page' => Page::class,
        'menu' => Menu::class,
        'menu_item' => MenuItem::class,
        'setting' => Setting::class,
        'redirect' => Redirect::class,
        'form' => Form::class,
        'form_field' => FormField::class,
        'lead' => Lead::class,
        'template' => Template::class,
        'province' => Province::class,
        'dealer' => Dealer::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | API
    |--------------------------------------------------------------------------
    */
    'api' => [
        'enabled' => true,
        'prefix' => 'api/v1',
        'middleware' => ['api'],
        'per_page' => 24,
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
            'posts' => 3,    // số tin mới nhất
        ],

        // Khoá menu dựng ở màn hình Menu. Chưa tạo thì phần đó không render.
        'menus' => [
            'header' => 'header',
            'footer' => 'footer',
        ],

        // Form hiện ở cuối trang chi tiết sản phẩm — key nào đã nhúng giữa
        // trang qua mục kiểu `form` (VD "dat-lich-lai-thu" gắn tự động cho
        // mọi xe trong BrandSeeder) thì không lặp lại ở đây. [] = không hiện.
        'product_forms' => ['dang-ky-tu-van'],

        // Form ở băng đăng ký nhận tin ngay trên footer — chỉ cần ô email.
        // null = ẩn hẳn băng đó.
        'newsletter_form' => 'dang-ky-nhan-tin',

        /*
        | Trang "Đặt cọc & lái thử" (/dat-coc) — wizard 3 bước.
        |
        | `forms` là các form khách chọn ở đầu trang, theo thứ tự tab; mỗi
        | form vẫn POST vào /gui-form/{form} như mọi form khác nên honeypot,
        | chống trùng và mail y hệt. null = tắt hẳn trang.
        */
        'booking' => [
            'forms' => ['dat-coc', 'dat-lich-lai-thu'],

            // Ô nào lên bước 1 (đứng cạnh bộ chọn xe); còn lại xuống bước 2.
            'step1_fields' => ['location_type'],

            // Ô select nào hiện thành lưới thẻ bấm thay vì dropdown. Quá số
            // lựa chọn này thì tự về dropdown cho khỏi vỡ lưới.
            'card_fields' => ['payment_method', 'preferred_time', 'location_type'],
            'card_max_options' => 4,

            // Số tiền cọc hiện ở bảng tóm tắt bước 2. null = ẩn bảng đó.
            'deposit' => 15000000,
        ],

        // Danh mục dùng cho trang Phụ kiện (/phu-kien). Mặt hàng thuộc danh
        // mục này KHÔNG hiện ở trang chủ và danh sách xe — phụ kiện lẫn vào
        // dải xe là lỗi thấy ngay. null = tắt hẳn trang.
        'accessory_category' => 'phu-kien',

        // Trang Trạm sạc & dịch vụ (/tram-sac-dich-vu). Nội dung lấy từ
        // Cài đặt → Trạm sạc & dịch vụ. false = tắt hẳn trang.
        'services_page' => true,
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
        'petrol_price' => 24500, // đ/lít xăng
        'diesel_price' => 21500, // đ/lít dầu
    ],

    /*
    |--------------------------------------------------------------------------
    | Trả góp (giá trị mặc định của bộ tính)
    |--------------------------------------------------------------------------
    |
    | Lãi suất tham khảo, không phải cam kết của ngân hàng. Đổi theo thời điểm.
    */
    'loan' => [
        'down_payment_percent' => 30,     // % trả trước gợi ý sẵn
        'annual_rate' => 9.0,             // %/năm
        'months' => 60,                   // kỳ trả mặc định
        'month_options' => [12, 24, 36, 48, 60, 72, 84],
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
        'product' => '/san-pham',
        'category' => '/danh-muc',
        'post' => '/tin-tuc',
        'post_category' => '/chuyen-muc',
        'page' => '',   // trang tĩnh nằm ngay gốc: /gioi-thieu

        // Ba trang cố định của frontend (không theo slug). Bật/tắt ở
        // `frontend` bên dưới, không phải ở đây.
        'booking' => '/dat-coc',
        'accessory' => '/phu-kien',
        'service' => '/tram-sac-dich-vu',
        'dealer' => '/he-thong-dai-ly',
        'search' => '/tim-kiem',
        'compare' => '/so-sanh',
    ],

    'seo' => [
        // Bật sitemap.xml tại {APP_URL}/sitemap.xml
        'sitemap' => true,

        // Loại trang đưa vào sitemap
        'sitemap_includes' => ['product', 'category', 'post', 'page'],

        // Tổ chức đứng sau — dùng cho JSON-LD Organization
        'organization' => [
            'name' => null,   // null thì lấy settings('site_name')
            'logo' => null,   // null thì lấy settings('logo')
            'sameAs' => [],     // link mạng xã hội
        ],
    ],

];
