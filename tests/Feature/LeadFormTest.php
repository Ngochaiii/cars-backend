<?php

namespace Tests\Feature;

use App\Events\LeadReceived;
use App\Models\Form;
use App\Models\Lead;
use App\Models\Product;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * Form lead trên trang Blade — POST thường, không JS.
 *
 * Cùng action StoreLead với API nên honeypot, chống trùng, mail và webhook
 * phải giống hệt. Test ở đây canh cái "giống hệt" đó.
 */
class LeadFormTest extends TestCase
{
    protected Form $form;

    protected function setUp(): void
    {
        parent::setUp();

        $this->form = Form::create([
            'key'             => 'dat-lich-lai-thu',
            'name'            => 'Đặt lịch lái thử',
            'success_message' => 'Tư vấn viên sẽ gọi lại trong 15 phút.',
        ]);

        $this->form->fields()->createMany([
            ['key' => 'name', 'label' => 'Họ tên', 'type' => 'text', 'rules' => ['required'], 'sort' => 1],
            ['key' => 'phone', 'label' => 'Điện thoại', 'type' => 'tel', 'rules' => ['required'], 'sort' => 2],
        ]);
    }

    public function test_gui_form_tao_lead_va_quay_ve_kem_cau_cam_on(): void
    {
        Event::fake([LeadReceived::class]);

        $this->from('/san-pham/lexus-gx-550')
            ->post('/gui-form/dat-lich-lai-thu', ['name' => 'Đạt', 'phone' => '0987654321'])
            ->assertRedirect('/san-pham/lexus-gx-550')
            ->assertSessionHas('lead_success', 'Tư vấn viên sẽ gọi lại trong 15 phút.')
            ->assertSessionHas('lead_form_key', 'dat-lich-lai-thu');

        $lead = Lead::sole();
        $this->assertSame('Đạt', $lead->name);
        $this->assertSame('0987654321', $lead->phone);
        $this->assertSame(['name' => 'Đạt', 'phone' => '0987654321'], $lead->data);

        Event::assertDispatched(LeadReceived::class);
    }

    public function test_thieu_field_bat_buoc_thi_quay_lai_kem_loi(): void
    {
        $this->from('/')
            ->post('/gui-form/dat-lich-lai-thu', ['name' => 'Đạt'])
            ->assertRedirect('/')
            ->assertSessionHasErrors('phone');

        $this->assertSame(0, Lead::count());
    }

    public function test_bot_dien_o_bay_thi_khong_tao_lead_nhung_van_bao_thanh_cong(): void
    {
        $this->post('/gui-form/dat-lich-lai-thu', [
            'name' => 'Bot', 'phone' => '0900000000', 'website' => 'http://spam.example',
        ])->assertSessionHas('lead_success');

        $this->assertSame(0, Lead::count());
    }

    public function test_gui_hai_lan_cung_so_trong_5_phut_chi_tao_mot_lead(): void
    {
        $payload = ['name' => 'Đạt', 'phone' => '0987654321'];

        $this->post('/gui-form/dat-lich-lai-thu', $payload);
        $this->post('/gui-form/dat-lich-lai-thu', $payload)->assertSessionHas('lead_success');

        $this->assertSame(1, Lead::count());
    }

    public function test_lead_gan_dung_mat_hang_khi_form_o_trang_chi_tiet(): void
    {
        $product = Product::create(['name' => 'Lexus GX 550', 'status' => 'published']);

        $this->post('/gui-form/dat-lich-lai-thu', [
            'name' => 'Đạt', 'phone' => '0987654321', 'product_id' => $product->id,
        ]);

        $this->assertSame($product->id, Lead::sole()->product_id);
    }

    public function test_form_da_tat_thi_404(): void
    {
        $this->form->update(['is_active' => false]);

        $this->post('/gui-form/dat-lich-lai-thu', ['name' => 'Đạt', 'phone' => '0987654321'])
            ->assertNotFound();
    }

    public function test_form_hien_o_cuoi_trang_chi_tiet_theo_config(): void
    {
        Product::create(['name' => 'Lexus GX 550', 'status' => 'published']);

        $this->get('/san-pham/lexus-gx-550')
            ->assertOk()
            ->assertSee('Đặt lịch lái thử')
            ->assertSee('Họ tên')
            ->assertSee('name="_token"', false)      // CSRF
            ->assertSee('class="honeypot"', false);  // ô bẫy bot

        // Đổi config sang form không tồn tại → khối form biến mất, trang vẫn chạy
        config(['catalog.frontend.product_form' => null]);

        $this->get('/san-pham/lexus-gx-550')->assertOk()->assertDontSee('Họ tên');
    }

    public function test_form_da_nhung_giua_trang_thi_khong_dung_lai_o_cuoi(): void
    {
        Product::create([
            'name'     => 'Lexus GX 550',
            'status'   => 'published',
            'sections' => [[
                'title' => 'Đăng ký lái thử', 'type' => 'form', 'form_key' => 'dat-lich-lai-thu',
            ]],
        ]);

        $html = $this->get('/san-pham/lexus-gx-550')->assertOk()->getContent();

        // Đúng một form trên trang, không phải hai cái giống hệt nhau
        $this->assertSame(1, substr_count($html, 'class="lead-form"'));
    }
}
