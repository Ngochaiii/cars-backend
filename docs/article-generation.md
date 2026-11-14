# Viết bài SEO/GEO từ một tiêu đề

Trong Admin → **Bài viết** bấm **Viết bài bằng AI**, nhập **tiêu đề** rồi bấm *Bắt đầu viết*.
Đó là toàn bộ phần phải nhập. Một bài **Nháp** mang tiêu đề đó xuất hiện ngay trong danh sách
để theo dõi tiến độ; trang không chờ API nên có thể đóng tab.

Gemini tự làm phần còn lại và điền thẳng vào bài đó:

| Thẻ | Nguồn |
| --- | --- |
| Slug | tạo từ tiêu đề, tự thêm số khi trùng |
| Tóm tắt, thân bài, hỏi đáp | Gemini viết |
| Ảnh bìa | ảnh hero của dòng xe được nhắc trong tiêu đề; không khớp thì để trống |
| Chuyên mục | Gemini chọn trong các chuyên mục đang có; không khớp thì để trống |
| Thẻ title, meta description, từ khóa | Gemini viết (title ≤ 70 ký tự, description 140–165 ký tự) |

Mở bài, đọc lại rồi bấm **Đăng bài** — xác nhận trong hộp thoại là nơi biên tập viên nhận
trách nhiệm đã đối chiếu số liệu và nguồn; nút đó ghi luôn trạng thái Đã đăng, thời điểm đăng
và dấu đã đọc duyệt. Đổi trạng thái bằng ô *Trạng thái* rồi lưu thì vẫn phải tích ô đọc duyệt.
**Viết lại bằng AI** trên trang sửa dùng tiêu đề hiện tại và thay nội dung của đúng bài đó.
Hệ thống không bao giờ tự đăng bài.

## Luật viết bài

- Dài 1.300–1.500 từ.
- Thẻ title viết hoa theo lối tiếng Việt: chỉ hoa chữ đầu câu và tên riêng, không hoa từng từ.
- Toàn bài tối đa **4 liên kết**, ưu tiên trang của dòng xe đang nói đến (dẫn 1–2 lần, anchor
  text tự nhiên). URL trang xe và bài cũ được đưa sẵn vào dữ liệu cho AI; link nằm ngoài danh
  sách đó bị khối kiểm tra bắt lỗi.

## Chuẩn GEO trong bài

- Đoạn đầu trả lời trực tiếp câu hỏi của tiêu đề, đứng độc lập được.
- Ngay sau đó là danh sách 3–5 ý chính, mỗi dòng một câu trọn nghĩa để trích dẫn riêng.
- Đề mục h2 viết dưới dạng câu hỏi người đọc thật sự tìm kiếm.
- Khối **Câu hỏi thường gặp** (mục `faq`) hiện ngay trên trang, không gập lại.
- Trang bài nhúng một `@graph` gồm `Article` (có author, publisher, dateModified, keywords,
  wordCount, inLanguage), `FAQPage` và `BreadcrumbList`. `FAQPage` chỉ khai những câu hỏi
  đang hiển thị trên trang — đúng yêu cầu của Google, không khai câu hỏi không có thật.

## Khối tư vấn viên cuối bài

Sau phần hỏi đáp, mỗi bài hiện một khối liên hệ gọn: ảnh tròn, tên, chức danh và hai nút
Gọi / Nhắn Zalo (`resources/views/frontend/partials/article-advisor.blade.php`, neo `#tu-van-vien`).

Nội dung **do template dựng từ Cài đặt → Trang chủ**, dùng chung dữ liệu với khối tư vấn viên ở
trang chủ (`advisor_name`, `advisor_role`, `advisor_phone`, `advisor_zalo`, `advisor_image`);
chưa khai thì lùi về Hotline và Zalo chung. Bật `advisor_off` là ẩn ở cả hai nơi.

Cố ý **không** để AI viết đoạn này trong từng bài: 50 bài sẽ có 50 đoạn na ná nhau, và AI dễ bịa
chi tiết về một người thật. Cũng **không** khai tư vấn viên là `author` trong JSON-LD — bài do AI
viết, tác giả phải là đại lý.

## Các bước chạy nền

Ba bước lưu riêng: **nghiên cứu/dàn ý → viết → kiểm tra rồi điền vào bài**. Bước nghiên cứu tự
rút ra từ khóa chính, ý định tìm kiếm, câu hỏi người đọc và dàn ý từ tiêu đề; nó chạy ở mức suy
nghĩ `low` (`GEMINI_RESEARCH_THINKING`) vì chỉ rút trích có cấu trúc — để mặc định thì model
3.x có thể suy nghĩ quá 75 giây và bị cắt kết nối.

