<?php

namespace Tests\Feature;

use App\Models\Form;
use App\Models\Setting;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Popup thu lead. Chỉ dựng khi Cài đặt trỏ tới một form ĐANG BẬT — khai thiếu
 * thì trang chủ không được dính khối rỗng nào.
 */
class PopupTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $form = Form::create(['key' => 'nhan-tu-van', 'name' => 'Nhận tư vấn']);
        $form->fields()->create([
            'key' => 'phone', 'label' => 'Số điện thoại', 'type' => 'tel',
            'rules' => ['required'], 'sort' => 1,
        ]);
    }

    protected function bat(): void
    {
        Setting::put('popup_form', 'nhan-tu-van');
        Setting::put('popup_title', 'Nhận báo giá lăn bánh');
        Setting::put('popup_delay', '8');
        Setting::put('popup_days', '7');
    }

    public function test_khai_du_thi_popup_hien_o_trang_chu(): void
    {
        $this->bat();

        $this->get('/')
            ->assertOk()
            ->assertSee('Nhận báo giá lăn bánh')
            ->assertSee('data-popup-delay="8"', false)
            ->assertSee('data-popup-days="7"', false);
    }

    public function test_chua_khai_form_thi_khong_co_popup(): void
    {
        $this->get('/')->assertOk()->assertDontSee('data-popup', false);
    }

    public function test_form_da_tat_thi_khong_co_popup(): void
    {
        $this->bat();
        Form::where('key', 'nhan-tu-van')->update(['is_active' => false]);

        $this->get('/')->assertOk()->assertDontSee('data-popup', false);
    }

    public function test_khai_khoa_form_khong_ton_tai_thi_khong_no(): void
    {
        Setting::put('popup_form', 'khong-co-that');

        $this->get('/')->assertOk()->assertDontSee('data-popup', false);
    }

    /** Mặc định chỉ trang chủ; bật `popup_everywhere` thì mọi trang. */
    public function test_mac_dinh_chi_hien_o_trang_chu(): void
    {
        $this->bat();

        $this->get('/tin-tuc')->assertOk()->assertDontSee('data-popup', false);

        Setting::put('popup_everywhere', '1');

        $this->get('/tin-tuc')->assertOk()->assertSee('data-popup', false);
    }

    /**
     * Server luôn render kèm `hidden`; chỉ JS mới mở. Tắt JS là popup không
     * bao giờ chắn đường người đọc.
     */
    public function test_server_luon_render_kem_hidden(): void
    {
        $this->bat();

        $html = $this->get('/')->assertOk()->getContent();
        $popup = Str::between($html, '<div class="popup"', '</div>');

        $this->assertStringContainsString('hidden', $popup);
    }

    public function test_vua_gui_form_xong_thi_khong_chao_lai(): void
    {
        $this->bat();

        $this->withSession(['lead_form_key' => 'nhan-tu-van'])
            ->get('/')
            ->assertOk()
            ->assertDontSee('data-popup', false);
    }
}
