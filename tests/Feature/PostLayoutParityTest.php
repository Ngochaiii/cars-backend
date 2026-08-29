<?php

namespace Tests\Feature;

use App\Filament\Resources\Posts\Pages\CreatePost;
use App\Filament\Resources\Posts\Pages\EditPost;
use App\Media\MediaStore;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Bảo đảm một bài viết đầy đủ tạo từ Filament đi nguyên vẹn
 * qua database, trang danh sách, trang chi tiết và API.
 */
class PostLayoutParityTest extends TestCase
{
    protected PostCategory $category;

    protected function setUp(): void
    {
        parent::setUp();

        foreach ([
            'catalog/posts/vf-7-ra-mat.jpg',
            'catalog/sections/vf-7-noi-that.jpg',
            'catalog/seo/vf-7-news-social.jpg',
        ] as $path) {
            app(MediaStore::class)->write(
                $path,
                UploadedFile::fake()->image(basename($path), 1200, 675)->getContent()
            );
        }

        $this->actingAs(User::create([
            'name' => 'Admin',
            'email' => 'admin@test.local',
            'password' => 'x',
        ]));

        $this->category = PostCategory::create([
            'name' => 'Xe điện',
            'slug' => 'xe-dien',
        ]);
    }

    /** @return array<string, mixed> */
    protected function formData(): array
    {
        return [
            'title' => 'VF 7 ra mắt phiên bản mới',
            'slug' => 'vf-7-ra-mat-phien-ban-moi',
            'post_category_id' => $this->category->id,
            'status' => 'published',
            'published_at' => now()->subMinute(),
            'cover' => ['catalog/posts/vf-7-ra-mat.jpg'],
            'excerpt' => 'Mẫu SUV điện được bổ sung công nghệ an toàn và màu sắc mới.',
            'sections' => [
                [
                    'title' => 'Thiết kế hướng tới người lái',
                    'type' => 'text',
                    'layout' => 'cols-3',
                    'intro' => 'Những thay đổi đáng chú ý trên phiên bản mới.',
                    'body' => 'Khoang lái được tối ưu cho cả hành trình trong đô thị và đường dài.',
                ],
                [
                    'title' => 'Hình ảnh thực tế',
                    'type' => 'media',
                    'layout' => 'cols-2',
                    'items' => [[
                        'image' => ['catalog/sections/vf-7-noi-that.jpg'],
                        'label' => 'Nội thất VF 7',
                        'desc' => 'Không gian tối giản và hiện đại.',
                    ]],
                ],
                [
                    'title' => 'Thông tin nhanh',
                    'type' => 'table',
                    'layout' => 'cols-3',
                    'rows' => [
                        ['label' => 'Quãng đường', 'value' => '496 km'],
                        ['label' => 'Dung lượng pin', 'value' => '75,3 kWh'],
                    ],
                ],
            ],
            'seo' => [
                'title' => 'VF 7 phiên bản mới — thông tin chính thức',
                'description' => 'Hình ảnh và thông tin chi tiết VF 7 phiên bản mới.',
                'canonical' => 'https://cars.example/tin/vf-7-ra-mat',
                'image' => ['catalog/seo/vf-7-news-social.jpg'],
            ],
        ];
    }

    protected function createPost(): Post
    {
        Livewire::test(CreatePost::class)
            ->fillForm($this->formData())
            ->call('create')
            ->assertHasNoFormErrors();

        return Post::where('slug', 'vf-7-ra-mat-phien-ban-moi')->sole();
    }

    public function test_tao_tin_day_du_tu_admin_ra_dung_trang_khach_va_api(): void
    {
        $post = $this->createPost();

        $this->assertSame($this->category->id, $post->post_category_id);
        $this->assertSame('vf-7-ra-mat.jpg', basename((string) $post->cover));
        $this->assertSame('vf-7-news-social.jpg', basename((string) data_get($post->seo, 'image')));
        $this->assertSame('Khoang lái được tối ưu cho cả hành trình trong đô thị và đường dài.', $post->sections[0]['body']);
        $this->assertSame('496 km', $post->sections[2]['rows'][0]['value']);

        $this->get('/tin-tuc')
            ->assertOk()
            ->assertSee('VF 7 ra mắt phiên bản mới')
            ->assertSee('Xe điện');

        $this->get('/chuyen-muc/xe-dien')
            ->assertOk()
            ->assertSee('VF 7 ra mắt phiên bản mới');

        $html = $this->get('/tin-tuc/vf-7-ra-mat-phien-ban-moi')
            ->assertOk()
            ->assertSee('Mẫu SUV điện được bổ sung công nghệ an toàn')
            ->assertSee('Khoang lái được tối ưu')
            ->assertSee('Nội thất VF 7')
            ->assertSee('496 km')
            ->getContent();

        $this->assertStringContainsString('<link rel="canonical" href="https://cars.example/tin/vf-7-ra-mat">', $html);
        $this->assertStringContainsString('/storage/catalog/seo/vf-7-news-social.jpg', $html);

        $data = $this->getJson('/api/v1/posts/vf-7-ra-mat-phien-ban-moi')
            ->assertOk()
            ->json('data');

        $this->assertSame($post->cover, $data['cover']);
        $this->assertSame('Xe điện', $data['category']['name']);
        $this->assertSame('Khoang lái được tối ưu cho cả hành trình trong đô thị và đường dài.', $data['sections'][0]['body']);
        $this->assertSame('https://cars.example/tin/vf-7-ra-mat', $data['canonical']);
        $this->assertSame('https://cars.example/tin/vf-7-ra-mat', $data['jsonld']['url']);
        $this->assertStringEndsWith('/storage/catalog/seo/vf-7-news-social.jpg', $data['jsonld']['image']);
    }

    public function test_sua_tin_trong_admin_khong_lam_mat_noi_dung_anh_va_seo(): void
    {
        $post = $this->createPost();

        Livewire::test(EditPost::class, ['record' => $post->getRouteKey()])
            ->assertSuccessful()
            ->fillForm(['title' => 'VF 7 ra mắt phiên bản mới tại Việt Nam'])
            ->call('save')
            ->assertHasNoFormErrors();

        $post->refresh();

        $this->assertSame('VF 7 ra mắt phiên bản mới tại Việt Nam', $post->title);
        $this->assertSame('vf-7-ra-mat.jpg', basename((string) $post->cover));
        $this->assertSame('Khoang lái được tối ưu cho cả hành trình trong đô thị và đường dài.', $post->sections[0]['body']);
        $this->assertSame('https://cars.example/tin/vf-7-ra-mat', data_get($post->seo, 'canonical'));
    }
}
