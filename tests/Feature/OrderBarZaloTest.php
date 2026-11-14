<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Setting;
use Tests\TestCase;

/**
 * Thanh dính đáy trang xe có nút Zalo cạnh nút gọi: khách mua xe hay nhắn Zalo
 * hơn điền form, và trên điện thoại thanh này là chỗ dễ bấm nhất.
 */
class OrderBarZaloTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Product::create(['name' => 'VinFast VF 6', 'slug' => 'vinfast-vf-6', 'status' => 'published']);
    }

    public function test_thanh_day_trang_xe_co_nut_zalo_khi_da_khai_so_zalo(): void
    {
        Setting::put('zalo', '0769 382 555');

        $bar = $this->orderBar();

        $this->assertStringContainsString('class="btn btn--outline order-bar__zalo"', $bar);
        $this->assertStringContainsString('href="https://zalo.me/0769382555"', $bar);
    }

    public function test_so_zalo_la_duong_dan_day_du_thi_giu_nguyen(): void
    {
        Setting::put('zalo', 'https://zalo.me/g/abc123');

        $this->assertStringContainsString('href="https://zalo.me/g/abc123"', $this->orderBar());
    }

    public function test_chua_khai_zalo_thi_khong_co_nut(): void
    {
        $this->assertStringNotContainsString('order-bar__zalo', $this->orderBar());
    }

    private function orderBar(): string
    {
        $html = $this->get('/san-pham/vinfast-vf-6')->assertOk()->getContent();

        $this->assertSame(1, preg_match('/<div class="order-bar__actions">.*?<\/div>/s', $html, $m), 'Không thấy thanh dính đáy');

        return $m[0];
    }
}
