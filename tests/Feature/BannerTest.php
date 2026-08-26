<?php

namespace Tests\Feature;

use App\Filament\Resources\Banners\Pages\ManageBanners;
use App\Models\Banner;
use App\Models\Product;
use App\Models\User;
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

    public function test_man_hinh_admin_banner_render_duoc(): void
    {
        $this->actingAs(User::create([
            'name' => 'Admin', 'email' => 'admin@test.local', 'password' => 'x',
        ]));

        Livewire::test(ManageBanners::class)->assertSuccessful();
    }
}
