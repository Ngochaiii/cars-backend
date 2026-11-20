<?php

namespace Tests\Feature;

use App\Filament\Resources\ContactClicks\Pages\ManageContactClicks;
use App\Models\ContactClick;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Ghi lại từng lượt bấm nút gọi / Zalo trên web.
 *
 * Google Ads chỉ đếm "có một lượt bấm", không cho biết khách là ai, vào từ
 * quảng cáo nào, đã xem những trang gì. Bảng này trả lời mấy câu đó để tư vấn
 * viên phân biệt khách quan tâm thật với người bấm thử.
 */
class ContactClickTest extends TestCase
{
    protected Product $vf5;

    protected function setUp(): void
    {
        parent::setUp();

        Http::fake(['ip-api.com/*' => Http::response(['status' => 'fail'])]);

        $this->vf5 = Product::create(['name' => 'VinFast VF 5 Plus', 'slug' => 'vinfast-vf-5-plus', 'status' => 'published']);
    }

    public function test_khach_tu_quang_cao_bam_zalo_thi_ghi_du_nguon_va_hanh_trinh(): void
    {
        $this->get('/san-pham/vinfast-vf-5-plus?gad_source=1&gad_campaignid=24450242141&gclid=Cj0KCQabc')->assertOk();
        $this->get('/san-pham')->assertOk();

        $this->post('/bam-lien-he', ['channel' => 'zalo', 'page' => '/san-pham/vinfast-vf-5-plus'])
            ->assertNoContent();

        $click = ContactClick::sole();

        $this->assertSame('zalo', $click->channel);
        $this->assertSame('/san-pham/vinfast-vf-5-plus', $click->page);
        $this->assertTrue($click->product->is($this->vf5));
        $this->assertSame('24450242141', $click->source['gad_campaignid']);
        $this->assertTrue($click->source['gclid']);
        $this->assertSame('/san-pham/vinfast-vf-5-plus', $click->source['landing']);
        $this->assertSame(['/san-pham/vinfast-vf-5-plus', '/san-pham'], array_column($click->trail, 'p'));
        $this->assertSame('Google Ads', $click->sourceLabel());
    }

    public function test_khach_tu_utm_thi_nguon_la_utm_source(): void
    {
        $this->get('/san-pham?utm_source=facebook&utm_medium=post')->assertOk();

        $this->post('/bam-lien-he', ['channel' => 'phone', 'page' => '/san-pham'])->assertNoContent();

        $click = ContactClick::sole();
        $this->assertSame('facebook', $click->source['utm_source']);
        $this->assertSame('facebook', $click->sourceLabel());
        $this->assertNull($click->product_id);
    }

    public function test_vao_thang_khong_qua_quang_cao_thi_khong_co_nguon(): void
    {
        $this->get('/san-pham')->assertOk();

        $this->post('/bam-lien-he', ['channel' => 'phone', 'page' => '/san-pham'])->assertNoContent();

        $click = ContactClick::sole();
        $this->assertNull($click->source);
        $this->assertSame('Vào thẳng / khác', $click->sourceLabel());
    }

    public function test_nguon_quang_cao_giu_nguyen_khi_khach_xem_trang_khac(): void
    {
        $this->get('/san-pham/vinfast-vf-5-plus?gclid=Cj0KCQabc&gad_campaignid=111')->assertOk();
        $this->get('/san-pham?utm_source=zalo-oa')->assertOk();

        $this->post('/bam-lien-he', ['channel' => 'zalo', 'page' => '/san-pham'])->assertNoContent();

        // Nguồn tính theo lần vào ĐẦU TIÊN — lượt nhấp quảng cáo mới là cái tốn tiền.
        $this->assertSame('111', ContactClick::sole()->source['gad_campaignid']);
    }

    public function test_bam_lien_tiep_cung_nut_chi_ghi_mot_lan(): void
    {
        $this->get('/san-pham')->assertOk();

        $this->post('/bam-lien-he', ['channel' => 'zalo', 'page' => '/san-pham'])->assertNoContent();
        $this->post('/bam-lien-he', ['channel' => 'zalo', 'page' => '/san-pham'])->assertNoContent();
        $this->post('/bam-lien-he', ['channel' => 'phone', 'page' => '/san-pham'])->assertNoContent();

        $this->assertSame(['zalo', 'phone'], ContactClick::orderBy('id')->pluck('channel')->all());
    }

    public function test_kenh_la_thi_tu_choi(): void
    {
        $this->post('/bam-lien-he', ['channel' => 'email', 'page' => '/'])->assertStatus(302);
        $this->postJson('/bam-lien-he', ['channel' => 'email', 'page' => '/'])->assertStatus(422);

        $this->assertSame(0, ContactClick::count());
    }

    public function test_hanh_trinh_chi_giu_15_trang_gan_nhat(): void
    {
        for ($i = 0; $i < 20; $i++) {
            $this->get('/san-pham?trang='.$i)->assertOk();
        }

        $this->post('/bam-lien-he', ['channel' => 'phone', 'page' => '/san-pham'])->assertNoContent();

        $this->assertCount(15, ContactClick::sole()->trail);
    }

    public function test_ghi_thiet_bi_tu_user_agent(): void
    {
        $this->withHeader('User-Agent', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_0 like Mac OS X) Mobile/15E148')
            ->post('/bam-lien-he', ['channel' => 'phone', 'page' => '/'])
            ->assertNoContent();

        $this->assertSame('mobile', ContactClick::sole()->device);
    }

    public function test_trang_web_co_san_csrf_cho_javascript_gui_luot_bam(): void
    {
        $this->get('/san-pham')->assertOk()->assertSee('<meta name="csrf-token"', false);
    }

    public function test_admin_xem_duoc_danh_sach_luot_bam(): void
    {
        $this->actingAs(User::create(['name' => 'Admin', 'email' => 'admin@test.local', 'password' => 'x']));

        $click = ContactClick::create([
            'channel' => 'zalo',
            'page' => '/san-pham/vinfast-vf-5-plus',
            'product_id' => $this->vf5->id,
            'source' => ['gad_campaignid' => '24450242141', 'gclid' => true, 'landing' => '/san-pham/vinfast-vf-5-plus'],
            'trail' => [['p' => '/san-pham/vinfast-vf-5-plus', 't' => '15:48'], ['p' => '/dat-coc', 't' => '15:49']],
            'device' => 'desktop',
        ]);

        Livewire::test(ManageContactClicks::class)
            ->assertSuccessful()
            ->assertCanSeeTableRecords([$click])
            ->assertSee('Google Ads')
            ->assertSee('15:49 /dat-coc');
    }
}
