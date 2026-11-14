<?php

namespace Tests\Feature;

use App\Models\Form;
use App\Models\Lead;
use App\Models\Product;
use Tests\TestCase;

/**
 * Ô "Nhận báo giá lăn bánh" ngay dưới giá ở đầu trang xe: chỉ tên + số điện
 * thoại, gửi vào form có sẵn (khoá khai ở config) để khách từ quảng cáo không
 * phải bấm sang trang khác mới để lại số được.
 */
class QuickQuoteTest extends TestCase
{
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $form = Form::create(['key' => 'dat-coc', 'name' => 'Báo giá']);
        $form->fields()->createMany([
            ['key' => 'name', 'label' => 'Họ và tên', 'type' => 'text', 'rules' => ['required'], 'sort' => 1],
            ['key' => 'phone', 'label' => 'Số điện thoại', 'type' => 'tel', 'rules' => ['required'], 'sort' => 2],
            ['key' => 'note', 'label' => 'Ghi chú', 'type' => 'textarea', 'rules' => ['nullable'], 'sort' => 3],
        ]);

        $this->product = Product::create([
            'name' => 'VinFast VF 6', 'slug' => 'vinfast-vf-6', 'status' => 'published', 'price_from' => 646_000_000,
        ]);

        config(['catalog.frontend.quick_quote_form' => 'dat-coc']);
    }

    public function test_trang_xe_co_o_bao_gia_nhanh_chi_ten_va_so_dien_thoai(): void
    {
        $html = $this->get('/san-pham/vinfast-vf-6')->assertOk()->getContent();

        $box = $this->quickQuote($html);

        $this->assertStringContainsString('action="'.url('/gui-form/dat-coc').'"', $box);
        $this->assertStringContainsString('data-lead-form', $box);
        $this->assertStringContainsString('name="product_id" value="'.$this->product->id.'"', $box);
        $this->assertStringContainsString('name="name"', $box);
        $this->assertStringContainsString('name="phone"', $box);
        // Ô phụ của form gốc (ghi chú…) không kéo lên đầu trang — càng ít ô càng dễ gửi.
        $this->assertStringNotContainsString('name="note"', $box);
    }

    public function test_tat_trong_config_thi_khong_hien(): void
    {
        config(['catalog.frontend.quick_quote_form' => null]);

        $this->get('/san-pham/vinfast-vf-6')
            ->assertOk()
            ->assertDontSee('data-quick-quote', false);
    }

    public function test_form_da_tat_thi_khong_hien(): void
    {
        Form::where('key', 'dat-coc')->update(['is_active' => false]);

        $this->get('/san-pham/vinfast-vf-6')
            ->assertOk()
            ->assertDontSee('data-quick-quote', false);
    }

    public function test_khach_tu_google_ads_duoc_gan_nguon_google_cpc(): void
    {
        $box = $this->quickQuote($this->get('/san-pham/vinfast-vf-6?gclid=abc123')->getContent());

        $this->assertStringContainsString('name="utm_source" value="google"', $box);
        $this->assertStringContainsString('name="utm_medium" value="cpc"', $box);
    }

    public function test_utm_tren_duong_dan_duoc_giu_nguyen(): void
    {
        $box = $this->quickQuote($this->get('/san-pham/vinfast-vf-6?utm_source=facebook&utm_campaign=vf6-thang-11')->getContent());

        $this->assertStringContainsString('name="utm_source" value="facebook"', $box);
        $this->assertStringContainsString('name="utm_campaign" value="vf6-thang-11"', $box);
        $this->assertStringNotContainsString('name="utm_medium"', $box);
    }

    public function test_khach_vao_thang_thi_khong_co_utm(): void
    {
        $box = $this->quickQuote($this->get('/san-pham/vinfast-vf-6')->getContent());

        $this->assertStringNotContainsString('utm_', $box);
    }

    public function test_gui_o_bao_gia_nhanh_luu_lead_kem_xe_va_nguon(): void
    {
        $this->postJson('/gui-form/dat-coc', [
            'name' => 'Anh Tuấn',
            'phone' => '0987654321',
            'product_id' => $this->product->id,
            'utm_source' => 'google',
            'utm_medium' => 'cpc',
        ])->assertCreated();

        $lead = Lead::sole();
        $this->assertSame($this->product->id, $lead->product_id);
        $this->assertSame(['utm_source' => 'google', 'utm_medium' => 'cpc'], $lead->utm);
    }

    private function quickQuote(string $html): string
    {
        $this->assertSame(1, preg_match('/<div[^>]*data-quick-quote.*?<\/form>/s', $html, $m), 'Không thấy ô báo giá nhanh');

        return $m[0];
    }
}
