<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Form;
use App\Models\Menu;
use App\Models\Page;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * Bước 11 — frontend Blade thật.
 *
 * Kiểm cái mà kiến trúc hứa: nội dung người nhập gõ trong admin thì hiện ra
 * đúng trên trang khách xem, ô trống thì không để lại vết, và tắt feature nào
 * thì khối đó biến mất.
 */
class FrontendTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Setting::put('site_name', 'Lexus Việt Nam');
        Setting::put('hotline', '1800 6088');
    }

    // --- Trang chủ & danh sách ---

    public function test_trang_chu_hien_ban_da_publish_va_bo_ban_nhap(): void
    {
        Product::create(['name' => 'Lexus GX 550', 'status' => 'published', 'published_at' => now()]);
        Product::create(['name' => 'Lexus LX 700h', 'status' => 'draft']);

        $this->get('/')
            ->assertOk()
            ->assertSee('Lexus GX 550')
            ->assertDontSee('Lexus LX 700h')
            ->assertSee('data-home-story', false)
            ->assertSee('data-home-hero', false)
            ->assertSee('Hành trình sở hữu')
            ->assertSee('Từ lựa chọn đầu tiên')
            ->assertSee('tools__item--feature', false)
            ->assertSee('data-home-reveal', false)
            ->assertSee('Lexus Việt Nam')      // tên site trong header
            ->assertSee('1800 6088');          // hotline
    }

    public function test_danh_sach_phan_trang_theo_config(): void
    {
        config(['catalog.frontend.per_page' => 2]);

        foreach (['A', 'B', 'C'] as $name) {
            Product::create(['name' => "Xe {$name}", 'status' => 'published']);
        }

        $this->get('/san-pham')
            ->assertOk()
            ->assertSee('Xe A')
            ->assertSee('Xe B')
            ->assertDontSee('Xe C')
            ->assertSee('page=2');
    }

    public function test_danh_muc_chi_hien_mat_hang_cua_no(): void
    {
        $suv = Category::create(['name' => 'SUV']);
        $sedan = Category::create(['name' => 'Sedan']);

        Product::create(['name' => 'Lexus GX 550', 'category_id' => $suv->id, 'status' => 'published']);
        Product::create(['name' => 'Lexus ES 300h', 'category_id' => $sedan->id, 'status' => 'published']);

        $this->get('/danh-muc/suv')
            ->assertOk()
            ->assertSee('Lexus GX 550')
            ->assertDontSee('Lexus ES 300h');
    }

    // --- Trang chi tiết mặt hàng ---

    /**
     * Trang chi tiết bám bản thiết kế: hero lấy TAGLINE làm tiêu đề,
     * hiện giá rút gọn và dựng các phiên bản thành thẻ chọn rõ ràng.
     */
    public function test_trang_chi_tiet_hien_du_hero_chi_so_mau_va_thong_so(): void
    {
        $product = Product::create([
            'name' => 'Lexus GX 550',
            'tagline' => 'Bản lĩnh chinh phục',
            'status' => 'published',
            'published_at' => now(),
            'price_from' => 5_990_000_000,
            'hero' => [
                'type' => 'image',
                'src' => 'catalog/hero/gx550.webp',
                'mobile_src' => 'catalog/hero/gx550-mobile.webp',
            ],
            'brochure_url' => 'https://example.com/gx550-brochure.pdf',
            'highlights' => [['value' => '349', 'unit' => 'mã lực', 'label' => 'Công suất']],
            'specs' => [['group' => 'Động cơ', 'rows' => [['label' => 'Dung tích', 'value' => '3.445 cm³']]]],
        ]);

        $product->variants()->create(['name' => 'GX 550 Luxury', 'price' => 6_890_000_000, 'is_default' => true]);
        $product->options()->create(['name' => 'Caviar Black', 'hex' => '#111111']);

        $this->get('/san-pham/lexus-gx-550')
            ->assertOk()
            ->assertSee('data-product-story', false)
            ->assertSee('data-product-nav', false)
            ->assertSee('data-story-hero', false)
            ->assertSee('data-story-cta', false)
            ->assertSee('Lexus GX 550')
            ->assertSee('Bản lĩnh chinh phục')  // tagline là tiêu đề lớn của hero
            ->assertSee('5,99 tỷ')              // hero dùng giá rút gọn
            ->assertSee('349')
            ->assertSee('Công suất')
            ->assertSee('GX 550 Luxury')        // khách chọn phiên bản ngay trên trang
            ->assertSee('Caviar Black')
            ->assertSee('Dung tích')
            ->assertSee('3.445 cm³')
            ->assertSee('catalog/hero/gx550.webp')
            ->assertSee('catalog/hero/gx550-mobile.webp')
            ->assertSee('data-swatch-fallback', false) // chưa có ảnh màu thì dùng hero, không để sân khấu trống
            ->assertSee('Màu tham khảo')
            ->assertSee('Cuộn để khám phá')
            ->assertSee('https://example.com/gx550-brochure.pdf')
            ->assertSee('<link rel="canonical"', false);
    }

    public function test_ban_nhap_thi_404(): void
    {
        Product::create(['name' => 'Lexus LX 700h', 'status' => 'draft']);

        $this->get('/san-pham/lexus-lx-700h')->assertNotFound();
    }

    public function test_thanh_hanh_dong_van_hien_khi_co_trang_dat_coc_nhung_chua_cau_hinh_form(): void
    {
        $this->assertTrue(Route::has('booking'));

        Product::create([
            'name' => 'Lexus RZ 450e',
            'status' => 'published',
            'price_from' => 3_210_000_000,
        ]);

        $this->get('/san-pham/lexus-rz-450e')
            ->assertOk()
            ->assertSee('class="order-bar"', false)
            ->assertSee('Đăng ký lái thử')
            ->assertSee(route('booking', ['xe' => 'lexus-rz-450e']), false);
    }

    public function test_tat_feature_thi_khoi_do_khong_render(): void
    {
        config(['catalog.features.variants' => false, 'catalog.features.specs' => false]);

        $product = Product::create([
            'name' => 'Lexus GX 550',
            'status' => 'published',
            'specs' => [['group' => 'Động cơ', 'rows' => [['label' => 'Dung tích', 'value' => '3.445 cm³']]]],
        ]);
        $product->variants()->create(['name' => 'GX 550 Luxury']);

        $this->get('/san-pham/lexus-gx-550')
            ->assertOk()
            ->assertDontSee('GX 550 Luxury')
            ->assertDontSee('3.445 cm³');
    }

    // --- Sections: từng kiểu mục ---

    public function test_muc_media_bo_trong_nhan_thi_khong_render_the_nhan(): void
    {
        Product::create([
            'name' => 'Lexus GX 550',
            'status' => 'published',
            'sections' => [
                [
                    'title' => 'Thư viện', 'intro' => '', 'type' => 'media', 'layout' => 'slider',
                    'items' => [['image' => 'gallery-01.webp', 'label' => '', 'desc' => '']],
                ],
                [
                    'title' => 'Mâm xe', 'intro' => 'Mâm hợp kim nhôm.', 'type' => 'media', 'layout' => 'cols-2',
                    'items' => [['image' => 'wheel-01.webp', 'label' => 'Mâm Luxury', 'desc' => 'Tối ưu đô thị.']],
                ],
            ],
        ]);

        $html = $this->get('/san-pham/lexus-gx-550')->assertOk()->getContent();

        // `slider` là dải cuộn ngang (.hstrip), không phải lưới `layout-*`.
        $this->assertStringContainsString('hstrip', $html);
        $this->assertStringContainsString('layout-cols-2', $html);
        $this->assertStringContainsString('Mâm Luxury', $html);
        $this->assertStringContainsString('Tối ưu đô thị.', $html);
        $this->assertStringContainsString('Mâm hợp kim nhôm.', $html);

        // Mục Thư viện chỉ có ảnh: không figcaption, không đoạn mở đầu rỗng
        $this->assertSame(1, substr_count($html, '<figcaption>'));
    }

    public function test_muc_van_ban_va_muc_bang(): void
    {
        Page::create([
            'title' => 'Giới thiệu',
            'status' => 'published',
            'sections' => [
                ['title' => 'Về chúng tôi', 'type' => 'text', 'body' => "Dòng một.\nDòng hai."],
                ['title' => 'Tỷ lệ mua lại', 'type' => 'table', 'rows' => [
                    ['label' => 'Năm 1', 'value' => '85%'],
                ]],
            ],
        ]);

        $this->get('/gioi-thieu')
            ->assertOk()
            ->assertSee('Dòng một.<br />', false)   // xuống dòng của người nhập được giữ
            ->assertSee('Tỷ lệ mua lại')
            ->assertSee('Năm 1')
            ->assertSee('85%');
    }

    public function test_muc_video_doi_link_youtube_thanh_khoi_nhung(): void
    {
        Product::create([
            'name' => 'Lexus GX 550',
            'status' => 'published',
            'sections' => [[
                'title' => 'Phim giới thiệu', 'type' => 'video',
                'video_url' => 'https://www.youtube.com/watch?v=abc123XYZ',
            ]],
        ]);

        $this->get('/san-pham/lexus-gx-550')
            ->assertOk()
            ->assertSee('https://www.youtube.com/embed/abc123XYZ', false);
    }

    public function test_muc_form_nhung_dung_form_theo_khoa(): void
    {
        $form = Form::create(['key' => 'lien-he', 'name' => 'Liên hệ']);
        $form->fields()->create(['key' => 'name', 'label' => 'Họ tên', 'type' => 'text', 'rules' => ['required']]);

        Page::create([
            'title' => 'Liên hệ',
            'status' => 'published',
            'sections' => [['title' => 'Gửi câu hỏi', 'type' => 'form', 'form_key' => 'lien-he']],
        ]);

        $this->get('/lien-he')
            ->assertOk()
            ->assertSee('Họ tên')
            ->assertSee(route('leads.store', 'lien-he'), false);

        // Form tắt thì mục im lặng, không để lại lỗi trên trang khách
        $form->update(['is_active' => false]);

        $this->get('/lien-he')->assertOk()->assertDontSee('Họ tên');
    }

    public function test_muc_rong_khong_render(): void
    {
        Product::create([
            'name' => 'Lexus GX 550',
            'status' => 'published',
            'sections' => [
                ['title' => 'Mục chưa nhập gì', 'type' => 'media', 'items' => []],
                ['title' => 'Thư viện', 'type' => 'media', 'items' => [['image' => 'a.webp']]],
            ],
        ]);

        $this->get('/san-pham/lexus-gx-550')
            ->assertOk()
            ->assertDontSee('Mục chưa nhập gì')
            ->assertSee('Thư viện');
    }

    // --- Menu, tin tức, trang tĩnh ---

    public function test_menu_header_render_nhieu_cap(): void
    {
        $menu = Menu::create(['key' => 'header', 'name' => 'Menu chính']);
        $product = Product::create(['name' => 'Lexus GX 550', 'status' => 'published']);

        $suv = $menu->items()->create(['label' => 'Dòng xe', 'url' => '/san-pham', 'sort' => 1]);
        $suv->children()->create(['label' => 'GX 550', 'target_type' => 'product', 'target_id' => $product->id]);

        $this->get('/')
            ->assertOk()
            ->assertSee('Dòng xe')
            ->assertSee('GX 550')
            ->assertSee('/san-pham/lexus-gx-550');
    }

    public function test_chua_dung_menu_thi_van_co_loi_vao_danh_sach(): void
    {
        $this->get('/')->assertOk()->assertSee(catalog_label('product.plural'));
    }

    public function test_tin_tuc_va_chuyen_muc(): void
    {
        $chuyenMuc = PostCategory::create(['name' => 'Trải nghiệm']);

        Post::create([
            'title' => 'Lái thử GX 550 xuyên Việt', 'status' => 'published', 'published_at' => now(),
            'post_category_id' => $chuyenMuc->id, 'excerpt' => 'Ba nghìn cây số.',
            'sections' => [['title' => 'Chặng một', 'type' => 'text', 'body' => 'Qua đèo Hải Vân.']],
        ]);
        Post::create(['title' => 'Bài nháp', 'status' => 'draft']);

        $this->get('/tin-tuc')
            ->assertOk()
            ->assertSee('Lái thử GX 550 xuyên Việt')
            ->assertDontSee('Bài nháp');

        $this->get('/chuyen-muc/trai-nghiem')->assertOk()->assertSee('Lái thử GX 550 xuyên Việt');

        $this->get('/tin-tuc/lai-thu-gx-550-xuyen-viet')
            ->assertOk()
            ->assertSee('Ba nghìn cây số.')
            ->assertSee('Qua đèo Hải Vân.')
            ->assertSee('Trải nghiệm');
    }

    // --- Cài đặt ăn vào layout ---

    public function test_ma_do_luong_lay_tu_cai_dat(): void
    {
        Setting::put('gtm_id', 'GTM-ABC123');

        $this->get('/')->assertOk()->assertSee('GTM-ABC123');
    }

    public function test_chua_cau_hinh_do_luong_thi_khong_nhung_script_nao(): void
    {
        $this->get('/')->assertOk()->assertDontSee('googletagmanager.com');
    }
}
