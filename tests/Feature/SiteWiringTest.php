<?php

namespace Tests\Feature;

use Catalog\Core\Filament\Resources\Products\ProductResource;
use Catalog\Core\Support\Catalog;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Test của core nằm trong packages/catalog-core và chạy độc lập bằng
 * testbench. Site chỉ còn phải chứng minh đúng một việc: nó cắm core vào
 * đúng cách và config riêng của nó có hiệu lực.
 */
class SiteWiringTest extends TestCase
{
    use RefreshDatabase;

    public function test_config_rieng_cua_site_de_len_config_mac_dinh_cua_core(): void
    {
        $this->assertSame('Xe', Catalog::label('product.single'));
        $this->assertSame('Dòng xe', Catalog::label('product.plural'));
        $this->assertSame('Màu xe', Catalog::label('option.single'));
        $this->assertSame('Chi tiết xe', Catalog::label('sections'));
    }

    public function test_site_nay_bat_tinh_nang_dac_thu_o_to(): void
    {
        $this->assertTrue(Catalog::feature('dealers'));
        $this->assertTrue(Catalog::feature('fee_calc'));

        // Route chỉ tồn tại khi feature bật
        $this->assertTrue(app('router')->has('catalog.dealers.index'));
        $this->assertTrue(app('router')->has('catalog.fee-calculator'));
    }

    public function test_goi_y_ten_muc_la_cua_nganh_o_to(): void
    {
        $this->assertSame(
            ['Thư viện', 'Ngoại thất', 'Nội thất', 'Mâm xe', 'Vận hành'],
            Catalog::sectionPresets(),
        );
    }

    public function test_panel_admin_da_nap_resource_cua_core(): void
    {
        $resources = Filament::getPanel('admin')->getResources();

        $this->assertContains(ProductResource::class, $resources);
    }

    public function test_api_cua_core_duoc_gan_vao_site(): void
    {
        $this->getJson('/api/v1/products')->assertOk()->assertJsonStructure(['data']);
    }
}
