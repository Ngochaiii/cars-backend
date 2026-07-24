# Seeder theo hãng xe

Mỗi hãng một file. Lớp con chỉ khai **dữ liệu**; phần tạo bản ghi, dựng
`sections`, sinh ảnh placeholder và gắn menu do `BrandSeeder` lo.

```
BrandSeeder.php      khung dùng chung — sửa ở đây là mọi hãng đổi theo
VinFastSeeder.php    VinFast: VF 3 · VF 5 Plus · VF 6 · VF 7 · VF 8 · VF 9
MauSeeder.php        MẪU để copy — mọi khoá dữ liệu có comment giải thích
```

## Chạy

```bash
# Toàn bộ: user admin + khung site (settings, menu, form, trang, tin) + VinFast
php artisan migrate:fresh --seed

# Chỉ một hãng
php artisan db:seed --class="Database\Seeders\Brands\VinFastSeeder"

# Chỉ khung site, không xe nào
php artisan db:seed --class=CatalogDemoSeeder
```

Chạy lại bao nhiêu lần cũng được: mọi thứ `updateOrCreate` theo `slug`, phiên
bản và bảng màu thì xoá rồi tạo lại.

## Thêm hãng mới

1. Copy `MauSeeder.php` thành `ToyotaSeeder.php`, đổi tên class.
2. Sửa `brand()`, `categories()`, `products()`.
3. Chạy `php artisan db:seed --class="Database\Seeders\Brands\ToyotaSeeder"`.
4. Muốn nó chạy cùng `migrate:fresh --seed` thì thêm vào `DatabaseSeeder`.

Một site chỉ bán một hãng (mỗi hãng fork repo riêng), nên bình thường
`DatabaseSeeder` chỉ gọi **một** brand seeder. Nhiều file ở đây là để chọn,
không phải để chạy cùng lúc.

## Hình dạng dữ liệu một chiếc xe

| Khoá | Kiểu | Ý nghĩa |
|---|---|---|
| `slug` `name` | string | **bắt buộc** |
| `tagline` | string | dòng mô tả ngắn dưới tên |
| `category` | string | khoá trong `categories()` |
| `price_from` | int | giá "từ" |
| `hero` | bool | sinh ảnh hero placeholder |
| `highlights` | array | `[['value','unit','label']]` — 3–4 con số nổi bật |
| `media` | array | mục ảnh: `'Tên mục' => ['layout','intro','items']` |
| `story` | array | mục văn bản: `'Tên mục' => nội dung` |
| `video` | string | link YouTube/Vimeo/mp4 |
| `tables` | array | mục bảng: `'Tên mục' => ['nhãn' => 'giá trị']` |
| `form` | bool | nhúng form đặt lịch ở cuối (mặc định `true`) |
| `colors` | array | `'Tên màu' => '#hex'` |
| `variants` | array | `[['name','price','price_original','note','is_default']]` |
| `specs` | array | `'Nhóm' => ['nhãn' => 'giá trị']` |
| `seo` | array | `['title','description']` |

Bỏ khoá nào thì phần đó không sinh ra — đúng quy tắc "ô trống thì không
render" của tài liệu kiến trúc.

Thứ tự mục trên trang chi tiết luôn là: **ảnh → chữ → video → bảng → form**.
Muốn thứ tự khác cho một xe cụ thể thì sửa trong admin (kéo thả), seeder chỉ
dựng lần đầu.

## Ảnh

Seeder sinh ảnh placeholder xám có chữ, đặt tại:

```
storage/app/public/catalog/{hãng}/{slug-xe}/hero.jpg
storage/app/public/catalog/{hãng}/{slug-xe}/{ten-muc}-1.jpg
```

Chỉ tạo khi file **chưa có** — upload ảnh thật trong admin rồi thì chạy lại
seeder cũng không đè mất.

Ảnh không hiện? Kiểm tra symlink:

```bash
php artisan storage:link
```

## Số liệu

Giá và thông số trong `VinFastSeeder` là **tham khảo tại thời điểm viết**, không
phải bảng giá chính thức — hãng đổi giá và chính sách pin liên tục. Đối chiếu
lại với nguồn chính hãng trước khi đưa lên trang thật.
