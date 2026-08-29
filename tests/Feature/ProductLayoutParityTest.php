<?php

namespace Tests\Feature;

use App\Filament\Resources\Products\Pages\CreateProduct;
use App\Filament\Resources\Products\Pages\EditProduct;
use App\Models\Category;
use App\Models\Form;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Chốt lời hứa lớn nhất của module thêm xe: một người KHÔNG BIẾT CODE vào
 * admin điền form là ra được trang chi tiết đủ khối như bản thiết kế, không
 * cần ai sửa seeder hộ.
 *
 * Test này cố ý dựng xe hoàn toàn qua form Filament chứ không Product::create
 * — chỉ đường vòng qua form mới bắt được lỗi cột json bị ghi đè.
 */
class ProductLayoutParityTest extends TestCase
{
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        foreach ([
            'catalog/hero/vf-7-desktop.jpg',
            'catalog/hero/vf-7-mobile.jpg',
            'catalog/options/vf-7-white.jpg',
            'catalog/seo/vf-7-social.jpg',
        ] as $path) {
            Storage::disk('public')->put(
                $path,
                UploadedFile::fake()->image(basename($path), 1200, 675)->getContent()
            );
        }

        $this->actingAs(User::create([
            'name' => 'Admin', 'email' => 'admin@test.local', 'password' => 'x',
        ]));

        $form = Form::create(['key' => 'dang-ky-tu-van', 'name' => 'Đăng ký tư vấn']);
        $form->fields()->create([
            'key' => 'phone', 'label' => 'Số điện thoại', 'type' => 'tel',
            'rules' => ['required'], 'sort' => 1,
        ]);

        $this->category = Category::create([
            'name' => 'SUV điện',
            'slug' => 'suv-dien',
        ]);