Bước lỗi được thử tối đa 4 lần, cách nhau 30/120/300 giây, mỗi lần đổi sang model kế tiếp
trong `GEMINI_MODEL` + `GEMINI_FALLBACK_MODELS`. Lỗi cấu hình 400 hoặc quyền 401/403 dừng ngay.
Mỗi lần gọi API tối đa 75 giây. Hết lượt hoặc worker ngắt quá lâu thì yêu cầu chuyển *Tạm dừng*;
mở bài và bấm **Thử lại từ bước đã lưu** trong khối *Trợ lý viết bài SEO / GEO*.

Mỗi biên tập viên chỉ có một yêu cầu đang chạy — bấm nhiều lần không tạo thêm bài hay thêm chi phí.

### Khi các model flash mới quá tải

`gemini-3.8-flash` và `gemini-3.7-flash` có những đợt trả 503 "high demand" hoặc treo hết
75 giây. Hệ thống tự xoay sang model kế tiếp nên bài vẫn ra, chỉ chậm hơn vài phút. Muốn nhanh
hơn thì đặt model đang rảnh lên đầu trong `.env`:

```sh
GEMINI_MODEL=gemini-3.6-flash
GEMINI_FALLBACK_MODELS=gemini-3.8-flash,gemini-3.7-flash,gemini-3.5-flash
```

Gói API miễn phí giới hạn **20 request/ngày cho mỗi model**; một bài tốn 2 request (nghiên cứu
và viết). Hết hạn mức thì yêu cầu báo 429 và model đó bị loại khỏi các lượt sau.

## Nguồn và chất lượng

- Bật **Google Search** bằng `GEMINI_GOOGLE_SEARCH=true` khi dự án API có quyền dùng công cụ này;
  có thể phát sinh phí theo gói. Không tự bật billing hay đổi tài khoản.
- Chỉ nguồn trong `groundingMetadata` được ghi là nguồn tìm kiếm. AI không được tự tạo danh sách
  nguồn rồi coi đó là đã tra cứu.
- Khi không có nguồn tìm kiếm, từ khóa chỉ là đề xuất theo ngữ nghĩa, không có volume/KD.
- Khối kiểm tra soát: từ khóa trong title/mở đầu (đủ các từ có nghĩa, không cần trùng nguyên cụm),
  số đề mục, độ dài mô tả, độ dài bài, cách viết hoa thẻ title, số lượng và nguồn gốc liên kết,
  số liệu giá/thông số cần đối chiếu, và độ trùng cụm 5 từ với 100 bài đã đăng gần nhất.
  Đây không phải kiểm tra đạo văn toàn Internet, không phải máy xác minh sự thật, và không có
  bảo đảm thứ hạng tìm kiếm.

## Vận hành

```sh
php artisan migrate --force
php artisan optimize
```

Worker riêng (không dùng worker mail để xử lý bài AI):

```sh
php artisan queue:work article_generation --queue=articles --sleep=3 --tries=20 --timeout=110 --max-time=3600 --memory=256
```

Connection `article_generation` dùng bảng jobs, queue `articles`, retry_after 180 giây, lớn hơn
timeout worker 110 giây và khóa chống xử lý đồng thời 140 giây. Có checkpoint và run version để
job cũ không tiếp tục sau khi người dùng thử lại. PHP CLI cần pcntl.

VPS hiện tại có mẫu unit `deploy/cars-articles.service`. Sau khi kiểm tra đường dẫn và user:

```sh
sudo cp deploy/cars-articles.service /etc/systemd/system/cars-articles.service
sudo systemctl daemon-reload
sudo systemctl enable --now cars-articles
sudo systemctl status cars-articles
```

Sau khi cập nhật code, `systemctl restart cars-articles` (chờ lượt đang chạy kết thúc). Không dừng
hoặc xóa queue mail. Khi queued quá 5 phút, giao diện gợi ý kiểm tra worker. Chẩn đoán:
`journalctl -u cars-articles -n 50`, bảng `article_generations` và log Laravel. Không đưa API key
vào log, bài viết hoặc Git. Mất mạng/quota vẫn có thể khiến yêu cầu không hoàn thành; trạng thái
lỗi và checkpoint được giữ lại để xử lý tiếp.
