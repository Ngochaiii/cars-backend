# Cars — website ô tô

Laravel 13 · PHP 8.3 · Filament 5 · MariaDB

Một app duy nhất: backend (admin Filament + API) và frontend (Blade) chung repo.
Làm hãng mới thì **fork repo này**, đổi `config/catalog.php` và giao diện.

## Cấu trúc

```
app/
├── Models/               Product, Variant, Option, Category, Post, Page,
│                         Menu, Setting, Redirect, Form, Lead, Template…
├── Filament/             Admin — Resources + trang Cài đặt
├── Http/
│   ├── Controllers/
│   │   ├── Api/          API JSON /api/v1/*
│   │   ├── Frontend/     Trang khách xem, render Blade
│   │   └── Backend/      (để dành — admin hiện do Filament lo)
│   ├── Resources/        API Resource
│   └── Middleware/       HandleRedirects (301 tự động)
├── Support/              Catalog, SectionCollection, SpecTableParser,
│                         Url, Sitemap, JsonLd, Media, Money
├── Actions/              StoreLead (dùng chung API + form Blade)
└── Events/               LeadReceived

config/catalog.php        labels · features · section_presets · frontend · routes · seo
resources/views/frontend/ giao diện public (Blade)
public/css/frontend.css   CSS giao diện ô tô — viết tay, không build
public/js/frontend.js     JS thuần cho menu, carousel, màu xe và tabs
database/migrations/      toàn bộ schema
tests/                    104 test
```

## Frontend

Blade render thẳng trong app. Giao diện public dùng CSS và JavaScript
thuần, không phụ thuộc framework frontend hay Vite khi chạy production.

Banner trang chủ và hero từng xe có ảnh desktop/mobile riêng. Trang chi
tiết hỗ trợ phiên bản, bảng màu, brochure riêng, gallery, tabs,
thông số, so sánh chi phí, trả góp và form tư vấn.

| Trang | URL (tiền tố lấy từ `config('catalog.routes')`) |
|---|---|
| Trang chủ | `/` |
| Danh sách mặt hàng | `/san-pham` |
| Danh mục | `/danh-muc/{slug}` |
| Chi tiết | `/san-pham/{slug}` |
| Tin tức · chuyên mục · bài | `/tin-tuc` · `/chuyen-muc/{slug}` · `/tin-tuc/{slug}` |
| Trang tĩnh | `/{slug}` |
| Nhận form | `POST /gui-form/{form}` |

Sáu kiểu mục của `sections` đều có view riêng trong
`resources/views/frontend/partials/section/`: `media` · `text` · `video` ·
`table` · `form` · `custom`. Kiểu `custom` tìm view
`frontend/sections/{slug-tên-mục}.blade.php` của riêng dự án, không có thì mục
im lặng.

## Nguyên tắc

Không chạy migration khi đổi hãng hoặc đổi mặt hàng:

1. Cột cố định chỉ giữ những gì mọi mặt hàng đều có
2. Mọi thứ biến thiên nằm trong `sections` — mảng mục do người nhập tự tạo
3. Chữ hiển thị trong admin đến từ `config/catalog.php`, không phải từ tên cột

`config/catalog.php` là chỗ khác nhau giữa các hãng: `labels` đổi chữ,
`features` bật/tắt khối, `section_presets` gợi ý tên mục, `routes` đổi hình
dạng URL. `tests/Feature/ItemTypeConfigTest.php` chứng minh: dựng mặt hàng nội
thất trên cùng schema mà bảng `migrations` không thêm dòng nào.

## Chạy

```bash
composer install
cp .env.example .env && php artisan key:generate
php artisan migrate --seed
php artisan storage:link      # để ảnh upload hiện được
php artisan serve
```

### Upload media không cần `ext-fileinfo`

Ảnh và PDF trong admin dùng bộ lưu trữ native của dự án: PHP kiểm tra chữ ký
file/kích thước ảnh, tự đặt tên an toàn, ghi file vào thư mục media và chỉ lưu
đường dẫn tương đối (`catalog/...`) trong database. Luồng này không gọi
`finfo`, `mime_content_type`, Flysystem hay Media Library.

