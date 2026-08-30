<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Setting;
use Tests\TestCase;

class HomeBandDesignTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Setting::put('offer_note', 'Ưu đãi trong tháng');
        Setting::put('offer_title', 'Trả góp 0% lãi suất 24 tháng');
        Setting::put('offer_text', 'Chương trình dành cho các dòng xe điện.');
        Setting::put('charging_note', 'Pin & trạm sạc');
        Setting::put('charging_title', 'Sạc đầy trong lúc bạn đi chợ');
        Setting::put('charging_text', 'Mạng lưới điểm sạc phủ khắp tỉnh.');
        Setting::put('charging_image', 'catalog/settings/charging.jpg');
    }

    public function test_bang_tram_sac_gan_cong_cu_tim_kiem_ben_canh_anh(): void
    {
        Setting::put('stations', implode("\n", [
            'Trạm Vincom|Còn 6/8 cổng|DC 150 kW · Mở 24/7|ok|21.27310,106.19460',
            'Trạm Bến xe|Còn 1/10 cổng|DC 250 kW|warn',
        ]));

        $this->get('/')
            ->assertOk()
            ->assertSee('home-charge__stage--finder', false)
            ->assertSee('home-charge__media', false)
            ->assertSee('data-finder', false)
            ->assertSee('Trạm Vincom')
            // Chỉ đường vẫn chạy khi tắt JS: link Google Maps render sẵn,
            // có toạ độ thì trỏ thẳng toạ độ.
            ->assertSee('https://www.google.com/maps/dir/?api=1&amp;destination=21.2731%2C106.1946', false)
            // Chưa nối API thì để trống endpoint, JS lọc tại chỗ.
            ->assertSee('data-endpoint=""', false)
            ->assertDontSee('home-charge__rail', false);
    }

    public function test_chua_co_tram_nao_thi_quay_ve_dai_loi_tat(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('home-charge__rail', false)
            ->assertSee('Tiện ích sạc')
            ->assertDontSee('data-finder', false);
    }

    public function test_uu_dai_va_tram_sac_dung_hai_bo_cuc_khac_nhau(): void
    {
        Product::create([
            'name' => 'VF 7',
            'slug' => 'vf-7',
            'status' => 'published',
            'published_at' => now(),
            'hero' => ['type' => 'image', 'src' => 'catalog/hero/vf-7.jpg'],
        ]);
        Setting::put('offer_image', 'catalog/settings/uu-dai.jpg');

        $this->get('/')
            ->assertOk()
            ->assertSee('offer__chapter', false)
            ->assertSee('offer__media', false)
            ->assertSee('home-charge__head', false)
            ->assertSee('home-charge__stage', false)
            ->assertSee('home-charge__rail', false)
            ->assertSee('Tiện ích sạc')
            ->assertDontSee('home-feature--charge', false);
    }

    public function test_uu_dai_khong_co_anh_khong_de_lai_cot_trong(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('offer--text-only', false);
    }

    /* Ảnh băng ưu đãi là khoá Cài đặt riêng. Trước đây nó mượn ảnh hero của mặt
       hàng đầu danh sách, nên đại lý đổi ảnh xe là băng ưu đãi đổi theo. */
    public function test_uu_dai_dung_anh_rieng_chu_khong_muon_anh_cua_xe(): void
    {
        Product::create([
            'name' => 'VF 7',
            'slug' => 'vf-7',
            'status' => 'published',
            'published_at' => now(),
            'hero' => ['type' => 'image', 'src' => 'catalog/hero/vf-7.jpg'],
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('offer--text-only', false);

        Setting::put('offer_image', 'catalog/settings/uu-dai.jpg');

        $this->get('/')
            ->assertOk()
            ->assertSee('catalog/settings/uu-dai.jpg', false)
            ->assertDontSee('offer--text-only', false);
    }
}
