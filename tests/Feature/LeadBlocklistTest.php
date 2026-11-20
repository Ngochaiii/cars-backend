<?php

namespace Tests\Feature;

use App\Models\Form;
use App\Models\Lead;
use App\Models\Setting;
use Tests\TestCase;

/**
 * Cài đặt → Chặn spam: số/email trong danh sách gửi form vẫn thấy "đã gửi"
 * nhưng không có lead, và JSON trả id rỗng để frontend.js không bắn chuyển đổi.
 */
class LeadBlocklistTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $quote = Form::create(['key' => 'dat-coc', 'name' => 'Báo giá']);
        $quote->fields()->createMany([
            ['key' => 'name', 'label' => 'Họ tên', 'type' => 'text', 'rules' => ['required'], 'sort' => 1],
            ['key' => 'phone', 'label' => 'Điện thoại', 'type' => 'tel', 'rules' => ['required'], 'sort' => 2],
        ]);

        $news = Form::create(['key' => 'dang-ky-nhan-tin', 'name' => 'Nhận tin']);
        $news->fields()->create(['key' => 'email', 'label' => 'Email', 'type' => 'email', 'rules' => ['required', 'email'], 'sort' => 1]);

        Setting::put('blocked_contacts', "090 111 2233\nSpamMer@example.com\n");
    }

    public function test_so_bi_chan_khong_tao_lead_nhung_van_bao_da_gui(): void
    {
        $this->postJson('/gui-form/dat-coc', ['name' => 'Người phá', 'phone' => '+84901112233'])
            ->assertCreated()
            ->assertJsonPath('data.id', null);

        $this->post('/gui-form/dat-coc', ['name' => 'Người phá', 'phone' => '0901112233'])
            ->assertSessionHas('lead_success');

        $this->assertSame(0, Lead::count());
    }

    public function test_email_bi_chan_khong_phan_biet_hoa_thuong(): void
    {
        $this->postJson('/gui-form/dang-ky-nhan-tin', ['email' => 'spammer@example.com'])
            ->assertCreated()
            ->assertJsonPath('data.id', null);

        $this->assertSame(0, Lead::count());
    }

    public function test_so_khac_van_tao_lead_binh_thuong(): void
    {
        $this->postJson('/gui-form/dat-coc', ['name' => 'Khách thật', 'phone' => '0987654321'])
            ->assertCreated();

        $this->assertSame(1, Lead::count());
    }

    public function test_danh_sach_trong_thi_khong_chan_ai(): void
    {
        Setting::put('blocked_contacts', null);

        $this->postJson('/gui-form/dat-coc', ['name' => 'Người phá', 'phone' => '0901112233'])
            ->assertCreated();

        $this->assertSame(1, Lead::count());
    }
}
