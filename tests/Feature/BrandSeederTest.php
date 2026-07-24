<?php

namespace Tests\Feature;

use App\Models\Product;
use Database\Seeders\Brands\MauSeeder;
use Database\Seeders\Brands\VinFastSeeder;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Seeder theo hãng: lớp con chỉ khai dữ liệu, khung `BrandSeeder` dựng bản ghi.
 *
 * Test canh cái khung đó — đổi nó là mọi hãng đổi theo, nên phải có chỗ báo
 * động khi hình dạng dữ liệu lệch.
 */
class BrandSeederTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Ảnh placeholder ghi vào disk giả, không đụng storage thật.
        Storage::fake('public');
    }

    public function test_mau_seeder_dung_du_muc_theo_dung_thu_tu(): void
    {
        $this->seed(MauSeeder::class);

        $product = Product::where('slug', 'mau-m1')->sole();

        // Thứ tự cố định cho mọi hãng: ảnh → chữ → video → bảng → form
        $this->assertSame(
            ['media', 'media', 'text', 'video', 'table', 'form'],
            array_column($product->sections, 'type'),
        );

        $this->assertSame(
            ['Thư viện', 'Ngoại thất', 'Vận hành', 'Phim giới thiệu', 'Thời gian sạc', 'Đăng ký lái thử'],
            array_column($product->sections, 'title'),
        );

        // Mục Thư viện: chỉ quăng ảnh vào, nhãn để trống
        $thuVien = $product->sections[0];
        $this->assertSame('slider', $thuVien['layout']);
        $this->assertCount(3, $thuVien['items']);
        $this->assertSame(['', '', ''], array_column($thuVien['items'], 'label'));

        // Mục ảnh khác thì giữ nhãn người nhập
        $this->assertSame(['Đèn trước', 'Mâm xe'], array_column($product->sections[1]['items'], 'label'));

        $this->assertSame('dat-lich-lai-thu', $product->sections[5]['form_key']);
    }

    public function test_specs_va_phien_ban_va_mau_dung_hinh_dang(): void
    {
        $this->seed(MauSeeder::class);

        $product = Product::where('slug', 'mau-m1')->sole();

        $this->assertSame(['Động cơ & hiệu năng', 'Kích thước'], array_column($product->specs, 'group'));
        $this->assertSame(
            ['label' => 'Công suất tối đa', 'value' => '180 mã lực'],
            $product->specs[0]['rows'][0],
        );

        // Phiên bản đầu tiên tự thành bản mặc định
        $variants = $product->variants;
        $this->assertCount(2, $variants);
        $this->assertTrue((bool) $variants[0]->is_default);
        $this->assertFalse((bool) $variants[1]->is_default);

        $this->assertSame(['Trắng', 'Đen'], $product->options->pluck('name')->all());
    }

    public function test_anh_placeholder_duoc_sinh_va_khong_de_len_anh_that(): void
    {
        $this->seed(MauSeeder::class);

        Storage::disk('public')->assertExists('catalog/mau/mau-m1/hero.jpg');
        Storage::disk('public')->assertExists('catalog/mau/mau-m1/thu-vien-1.jpg');

        // Người dùng upload ảnh thật đè lên → chạy lại seeder không được ghi đè
        Storage::disk('public')->put('catalog/mau/mau-m1/hero.jpg', 'ảnh thật');

        $this->seed(MauSeeder::class);

        $this->assertSame('ảnh thật', Storage::disk('public')->get('catalog/mau/mau-m1/hero.jpg'));
    }

    public function test_chay_lai_khong_nhan_ban_du_lieu(): void
    {
        $this->seed(MauSeeder::class);
        $this->seed(MauSeeder::class);

        $this->assertSame(1, Product::where('slug', 'mau-m1')->count());
        $this->assertSame(2, Product::where('slug', 'mau-m1')->sole()->variants()->count());
        $this->assertSame(2, Product::where('slug', 'mau-m1')->sole()->options()->count());
    }

    public function test_vinfast_seed_du_sau_mau_xe(): void
    {
        $this->seed(VinFastSeeder::class);

        $products = Product::orderBy('sort')->pluck('name', 'slug');

        $this->assertSame([
            'vinfast-vf-3'      => 'VinFast VF 3',
            'vinfast-vf-5-plus' => 'VinFast VF 5 Plus',
            'vinfast-vf-6'      => 'VinFast VF 6',
            'vinfast-vf-7'      => 'VinFast VF 7',
            'vinfast-vf-8'      => 'VinFast VF 8',
            'vinfast-vf-9'      => 'VinFast VF 9',
        ], $products->all());

        // Mọi xe đều publish và có danh mục, không sót cái nào ở nháp
        $this->assertSame(0, Product::where('status', '!=', 'published')->count());
        $this->assertSame(0, Product::whereNull('category_id')->count());
    }

    public function test_seed_ca_bo_chay_duoc_du_model_event_bi_tat(): void
    {
        // DatabaseSeeder dùng WithoutModelEvents → hook `saving` của MenuItem
        // (tự thừa kế menu_id từ cha) KHÔNG chạy, mà cột đó NOT NULL.
        // Seeder phải tự gán, không được dựa vào event.
        $this->seed(\Database\Seeders\DatabaseSeeder::class);

        $menu = \App\Models\Menu::where('key', 'header')->sole();
        $children = \App\Models\MenuItem::whereNotNull('parent_id')->get();

        $this->assertCount(6, $children);
        $this->assertSame([$menu->id], $children->pluck('menu_id')->unique()->all());
    }

    public function test_trang_xe_vinfast_render_duoc(): void
    {
        $this->seed(VinFastSeeder::class);

        $this->get('/san-pham/vinfast-vf-8')
            ->assertOk()
            ->assertSee('VinFast VF 8')
            ->assertSee('1.109.000.000 đ')   // giá từ
            ->assertSee('VF 8 Plus')
            ->assertSee('Dung lượng pin')
            ->assertSee('82 kWh');
    }
}