        config(['catalog.frontend.product_forms' => ['dang-ky-tu-van']]);
    }

    /** Đúng bộ dữ liệu một người nhập sẽ gõ để dựng lại bản thiết kế. */
    protected function formData(): array
    {
        return [
            'name' => 'Xe Mẫu VF 7',
            'slug' => 'xe-mau-vf-7',
            'tagline' => 'Khi phong cách trở thành dấu ấn',
            'status' => 'published',
            'published_at' => now(),
            'category_id' => $this->category->id,
            'price_from' => 799_000_000,
            'brochure_url' => 'https://example.com/brochures/vf-7.pdf',

            'hero' => [
                'type' => 'image',
                'src' => ['catalog/hero/vf-7-desktop.jpg'],
                'mobile_src' => ['catalog/hero/vf-7-mobile.jpg'],
                'lede' => 'Thiết kế hoàn toàn mới, công nghệ dẫn đầu.',
                'intro_title' => 'Thiết kế phong cách cho thế hệ khách hàng hiện đại',
                'intro_body' => 'Ngoại hình liền mạch, tỷ lệ cân đối, chi tiết tinh giản.',
            ],

            'highlights' => [
                ['value' => '260', 'unit' => 'kW', 'label' => 'Công suất tối đa'],
                ['value' => '500', 'unit' => 'Nm', 'label' => 'Mô-men xoắn'],
                ['value' => '496', 'unit' => 'km', 'label' => 'Quãng đường'],
                ['value' => '75,3', 'unit' => 'kWh', 'label' => 'Dung lượng pin'],
            ],

            'variants' => [[
                'name' => 'Plus',
                'price' => 999_000_000,
                'price_original' => 1_049_000_000,
                'note' => 'Pin mua kèm xe',
                'battery_kwh' => 75.3,
                'range_km' => 496,
                'is_default' => true,
            ]],

            'options' => [[
                'name' => 'Brahminy White',
                'hex' => '#f4f1e8',
                'image' => ['catalog/options/vf-7-white.jpg'],
            ]],

            'sections' => [
                ['title' => 'Tech Fluid — dòng chảy công nghệ', 'intro' => 'Đoạn mở đầu.',
                    'type' => 'media', 'layout' => 'gallery',
                    'items' => [['label' => 'Ảnh lớn'], ['label' => 'Ảnh 2'], ['label' => 'Ảnh 3']]],

                ['title' => 'Trải nghiệm thị giác không giới hạn', 'intro' => 'Chữ trái, ảnh phải.',
                    'type' => 'media', 'layout' => 'split', 'items' => [['label' => 'Ảnh 3/4']]],

                ['title' => 'Điểm nhấn công nghệ', 'intro' => 'Băng chuyền ba ảnh.',
                    'type' => 'media', 'layout' => 'carousel',
                    'items' => [['label' => 'A'], ['label' => 'B'], ['label' => 'C']]],

                ['title' => 'Nội thất khoáng đạt', 'intro' => 'Ảnh trái, chữ phải.',
                    'type' => 'media', 'layout' => 'split-alt', 'items' => [['label' => 'Nội thất']]],

                ['title' => 'Nâng cấp trải nghiệm thực tế mỗi ngày',
                    'type' => 'media', 'layout' => 'tabs',
                    'items' => [['label' => 'Tab 1'], ['label' => 'Tab 2'],
                        ['label' => 'Tab 3'], ['label' => 'Tab 4']]],
            ],

            'specs' => [
                ['group' => 'Động cơ', 'rows' => [
                    ['label' => 'Công suất tối đa', 'value' => '260 kW'],
                    ['label' => 'Mô-men xoắn', 'value' => '500 Nm'],
                ]],
            ],

            'spec_notes' => [
                ['label' => 'An toàn & an ninh', 'body' => 'Camera 360 độ · 6 túi khí.'],
                ['label' => 'Hỗ trợ lái nâng cao ADAS', 'body' => 'Ga tự động thích ứng · Giữ làn.'],
            ],

            'seo' => [
                'title' => 'VF 7 — SUV điện phong cách',
                'description' => 'Mô tả SEO cho công cụ tìm kiếm.',
                'canonical' => 'https://cars.example/vf-7',
                'image' => ['catalog/seo/vf-7-social.jpg'],
            ],
        ];
    }

    public function test_dung_xe_bang_form_admin_ra_du_khoi_nhu_ban_thiet_ke(): void
    {
        Livewire::test(CreateProduct::class)
            ->fillForm($this->formData())
            ->call('create')
            ->assertHasNoFormErrors();

        $html = $this->get('/san-pham/xe-mau-vf-7')->assertOk()->getContent();

        // Đủ 5 bố cục của bản thiết kế
        $this->assertStringContainsString('layout-gallery', $html, 'thiếu thư viện lớn');
        $this->assertStringContainsString('data-gallery', $html, 'thiếu băng chuyền');
        $this->assertStringContainsString('layout-split', $html, 'thiếu bố cục chia đôi');
        $this->assertStringContainsString('split--media-first', $html, 'thiếu chia đôi đảo bên');
        $this->assertStringContainsString('data-tabs', $html, 'thiếu tab đánh số');

        // Thông số phẳng + hai ô ghi chú
        $this->assertStringContainsString('spec-flat', $html, 'thông số không phải lưới phẳng');
        $this->assertStringContainsString('spec-notes', $html, 'thiếu ghi chú dưới bảng thông số');
        $this->assertStringContainsString('An toàn &amp; an ninh', $html);

        // Chữ đầu trang: ba câu KHÁC NHAU, đúng ba chỗ
        $this->assertStringContainsString('Khi phong cách trở thành dấu ấn', $html);
        $this->assertStringContainsString('Thiết kế hoàn toàn mới, công nghệ dẫn đầu.', $html);
        $this->assertStringContainsString('Thiết kế phong cách cho thế hệ khách hàng hiện đại', $html);
        $this->assertStringContainsString('Ngoại hình liền mạch, tỷ lệ cân đối, chi tiết tinh giản.', $html);

        // Dữ liệu bán hàng/SEO nhập cùng form phải đi hết ra trang khách.
        $this->assertStringContainsString('Plus', $html);
        $this->assertStringContainsString('Brahminy White', $html);
        $this->assertStringContainsString('https://example.com/brochures/vf-7.pdf', $html);
        $this->assertStringContainsString('<link rel="canonical" href="https://cars.example/vf-7">', $html);
        $this->assertStringContainsString('/storage/catalog/seo/vf-7-social.jpg', $html);

        $product = Product::where('slug', 'xe-mau-vf-7')->sole();

        $this->assertSame($this->category->id, $product->category_id);
        $this->assertSame('75.30', $product->variants->sole()->battery_kwh);
        $this->assertSame(496, $product->variants->sole()->range_km);
        $this->assertTrue($product->variants->sole()->is_default);
        $this->assertSame('vf-7-mobile.jpg', basename($product->hero['mobile_src']));
    }

    public function test_khong_in_lap_mot_cau_hai_lan(): void
    {
        $data = $this->formData();

        // Bỏ trống hai ô chữ để cả hai chỗ đều phải đi tìm nguồn dự phòng.
        unset($data['hero']['lede'], $data['hero']['intro_body']);

        Livewire::test(CreateProduct::class)
            ->fillForm($data)
            ->call('create')
            ->assertHasNoFormErrors();

        $html = $this->get('/san-pham/xe-mau-vf-7')->assertOk()->getContent();

        // Chỉ đếm trong <main>: mô tả SEO còn nằm ở hai thẻ meta trên <head>
        // (description và og:description), đếm cả trang thì luôn lệch và
        // thêm một thẻ meta nữa là test đỏ oan.
        $body = Str::between($html, '<main>', '</main>');

        $this->assertSame(
            1,
            substr_count($body, 'Mô tả SEO cho công cụ tìm kiếm.'),
            'mô tả SEO bị in lặp trong thân trang — hai chỗ cùng mượn một nguồn'
        );
    }

    public function test_sua_xe_trong_admin_khong_lam_mat_chu_dau_trang(): void
    {
        Livewire::test(CreateProduct::class)
            ->fillForm($this->formData())
            ->call('create')
            ->assertHasNoFormErrors();

        $product = Product::where('slug', 'xe-mau-vf-7')->sole();

        Livewire::test(EditProduct::class, ['record' => $product->getRouteKey()])
            ->assertSuccessful()
            ->fillForm(['name' => 'Xe Mẫu VF 7 bản mới'])
            ->call('save')
            ->assertHasNoFormErrors();

        $product->refresh();

        $this->assertSame('Thiết kế hoàn toàn mới, công nghệ dẫn đầu.', $product->hero['lede']);
        $this->assertSame('Thiết kế phong cách cho thế hệ khách hàng hiện đại', $product->hero['intro_title']);
        $this->assertSame('Ngoại hình liền mạch, tỷ lệ cân đối, chi tiết tinh giản.', $product->hero['intro_body']);
        $this->assertSame('An toàn & an ninh', $product->spec_notes[0]['label']);
    }
}
