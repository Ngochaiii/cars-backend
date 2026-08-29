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

    public function test_uu_dai_va_tram_sac_dung_hai_bo_cuc_khac_nhau(): void
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
}
