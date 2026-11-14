# Tạo bài SEO/GEO chạy nền

Trong Admin → Bài viết → Tạo/sửa, nhập tiêu đề, ảnh, từ khóa chính, ý định tìm kiếm,
khu vực và dữ kiện đã duyệt. Bấm **Sinh bài viết bằng Gemini**. Trang không chờ API.
Chọn yêu cầu ở **Yêu cầu đã lưu của bạn** để xem trạng thái (cập nhật mỗi 10 giây).
Mỗi biên tập viên có tối đa một yêu cầu đang chạy. Lịch sử hiển thị 100 yêu cầu gần nhất.

Các bước được lưu riêng: nghiên cứu/dàn ý → viết → kiểm tra. Bước lỗi được thử tối đa
4 lần, cách nhau 30/120/300 giây, chuyển model theo danh sách cấu hình. Lỗi cấu hình
400 hoặc quyền 401/403 dừng ngay. Mỗi lần gọi API tối đa 75 giây. Nếu hết lượt hoặc
worker bị ngắt quá lâu, yêu cầu chuyển trạng thái tạm dừng; bấm **Thử lại từ bước đã lưu**.
Không tự thử vô hạn và không tự đăng bài.

**Đưa bản nháp vào trình soạn thảo** là thao tác có xác nhận: thay nội dung form hiện tại,
chuyển sang Nháp, chưa lưu bài. Sau khi đọc và đối chiếu nguồn, đánh dấu đã đọc duyệt mới
có thể lưu trạng thái Đã đăng. Lưu nháp không cần xác nhận. Không có bảo đảm thứ hạng.

## Nguồn và chất lượng

- Chọn **Tra cứu nguồn bằng Google Search** khi dự án API có quyền dùng công cụ này.
  `GEMINI_GOOGLE_SEARCH` chỉ đặt giá trị mặc định; lựa chọn được lưu theo yêu cầu.
  Không tự bật billing, đổi tài khoản hay mua gói. Có thể có phí theo gói API.
- Chỉ nguồn từ `groundingMetadata` được ghi là nguồn tìm kiếm. AI không được tự tạo
  danh sách nguồn rồi coi đó là đã tra cứu. Gợi ý Google Search được hiển thị kèm kết quả.
- Khi không có nguồn tìm kiếm, ghi rõ đề xuất từ khóa theo ngữ nghĩa, không có volume/KD.
  Dữ kiện do biên tập viên nhập và URL nguồn cần được kiểm chứng thủ công.
- Kiểm tra từ khóa trong title/mở đầu, đề mục, mô tả, liên kết chưa có trong nguồn,
  số liệu cần đối chiếu, và độ trùng cụm 5 từ với 100 bài đã đăng gần nhất.
  Đây không phải kiểm tra đạo văn toàn Internet hoặc máy xác minh sự thật.
- Có thể chỉnh title, nội dung, metadata sau khi nhận bản nháp. Báo cáo gắn với phiên bản
  AI trả về; việc biên tập cuối cùng thuộc trách nhiệm người đọc duyệt.

## Vận hành

Chạy migration trước khi đưa giao diện mới vào phục vụ:

```sh
php artisan migrate --force
php artisan optimize
```

Worker riêng (không dùng worker mail để xử lý bài AI):

```sh
php artisan queue:work article_generation --queue=articles --sleep=3 --tries=20 --timeout=110 --max-time=3600 --memory=256
```

Connection `article_generation` dùng bảng jobs, queue `articles`, retry_after 180 giây,
lớn hơn timeout worker 110 giây và khóa chống xử lý đồng thời 140 giây. Có checkpoint
và run version để job cũ không tiếp tục sau khi người dùng thử lại. PHP CLI cần pcntl.

VPS hiện tại có mẫu unit `deploy/cars-articles.service`. Sau khi kiểm tra đường dẫn và user:

```sh
sudo cp deploy/cars-articles.service /etc/systemd/system/cars-articles.service
sudo systemctl daemon-reload
sudo systemctl enable --now cars-articles
sudo systemctl status cars-articles
```

Sau khi cập nhật code, `systemctl restart cars-articles` (chờ lượt đang chạy kết thúc).
Không dừng hoặc xóa queue mail. Khi queued quá 5 phút, giao diện gợi ý kiểm tra worker.
Chẩn đoán: `journalctl -u cars-articles -n 50`, bảng article_generations và log Laravel.
Không đưa API key vào log, bài viết hoặc Git. Mất mạng/quota vẫn có thể khiến yêu cầu
không hoàn thành; trạng thái lỗi và checkpoint giữ lại để xử lý tiếp.
