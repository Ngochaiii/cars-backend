<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Product;
use Tests\TestCase;

class SearchTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Product::create([
            'name' => 'VinFast VF 7', 'status' => 'published', 'published_at' => now(),
            'tagline' => 'SUV điện cỡ C',
        ]);
        Product::create(['name' => 'VinFast VF 9', 'status' => 'published', 'published_at' => now()]);
        Product::create(['name' => 'Xe nháp', 'status' => 'draft']);

        Post::create([
            'title' => 'So sánh VF 7 và VF 9', 'status' => 'published', 'published_at' => now(),
        ]);
    }

    public function test_tim_thay_xe_va_bai_viet_theo_tu_khoa(): void
    {
        $this->get('/tim-kiem?q=VF+7')
            ->assertOk()
            ->assertSee('VinFast VF 7')
            ->assertSee('So sánh VF 7 và VF 9')
            ->assertDontSee('VinFast VF 9');
    }

    public function test_tim_theo_tagline(): void
    {
        // Khoảng trắng phải mã hoá, không thì URL vỡ trước khi tới controller.
        $this->get('/tim-kiem?'.http_build_query(['q' => 'SUV điện']))
            ->assertOk()
            ->assertSee('VinFast VF 7');
    }

    public function test_khong_tra_ve_ban_nhap(): void
    {
        $this->get('/tim-kiem?q=nháp')->assertOk()->assertDontSee('Xe nháp');
    }

    /** Vừa vào trang đã bị báo "không tìm thấy" là vô lý. */
    public function test_chua_go_gi_thi_khong_bao_khong_tim_thay(): void
    {
        $this->get('/tim-kiem')->assertOk()->assertDontSee('Không tìm thấy');
    }

    public function test_go_tu_khoa_la_thi_bao_khong_tim_thay(): void
    {
        $this->get('/tim-kiem?q=zzzkhongcogi')->assertOk()->assertSee('Không tìm thấy');
    }

    public function test_o_tim_kiem_co_o_header(): void
    {
        $this->get('/')->assertOk()->assertSee('header__search', false);
    }

    public function test_tat_tin_tuc_thi_chi_tim_mat_hang(): void
    {
        config(['catalog.features.posts' => false]);

        $this->get('/tim-kiem?q=VF+7')
            ->assertOk()
            ->assertSee('VinFast VF 7')
            ->assertDontSee('So sánh VF 7 và VF 9');
    }
}
