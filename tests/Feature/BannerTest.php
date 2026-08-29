<?php

namespace Tests\Feature;

use App\Filament\Resources\Banners\BannerResource;
use App\Filament\Resources\Banners\Pages\ManageBanners;
use App\Models\Banner;
use App\Models\Product;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Banner hero trang chủ. Chưa khai banner nào thì hero lùi về dùng ảnh xe như
 * cũ — site đang chạy không được vỡ chỉ vì bảng mới còn rỗng.
 */
class BannerTest extends TestCase
{
    public function test_banner_dang_bat_thi_hien_o_trang_chu(): void
    {
        Banner::create([
            'title' => 'Trả góp 0% lãi suất 24 tháng',
            'subtitle' => 'Áp dụng đến hết tháng 8',
            'cta_label' => 'Xem chương trình',
            'cta_url' => '/tin-tuc',
            'is_active' => true,
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('Trả góp 0% lãi suất 24 tháng')
            ->assertSee('Xem chương trình');
    }

    public function test_banner_tat_hoac_het_han_thi_khong_hien(): void
    {
        Banner::create(['title' => 'Banner đã tắt', 'is_active' => false]);
        Banner::create([
            'title' => 'Banner hết hạn',
            'is_active' => true,
            'ends_at' => now()->subDay(),
        ]);
        Banner::create([
            'title' => 'Banner chưa tới ngày',
            'is_active' => true,
            'starts_at' => now()->addDay(),
        ]);

        $this->get('/')
            ->assertOk()
            ->assertDontSee('Banner đã tắt')
            ->assertDontSee('Banner hết hạn')
            ->assertDontSee('Banner chưa tới ngày');
    }

    public function test_chua_co_banner_thi_hero_lui_ve_dung_xe(): void
    {
        Product::create([
            'name' => 'Lexus GX 550',
            'status' => 'published',
            'published_at' => now(),
            'tagline' => 'Bản lĩnh chinh phục',
        ]);

        $this->get('/')->assertOk()->assertSee('Bản lĩnh chinh phục');
    }

    public function test_nhan_khong_kem_link_thi_khong_dung_nut(): void
    {
        Banner::create([
            'title' => 'Banner không link',
            'cta_label' => 'Nút chết',
            'is_active' => true,
        ]);

        $this->get('/')->assertOk()->assertDontSee('Nút chết');
    }

    public function test_banner_khong_anh_van_du_tuong_phan_nut(): void
    {
        Banner::create([
            'title' => 'Banner nền tối',
            'cta_label' => 'Bấm vào đây',
            'cta_url' => '/tin-tuc',
            'is_active' => true,
        ]);

        // Nền carousel tối kể cả khi không có ảnh, nên nút phải dùng biến thể
        // cho nền tối. Dùng btn--outline là chữ đen trên nền đen.
        $html = $this->get('/')->assertOk()->getContent();
        $hero = Str::between($html, 'hero--carousel', '</section>');

        $this->assertStringContainsString('btn btn--light', $hero);
        $this->assertStringContainsString('btn btn--ghost', $hero);
        $this->assertStringNotContainsString('btn--outline', $hero);
    }

    /**
     * Banner chỉ có ảnh: hiện đúng tấm ảnh, không đè tiêu đề hay nút của site
     * lên — ảnh loại này thường đã thiết kế sẵn chữ bên trong.
     */
    public function test_banner_chi_co_anh_thi_chi_hien_anh(): void
    {
        Banner::create([
            'image' => 'catalog/banners/khuyen-mai.jpg',
            'cta_url' => '/tin-tuc',
            'cta_label' => 'Ưu đãi tháng 8',
            'is_active' => true,
        ]);

        $html = $this->get('/')->assertOk()->getContent();
        $hero = Str::between($html, 'hero--carousel', '</section>');

        // Cả tấm ảnh là vùng bấm
        $this->assertStringContainsString('hero__slide--bare', $hero);
        $this->assertStringContainsString('hero__bare-link', $hero);
        $this->assertStringContainsString('href="/tin-tuc"', $hero);

        // Không dựng khối chữ, không nút nào
        $this->assertStringNotContainsString('hero__body', $hero);
        $this->assertStringNotContainsString('Xem tất cả', $hero);

        // Link chỉ chứa ảnh nên phải có chữ mô tả đích đến
        $this->assertStringContainsString('Ưu đãi tháng 8', $hero);

        // Nút chuyển slide cũng phải có tên, kể cả banner không có title.
        $this->assertStringContainsString('<span class="sr-only">', $hero);
        $this->assertSame(2, substr_count($hero, 'Ưu đãi tháng 8'));
    }

    public function test_banner_chi_co_anh_va_khong_link_thi_khong_dung_the_a(): void
    {
        Banner::create([
            'image' => 'catalog/banners/anh.jpg',
            'is_active' => true,
        ]);

        $hero = Str::between(
            $this->get('/')->assertOk()->getContent(), 'hero--carousel', '</section>'
        );

        $this->assertStringContainsString('hero__slide--bare', $hero);
        $this->assertStringNotContainsString('hero__bare-link', $hero);
    }

    public function test_banner_co_anh_mobile_thi_dung_picture_source(): void
    {
        Banner::create([
            'image' => 'catalog/banners/desktop.jpg',
            'image_mobile' => 'catalog/banners/mobile.jpg',
            'is_active' => true,
        ]);

        $hero = Str::between(
            $this->get('/')->assertOk()->getContent(), 'hero--carousel', '</section>'
        );

        $this->assertStringContainsString('<picture>', $hero);
        $this->assertStringContainsString('media="(max-width: 680px)"', $hero);
        $this->assertStringContainsString('catalog/banners/mobile.jpg', $hero);
        $this->assertStringContainsString('catalog/banners/desktop.jpg', $hero);
    }

    public function test_them_tieu_de_thi_hien_day_du_nhu_cu(): void
    {
        Banner::create([
            'image' => 'catalog/banners/anh.jpg',
            'title' => 'Trả góp 0%',
            'subtitle' => 'Đến hết tháng 8',
            'cta_label' => 'Xem ngay',
            'cta_url' => '/tin-tuc',
            'is_active' => true,
        ]);

        $hero = Str::between(
            $this->get('/')->assertOk()->getContent(), 'hero--carousel', '</section>'
        );

        $this->assertStringNotContainsString('hero__slide--bare', $hero);
        $this->assertStringContainsString('hero__body', $hero);
        $this->assertStringContainsString('Trả góp 0%', $hero);
        $this->assertStringContainsString('Xem ngay', $hero);
        $this->assertStringContainsString('Xem tất cả', $hero);
    }

    /** Trộn hai loại trong cùng một băng chuyền: mỗi slide theo kiểu của nó. */
    public function test_tron_banner_chi_anh_va_banner_day_du(): void
    {
        Banner::create([
            'image' => 'catalog/banners/a.jpg',
            'cta_url' => '/tin-tuc',
            'cta_label' => 'Ảnh khuyến mãi',
            'sort' => 1,
            'is_active' => true,
        ]);
        Banner::create([
            'title' => 'Banner có chữ',
            'subtitle' => 'Mô tả',
            'sort' => 2,
            'is_active' => true,
        ]);

        $hero = Str::between(
            $this->get('/')->assertOk()->getContent(), 'hero--carousel', '</section>'
        );

        $this->assertSame(1, substr_count($hero, 'hero__slide--bare'), 'chỉ slide ảnh mới được ở chế độ trần');
        $this->assertSame(1, substr_count($hero, 'hero__body'), 'chỉ slide có chữ mới dựng khối chữ');
        $this->assertStringContainsString('Banner có chữ', $hero);
    }

    public function test_khong_anh_khong_chu_thi_khong_hien(): void
    {
        Banner::create(['is_active' => true]);

        $this->assertSame(0, Banner::active()->count());
    }

    public function test_man_hinh_admin_banner_render_duoc(): void
    {
        $this->actingAs(User::create([
            'name' => 'Admin', 'email' => 'admin@test.local', 'password' => 'x',
        ]));

        Livewire::test(ManageBanners::class)->assertSuccessful();
    }

    /**
     * Filament 4 không tự thêm nút sửa/xoá vào bảng nữa — không khai
     * recordActions thì bảng chỉ để ngắm: tạo được bản ghi rồi chịu.
     */
    public function test_sua_va_xoa_duoc_banner_tu_bang(): void
    {
        $this->actingAs(User::create([
            'name' => 'Admin', 'email' => 'admin@test.local', 'password' => 'x',
        ]));

        $banner = Banner::create(['title' => 'Banner sắp xoá', 'is_active' => true]);

        Livewire::test(ManageBanners::class)
            ->assertTableActionExists('edit')
            ->assertTableActionExists('delete')
            ->callTableAction('delete', $banner);

        $this->assertSame(0, Banner::count());
    }

    /**
     * Livewire::test() gọi thẳng vào class nên component render được KHÔNG có
     * nghĩa là admin thấy nó: panel này đăng ký resource tường minh, quên thêm
     * vào danh sách là menu không có mục nào cả. Đúng lỗi đã xảy ra một lần.
     */
    public function test_banner_co_mat_trong_menu_admin(): void
    {
        $resources = Filament::getPanel('admin')->getResources();

        $this->assertContains(
            BannerResource::class,
            $resources,
            'BannerResource chưa được đăng ký trong AdminPanelProvider'
        );
    }

    public function test_tat_feature_thi_admin_khong_lo_muc_banner(): void
    {
        $this->assertTrue(BannerResource::shouldRegisterNavigation());

        config(['catalog.features.banners' => false]);

        $this->assertFalse(BannerResource::shouldRegisterNavigation());
    }
}
