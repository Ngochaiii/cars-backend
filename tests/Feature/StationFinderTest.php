<?php

namespace Tests\Feature;

use App\Models\Setting;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * Công cụ tìm trạm sạc (partials/station-finder) — dùng chung cho băng trạm
 * sạc ngoài trang chủ và trang Trạm sạc & dịch vụ.
 */
class StationFinderTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Setting::put('charging_title', 'Sạc đầy trong lúc bạn đi chợ');
        Setting::put('service_title', 'Sạc và bảo dưỡng, ngay trong tỉnh');
    }

    public function test_trang_tram_sac_dung_chung_panel_voi_trang_chu(): void
    {
        if (! Route::has('services')) {
            $this->markTestSkipped('Trang Trạm sạc & dịch vụ đang tắt trong config.');
        }

        Setting::put('stations', 'Trạm Vincom|Còn 6/8 cổng|DC 150 kW|ok|21.27310,106.19460');

        $this->get(route('services'))
            ->assertOk()
            ->assertSee('data-finder', false)
            ->assertSee('Trạm Vincom')
            ->assertSee('https://www.google.com/maps/dir/?api=1&amp;destination=21.2731%2C106.1946', false)
            // Danh sách chỉ đọc cũ đã nhường chỗ cho công cụ.
            ->assertDontSee('stations__list', false);
    }

    public function test_khong_co_toa_do_thi_chi_duong_bang_ten_va_dia_chi(): void
    {
        Setting::put('stations', 'Trạm Bến xe|Còn 1/10 cổng|DC 250 kW|warn|Đường Xương Giang, TP. Bắc Giang');

        $this->get('/')
            ->assertOk()
            ->assertSee('destination=Tr%E1%BA%A1m%20B%E1%BA%BFn%20xe%20%C4%90%C6%B0%E1%BB%9Dng%20X%C6%B0%C6%A1ng%20Giang', false)
            ->assertSee('finder__badge is-warn', false);
    }

    public function test_khoa_stations_api_thanh_endpoint_cho_js(): void
    {
        Setting::put('stations', 'Trạm Vincom|Còn 6/8 cổng|DC 150 kW|ok');
        Setting::put('stations_api', 'https://api.example.test/tram-sac');

        $this->get('/')
            ->assertOk()
            ->assertSee('data-endpoint="https://api.example.test/tram-sac"', false);
    }

    public function test_ten_tram_khong_thoat_ra_khoi_the_script(): void
    {
        Setting::put('stations', 'Trạm </script><img src=x>|Còn 2/4 cổng|DC 60 kW|ok');

        $html = $this->get('/')->assertOk()->getContent();

        $this->assertStringNotContainsString('</script><img src=x>', $html);
        $this->assertStringContainsString('\u003C\/script\u003E', $html);
    }
}
