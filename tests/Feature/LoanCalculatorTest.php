<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Support\Str;
use Tests\TestCase;

class LoanCalculatorTest extends TestCase
{
    protected function product(): Product
    {
        return Product::create([
            'name' => 'Lexus GX 550',
            'status' => 'published',
            'published_at' => now(),
            'price_from' => 1_000_000_000,
        ]);
    }

    public function test_hien_bo_tinh_tra_gop_voi_gia_tri_mac_dinh(): void
    {
        $this->product();

        $this->get('/san-pham/lexus-gx-550')
            ->assertOk()
            ->assertSee('Trả góp')
            ->assertSee('name="down"', false)
            ->assertSee('name="months"', false)
            ->assertSee('name="rate"', false);
    }

    public function test_nhap_so_lieu_thi_tinh_ra_ket_qua(): void
    {
        $this->product();

        // Vay 800tr, 12 tháng, 9%/năm → tháng đầu 72.666.667 đ
        $this->get('/san-pham/lexus-gx-550?down=200000000&months=12&rate=9')
            ->assertOk()
            ->assertSee('72.666.667 đ');
    }

    /** Người dùng gõ "200.000.000" cũng phải nhận, không bắt gõ đúng định dạng. */
    public function test_nhan_ca_so_co_dau_cham(): void
    {
        $this->product();

        $this->get('/san-pham/lexus-gx-550?down=200.000.000&months=12&rate=9')
            ->assertOk()
            ->assertSee('72.666.667 đ');
    }

    public function test_tat_feature_thi_khoi_do_bien_mat(): void
    {
        $this->product();
        config(['catalog.features.loan_calc' => false]);

        $this->get('/san-pham/lexus-gx-550')->assertOk()->assertDontSee('name="down"', false);
    }

    public function test_xe_chua_co_gia_thi_khong_dung_bo_tinh(): void
    {
        Product::create(['name' => 'Lexus LX 700h', 'status' => 'published', 'published_at' => now()]);

        $this->get('/san-pham/lexus-lx-700h')->assertOk()->assertDontSee('name="down"', false);
    }

    public function test_khong_can_js(): void
    {
        $this->product();

        // Form GET tính bằng PHP: bấm nút là tải lại trang kèm query string.
        $html = $this->get('/san-pham/lexus-gx-550')->assertOk()->getContent();
        $panel = Str::between($html, '<div class="loan">', '</form>');

        $this->assertStringContainsString('method="GET"', $panel);
        $this->assertStringContainsString('type="submit"', $panel);
    }
}
