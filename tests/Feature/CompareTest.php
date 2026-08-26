<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Support\Str;
use Tests\TestCase;

class CompareTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Product::create([
            'name' => 'VinFast VF 7', 'status' => 'published', 'published_at' => now(),
            'price_from' => 799_000_000,
            'specs' => [['group' => 'Pin', 'rows' => [
                ['label' => 'Dung lượng pin', 'value' => '75,3 kWh'],
                ['label' => 'Quãng đường', 'value' => '496 km'],
            ]]],
        ]);

        Product::create([
            'name' => 'VinFast VF 9', 'status' => 'published', 'published_at' => now(),
            'price_from' => 1_491_000_000,
            'specs' => [['group' => 'Pin', 'rows' => [
                ['label' => 'Dung lượng pin', 'value' => '92 kWh'],
                ['label' => 'Số chỗ ngồi', 'value' => '7'],
            ]]],
        ]);
    }

    public function test_so_sanh_hai_xe_dat_thong_so_canh_nhau(): void
    {
        $this->get('/so-sanh?xe=vinfast-vf-7,vinfast-vf-9')
            ->assertOk()
            ->assertSee('VinFast VF 7')
            ->assertSee('VinFast VF 9')
            ->assertSee('75,3 kWh')
            ->assertSee('92 kWh')
            ->assertSee('Số chỗ ngồi');
    }

    /**
     * VF 7 không khai "Số chỗ ngồi" nên ô của nó phải là dấu gạch — không được
     * đẩy giá trị của xe bên cạnh sang, đó là cách bảng so sánh nói dối.
     */
    public function test_thong_so_xe_kia_khong_co_thi_de_gach_ngang(): void
    {
        $html = $this->get('/so-sanh?xe=vinfast-vf-7,vinfast-vf-9')->assertOk()->getContent();
        $body = Str::between($html, '<tbody>', '</tbody>');

        // Dòng "Số chỗ ngồi": ô VF 7 gạch, ô VF 9 là 7
        $row = Str::between($body, 'Số chỗ ngồi', '</tr>');

        $this->assertStringContainsString('—', $row);
        $this->assertStringContainsString('7', $row);
    }

    public function test_giu_dung_thu_tu_khach_go_tren_url(): void
    {
        $html = $this->get('/so-sanh?xe=vinfast-vf-9,vinfast-vf-7')->assertOk()->getContent();
        $head = Str::between($html, '<thead>', '</thead>');

        $this->assertTrue(
            strpos($head, 'VinFast VF 9') < strpos($head, 'VinFast VF 7'),
            'thứ tự cột phải theo URL, không theo thứ tự DB'
        );
    }

    public function test_chua_chon_xe_thi_hien_danh_sach_de_chon(): void
    {
        $this->get('/so-sanh')
            ->assertOk()
            ->assertSee('Chọn xe để so sánh')
            ->assertSee('VinFast VF 7');
    }

    public function test_chon_mot_xe_thi_nhac_chon_them(): void
    {
        $this->get('/so-sanh?xe=vinfast-vf-7')
            ->assertOk()
            ->assertSee('Chọn ít nhất hai xe');
    }

    public function test_chi_nhan_toi_da_ba_xe(): void
    {
        Product::create(['name' => 'VinFast VF 8', 'status' => 'published', 'published_at' => now()]);
        Product::create(['name' => 'VinFast VF 6', 'status' => 'published', 'published_at' => now()]);

        $head = Str::between(
            $this->get('/so-sanh?xe=vinfast-vf-7,vinfast-vf-9,vinfast-vf-8,vinfast-vf-6')
                ->assertOk()->getContent(),
            '<thead>', '</thead>'
        );

        $this->assertStringNotContainsString('VinFast VF 6', $head);
    }

    /** Form trên trang gửi xe[] (mảng), link chia sẻ gửi chuỗi ngăn phẩy. */
    public function test_nhan_ca_hai_dang_tham_so(): void
    {
        $this->get('/so-sanh?'.http_build_query(['xe' => ['vinfast-vf-7', 'vinfast-vf-9']]))
            ->assertOk()
            ->assertSee('75,3 kWh')
            ->assertSee('92 kWh');
    }

    public function test_khong_tra_ve_ban_nhap(): void
    {
        Product::create(['name' => 'Xe nháp', 'slug' => 'xe-nhap', 'status' => 'draft']);

        $this->get('/so-sanh?xe=vinfast-vf-7,xe-nhap')
            ->assertOk()
            ->assertSee('Chọn ít nhất hai xe');
    }
}
