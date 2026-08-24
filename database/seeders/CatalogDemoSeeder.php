<?php

namespace Database\Seeders;

use App\Support\Catalog;
use Illuminate\Database\Seeder;

/**
 * Khung của một site: cài đặt, menu, form đặt lịch, một trang tĩnh, một bài
 * viết. **Không có xe nào** — xe do seeder theo hãng lo
 * (`database/seeders/Brands/`), để đổi hãng không phải sửa file này.
 *
 * Chạy lại được nhiều lần (updateOrCreate).
 */
class CatalogDemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->form();
        $this->content();
        $this->settings();
        $this->menus();
    }

    /**
     * Hai form ở trang chi tiết xe:
     *   - "dat-lich-lai-thu": brand seeder tự nhúng giữa trang (xem
     *     BrandSeeder::formKey()/sections()).
     *   - "dat-coc": nằm cuối trang, khai ở config('catalog.frontend.product_forms').
     */
    protected function form(): void
    {
        $testDrive = Catalog::query('form')->updateOrCreate(
            ['key' => 'dat-lich-lai-thu'],
            [
                'name'             => 'Đặt lịch lái thử',
                'description'      => 'Để lại thông tin, tư vấn viên sẽ liên hệ lại.',
                'success_message'  => 'Đã nhận thông tin, chúng tôi sẽ liên hệ sớm.',
            ],
        );

        $testDrive->fields()->delete();
        $testDrive->fields()->createMany([
            ['key' => 'name', 'label' => 'Họ tên', 'type' => 'text', 'rules' => ['required'], 'sort' => 1, 'width' => 'half'],
            ['key' => 'phone', 'label' => 'Điện thoại', 'type' => 'tel', 'rules' => ['required'], 'sort' => 2, 'width' => 'half'],
            ['key' => 'preferred_time', 'label' => 'Khung giờ mong muốn', 'type' => 'select', 'rules' => ['nullable'], 'sort' => 3, 'width' => 'half',
                'options' => ['sang-t7' => 'Sáng thứ 7', 'chieu-t7' => 'Chiều thứ 7', 'sang-cn' => 'Sáng chủ nhật', 'chieu-cn' => 'Chiều chủ nhật']],
            ['key' => 'location_type', 'label' => 'Hình thức', 'type' => 'select', 'rules' => ['nullable'], 'sort' => 4, 'width' => 'half',
                'options' => ['tai-nha' => 'Lái thử tại nhà', 'showroom' => 'Tại showroom']],
            ['key' => 'note', 'label' => 'Ghi chú', 'type' => 'textarea', 'rules' => ['nullable'], 'sort' => 5],
        ]);

        $deposit = Catalog::query('form')->updateOrCreate(
            ['key' => 'dat-coc'],
            [
                'name'             => 'Đặt cọc',
                'description'      => 'Cọc giữ suất xe — hoàn lại 100% trong 7 ngày nếu bạn đổi ý. Thanh toán sau khi tư vấn viên xác nhận.',
                'success_message'  => 'Đã nhận yêu cầu đặt cọc, tư vấn viên sẽ gọi lại trong vòng 2 giờ làm việc để xác nhận.',
            ],
        );

        $deposit->fields()->delete();
        $deposit->fields()->createMany([
            ['key' => 'name', 'label' => 'Họ và tên', 'type' => 'text', 'rules' => ['required'], 'sort' => 1, 'width' => 'half'],
            ['key' => 'phone', 'label' => 'Số điện thoại', 'type' => 'tel', 'rules' => ['required'], 'sort' => 2, 'width' => 'half'],
            ['key' => 'email', 'label' => 'Email', 'type' => 'email', 'rules' => ['nullable'], 'sort' => 3, 'width' => 'half'],
            ['key' => 'payment_method', 'label' => 'Phương thức thanh toán cọc', 'type' => 'select', 'rules' => ['nullable'], 'sort' => 4, 'width' => 'half',
                'options' => ['qr' => 'Chuyển khoản QR', 'the' => 'Thẻ ngân hàng', 'showroom' => 'Tại showroom']],
            ['key' => 'note', 'label' => 'Ghi chú', 'type' => 'textarea', 'rules' => ['nullable'], 'sort' => 5],
        ]);

        // Băng đăng ký nhận tin trên footer — khai ở
        // config('catalog.frontend.newsletter_form'), chỉ một ô email.
        $newsletter = Catalog::query('form')->updateOrCreate(
            ['key' => 'dang-ky-nhan-tin'],
            [
                'name'            => 'Đăng ký nhận thông tin',
                'description'     => 'Chương trình khuyến mãi và tin dịch vụ từ đại lý — 1–2 email mỗi tháng.',
                'success_message' => 'Đã đăng ký! Hẹn gặp bạn trong hộp thư.',
            ],
        );

        $newsletter->fields()->delete();
        $newsletter->fields()->createMany([
            ['key' => 'email', 'label' => 'Email', 'type' => 'email', 'rules' => ['required', 'email'], 'sort' => 1],
        ]);
    }

    /** Trang tĩnh + bài viết — dùng đúng cơ chế `sections` của sản phẩm. */
    protected function content(): void
    {
        Catalog::query('page')->updateOrCreate(
            ['slug' => 'gioi-thieu'],
            [
                'title'    => 'Giới thiệu',
                'status'   => 'published',
                'sections' => [[
                    'title' => 'Về chúng tôi',
                    'type'  => 'text',
                    'body'  => "Trang tĩnh mẫu.\nSửa nội dung trong admin → Trang.",
                ]],
            ],
        );

        $postCategory = Catalog::query('post_category')->firstOrCreate(
            ['slug' => 'trai-nghiem'],
            ['name' => 'Trải nghiệm'],
        );

        Catalog::query('post')->updateOrCreate(
            ['slug' => 'bai-viet-mau'],
            [
                'title'            => 'Bài viết mẫu',
                'excerpt'          => 'Tóm tắt ngắn hiện ở thẻ bài viết.',
                'post_category_id' => $postCategory->id,
                'status'           => 'published',
                'published_at'     => now()->subDays(3),
                'sections'         => [[
                    'title' => 'Đoạn một',
                    'type'  => 'text',
                    'body'  => 'Bài viết dùng chung bộ mục với trang xe.',
                ]],
            ],
        );
    }

    /** Cài đặt: những giá trị layout frontend đọc ra ngay. */
    protected function settings(): void
    {
        $setting = Catalog::model('setting');

        $setting::put('site_name', 'Website ô tô demo');
        $setting::put('site_description', 'Dữ liệu mẫu để soi thử admin, API và luồng frontend.');
        $setting::put('hotline', '1900 0000');
        $setting::put('email', 'demo@example.test');
        $setting::put('address', 'Số 1, đường Demo, Hà Nội');
        $setting::put('opening_hours', 'Mở cửa 8:00–19:00 hằng ngày');

        // Các khoá dưới đây frontend đọc ra nếu có, bỏ trống thì khối tương
        // ứng tự ẩn — xem partials/header.blade.php và home.blade.php.
        $setting::put('brand_sub', 'Đại lý demo');
        $setting::put('promo_text', 'Trả góp 0% lãi suất 24 tháng — xem chương trình đang chạy');
        $setting::put('offer_note', 'Ưu đãi trong tháng');
        $setting::put('offer_title', 'Trả góp 0% lãi suất 24 tháng cho các dòng xe điện');
        $setting::put('offer_text', 'Kèm gói lắp sạc tại nhà miễn phí công lắp đặt cho khách đặt cọc tại đại lý.');

        $setting::put('charging_note', 'Pin & trạm sạc');
        $setting::put('charging_title', 'Sạc đầy trong lúc bạn đi chợ');
        $setting::put('charging_text', 'Mạng lưới điểm sạc phủ khắp tỉnh, sạc nhanh 10–70% dưới 30 phút, kèm dịch vụ khảo sát và lắp đặt sạc tại nhà miễn phí công lắp.');

        $setting::put('care_note', 'Chăm sóc chủ xe');
        $setting::put('care_title', 'Yên tâm suốt 10 năm sở hữu');
        // Dạng "giá trị|nhãn", ngăn nhau bằng dấu chấm phẩy.
        $setting::put('care_stats', '10 năm|Bảo hành xe và pin;24/7|Cứu hộ lưu động toàn tỉnh;45 phút|Thời gian cứu hộ trung bình;4,9/5|Điểm hài lòng dịch vụ');
    }

    /**
     * Menu header + footer. Mục "Dòng xe" để trống mục con — brand seeder gắn
     * từng xe vào dưới đó, nên đổi hãng không phải sửa menu ở đây.
     */
    protected function menus(): void
    {
        $header = Catalog::query('menu')->updateOrCreate(['key' => 'header'], ['name' => 'Menu chính']);
        $header->items()->delete();

        $header->items()->create([
            'label' => catalog_label('product.plural'),
            'url'   => \App\Support\Url::prefix('product') ?: '/',
            'sort'  => 1,
        ]);
        $header->items()->create(['label' => 'Tin tức', 'url' => \App\Support\Url::prefix('post'), 'sort' => 2]);
        $header->items()->create(['label' => 'Giới thiệu', 'url' => '/gioi-thieu', 'sort' => 3]);

        $footer = Catalog::query('menu')->updateOrCreate(['key' => 'footer'], ['name' => 'Menu chân trang']);
        $footer->items()->delete();
        $footer->items()->create(['label' => 'Giới thiệu', 'url' => '/gioi-thieu', 'sort' => 1]);
        $footer->items()->create(['label' => 'Tin tức', 'url' => \App\Support\Url::prefix('post'), 'sort' => 2]);
    }
}
