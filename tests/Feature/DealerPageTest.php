<?php

namespace Tests\Feature;

use App\Filament\Resources\Dealers\DealerResource;
use App\Filament\Resources\Dealers\Pages\ManageDealers;
use App\Filament\Resources\Provinces\Pages\ManageProvinces;
use App\Models\Dealer;
use App\Models\Province;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;
use Tests\TestCase;

class DealerPageTest extends TestCase
{
    public function test_trang_dai_ly_hien_theo_tinh(): void
    {
        $bg = Province::create(['name' => 'Bắc Giang']);
        $hn = Province::create(['name' => 'Hà Nội']);

        Dealer::create([
            'name' => 'Showroom Xương Giang', 'province_id' => $bg->id,
            'address' => 'Đường Xương Giang, TP. Bắc Giang', 'phone' => '0204 123 456',
        ]);
        Dealer::create(['name' => 'Showroom Long Biên', 'province_id' => $hn->id]);

        $this->get('/he-thong-dai-ly')
            ->assertOk()
            ->assertSee('Bắc Giang')
            ->assertSee('Showroom Xương Giang')
            ->assertSee('0204 123 456')
            ->assertSee('Showroom Long Biên');
    }

    public function test_tinh_khong_co_dai_ly_thi_khong_hien(): void
    {
        Province::create(['name' => 'Tỉnh trống']);

        $this->get('/he-thong-dai-ly')->assertOk()->assertDontSee('Tỉnh trống');
    }

    /** Thiếu một toạ độ là link mở ra giữa biển — thà không có nút. */
    public function test_thieu_toa_do_thi_khong_dung_nut_chi_duong(): void
    {
        $p = Province::create(['name' => 'Bắc Giang']);
        Dealer::create(['name' => 'Chỉ có vĩ độ', 'province_id' => $p->id, 'lat' => 21.27]);
        Dealer::create(['name' => 'Đủ hai toạ độ', 'province_id' => $p->id, 'lat' => 21.27, 'lng' => 106.19]);

        $html = $this->get('/he-thong-dai-ly')->assertOk()->getContent();

        $this->assertSame(1, substr_count($html, 'Chỉ đường'));
        $this->assertStringContainsString('query=21.27,106.19', $html);
    }

    public function test_gio_mo_cua_liet_ke_tung_dong(): void
    {
        $p = Province::create(['name' => 'Bắc Giang']);
        Dealer::create([
            'name' => 'Showroom', 'province_id' => $p->id,
            'opening_hours' => ['T2–T7: 8:00–19:00', 'CN: 8:00–17:00'],
        ]);

        $this->get('/he-thong-dai-ly')
            ->assertOk()
            ->assertSee('T2–T7: 8:00–19:00')
            ->assertSee('CN: 8:00–17:00');
    }

    public function test_chua_co_dai_ly_nao_thi_bao_trong(): void
    {
        $this->get('/he-thong-dai-ly')->assertOk()->assertSee('Chưa có đại lý nào');
    }

    public function test_tat_feature_thi_route_bien_mat(): void
    {
        $this->assertTrue(Route::has('dealers'));
        $this->assertTrue(DealerResource::shouldRegisterNavigation());

        config(['catalog.features.dealers' => false]);

        $this->assertFalse(DealerResource::shouldRegisterNavigation());
    }

    public function test_man_hinh_admin_render_va_xoa_duoc(): void
    {
        $this->actingAs(User::create([
            'name' => 'Admin', 'email' => 'admin@test.local', 'password' => 'x',
        ]));

        $p = Province::create(['name' => 'Bắc Giang']);
        $d = Dealer::create(['name' => 'Sắp xoá', 'province_id' => $p->id]);

        Livewire::test(ManageDealers::class)
            ->assertSuccessful()
            ->callTableAction('delete', $d);

        $this->assertSame(0, Dealer::count());

        Livewire::test(ManageProvinces::class)->assertSuccessful();
    }
}
