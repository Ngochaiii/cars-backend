<?php

namespace Database\Seeders;

use App\Support\Catalog;
use Illuminate\Database\Seeder;

/**
 * Khung của một site: cài đặt, menu, form đặt lịch, một trang tĩnh, một bài
 * viết. **Không có xe nào** — xe do seeder theo hãng lo
 * (`database/seeders/Brands/`), để đổi hãng không phải sửa file này.
 *
 * Chạy lại được nhiều lần (updateOrCreate).
 */
class CatalogDemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->form();
        $this->content();
        $this->settings();
        $this->menus();
    }

    /** Form đặt lịch lái thử — brand seeder nhúng form này vào cuối trang xe. */
    protected function form(): void
    {
        $form = Catalog::query('form')->updateOrCreate(
            ['key' => 'dat-lich-lai-thu'],
            ['name' => 'Đặt lịch lái thử', 'success_message' => 'Đã nhận thông tin, chúng tôi sẽ liên hệ sớm.'],
        );

        $form->fields()->delete();
        $form->fields()->createMany([
            ['key' => 'name', 'label' => 'Họ tên', 'type' => 'text', 'rules' => ['required'], 'sort' => 1, 'width' => 'half'],
            ['key' => 'phone', 'label' => 'Điện thoại', 'type' => 'tel', 'rules' => ['required'], 'sort' => 2, 'width' => 'half'],
            ['key' => 'email', 'label' => 'Email', 'type' => 'email', 'rules' => ['nullable'], 'sort' => 3],
            ['key' => 'note', 'label' => 'Ghi chú', 'type' => 'textarea', 'rules' => ['nullable'], 'sort' => 4],
        ]);
    }

    /** Trang tĩnh + bài viết — dùng đúng cơ chế `sections` của sản phẩm. */
    protected function content(): void
    {
        Catalog::query('page')->updateOrCreate(
            ['slug' => 'gioi-thieu'],
            [
                'title'    => 'Giới thiệu',
                'status'   => 'published',
                'sections' => [[
                    'title' => 'Về chúng tôi',
                    'type'  => 'text',
                    'body'  => "Trang tĩnh mẫu.\nSửa nội dung trong admin → Trang.",
                ]],
            ],
        );

        $postCategory = Catalog::query('post_category')->firstOrCreate(
            ['slug' => 'trai-nghiem'],
            ['name' => 'Trải nghiệm'],
        );

        Catalog::query('post')->updateOrCreate(
            ['slug' => 'bai-viet-mau'],
            [
                'title'            => 'Bài viết mẫu',
                'excerpt'          => 'Tóm tắt ngắn hiện ở thẻ bài viết.',
                'post_category_id' => $postCategory->id,
                'status'           => 'published',
                'published_at'     => now()->subDays(3),
                'sections'         => [[
                    'title' => 'Đoạn một',
                    'type'  => 'text',
                    'body'  => 'Bài viết dùng chung bộ mục với trang xe.',
                ]],
            ],
        );
    }

    /** Cài đặt: những giá trị layout frontend đọc ra ngay. */
    protected function settings(): void
    {
        $setting = Catalog::model('setting');

        $setting::put('site_name', 'Website ô tô demo');
        $setting::put('site_description', 'Dữ liệu mẫu để soi thử admin, API và luồng frontend.');
        $setting::put('hotline', '1900 0000');
        $setting::put('email', 'demo@example.test');
        $setting::put('address', 'Số 1, đường Demo, Hà Nội');
    }

    /**
     * Menu header + footer. Mục "Dòng xe" để trống mục con — brand seeder gắn
     * từng xe vào dưới đó, nên đổi hãng không phải sửa menu ở đây.
     */
    protected function menus(): void
    {
        $header = Catalog::query('menu')->updateOrCreate(['key' => 'header'], ['name' => 'Menu chính']);
        $header->items()->delete();

        $header->items()->create([
            'label' => catalog_label('product.plural'),
            'url'   => \App\Support\Url::prefix('product') ?: '/',
            'sort'  => 1,
        ]);
        $header->items()->create(['label' => 'Tin tức', 'url' => \App\Support\Url::prefix('post'), 'sort' => 2]);
        $header->items()->create(['label' => 'Giới thiệu', 'url' => '/gioi-thieu', 'sort' => 3]);

        $footer = Catalog::query('menu')->updateOrCreate(['key' => 'footer'], ['name' => 'Menu chân trang']);
        $footer->items()->delete();
        $footer->items()->create(['label' => 'Giới thiệu', 'url' => '/gioi-thieu', 'sort' => 1]);
        $footer->items()->create(['label' => 'Tin tức', 'url' => \App\Support\Url::prefix('post'), 'sort' => 2]);
    }
}