Trước khi POST, trình duyệt tự thu ảnh về cạnh dài tối đa 1920 px và mã hoá
WebP chất lượng 82%. Nếu bản WebP không nhẹ hơn hoặc trình duyệt không hỗ trợ,
uploader giữ nguyên file gốc. Có thể chỉnh bằng
`MEDIA_CLIENT_IMAGE_MAX_DIMENSION` và `MEDIA_CLIENT_IMAGE_QUALITY`.

Ngay sau khi lưu, endpoint tự sinh các bản WebP responsive 400/800/1280 px
(tuỳ chiều rộng ảnh) và cập nhật manifest cho `<x-img>`. Vì vậy ảnh upload từ
admin dùng `srcset` ngay lập tức; không cần chạy `php artisan catalog:images`
sau mỗi lần upload. Lệnh này chỉ còn dùng để xử lý lại ảnh cũ hoặc rebuild.

Mặc định local vẫn dùng `storage/app/public` và `php artisan storage:link`.
Trên VPS nên đặt media ngoài thư mục mỗi release để deploy code không làm mất
ảnh:

```dotenv
MEDIA_ROOT=/var/www/cars/shared/media
MEDIA_URL=/storage
```

Nginx phục vụ chính thư mục đó (đổi path theo máy chủ):

```nginx
location ^~ /storage/ {
    alias /var/www/cars/shared/media/;
    autoindex off;
    add_header X-Content-Type-Options nosniff always;
}
```

Thư mục `MEDIA_ROOT` phải cho user PHP-FPM quyền ghi. Do Laravel và Filament
vẫn kéo các package có khai báo platform `ext-fileinfo` dù luồng upload của app
không dùng chúng, máy build không có extension cần cài dependency bằng:

```bash
composer install --no-dev --optimize-autoloader --ignore-platform-req=ext-fileinfo
```

Tốt nhất build `vendor/` trong CI rồi deploy nguyên gói code + vendor; VPS khi
đó không cần chạy Composer và cũng không cần cài `fileinfo`.

## Dữ liệu mẫu

| Seeder | Seed gì |
|---|---|
| `CatalogDemoSeeder` | khung site: cài đặt, menu, form đặt lịch, trang tĩnh, bài viết |
| `Brands\VinFastSeeder` | 6 mẫu xe VinFast — đủ phiên bản, bảng màu, mục, thông số |
| `Brands\MauSeeder` | **mẫu để copy** khi thêm hãng mới, mọi khoá có comment |

```bash
php artisan db:seed --class="Database\Seeders\Brands\VinFastSeeder"
```

Thêm hãng mới: copy `MauSeeder.php`, sửa `brand()` · `categories()` ·
`products()`. Chi tiết trong `database/seeders/Brands/README.md`.

| | |
|---|---|
| Trang chủ | http://127.0.0.1:8000 |
| Admin | http://127.0.0.1:8000/admin — `admin@cars.test` / `password` |
| API | http://127.0.0.1:8000/api/v1/products |
| Sitemap | http://127.0.0.1:8000/sitemap.xml |

## Test

```bash
php artisan test
```

Chạy trên MariaDB thật (`catalog_cars_test`), không phải sqlite `:memory:` —
schema dựa nặng vào cột `json` và `enum`, SQLite giả lập bằng text nên test có
thể xanh mà production vẫn hỏng.

## Yêu cầu

- PHP 8.3+
- MariaDB 10.4+ / MySQL 8 — `DB_CONNECTION=mariadb` trong `.env`

Node chỉ cần khi muốn chạy `npm run build` để kiểm tra bộ asset Vite
mặc định; frontend khách xem vẫn nạp file tĩnh trong `public/js` và
`public/css`.

## Làm hãng mới

Fork repo này. Sửa:

- `config/catalog.php` — labels, features, section_presets, frontend, routes
- `resources/views/frontend/` + `public/css/frontend.css` — giao diện
- `.env` — `APP_NAME`, `DB_DATABASE`, `APP_URL`

Không đụng `app/Models`, `app/Filament`, `database/migrations` trừ khi thật sự
cần thêm hành vi riêng cho hãng đó.
