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
│                         Url, Sitemap, JsonLd
└── Events/               LeadReceived

config/catalog.php        labels · features · section_presets · routes · seo
resources/views/frontend/ giao diện public (Blade)
database/migrations/      toàn bộ schema
tests/                    58 test
```

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
php artisan serve
```

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
- Node 20+ cho Vite

## Làm hãng mới

Fork repo này. Sửa:

- `config/catalog.php` — labels, features, section_presets, routes
- `resources/views/frontend/` — giao diện
- `.env` — `APP_NAME`, `DB_DATABASE`, `APP_URL`

Không đụng `app/Models`, `app/Filament`, `database/migrations` trừ khi thật sự
cần thêm hành vi riêng cho hãng đó.
