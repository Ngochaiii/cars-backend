# Quy trình local → Git → VPS

Source code có một nguồn chính là Git. Không sửa source trực tiếp trên VPS và không
rsync source từ working tree chưa commit. `.env`, database, media và cấu hình aaPanel
là dữ liệu riêng của từng môi trường, không commit hoặc đồng bộ đè qua Git.

1. Sửa code trên local, bổ sung kiểm thử và xem diff.
2. Commit thay đổi trên `main` (hoặc merge nhánh đã review vào main).
3. Chạy `./deploy.sh --go` từ local. Wrapper riêng máy gọi script đã version hóa
   `scripts/deploy.sh`: kiểm tra cây sạch, fetch origin, **chạy toàn bộ test local và
   build Vite**, kiểm tra HEAD không đổi, rồi mới push GitHub.
4. VPS kiểm tra cây sạch, xác minh checksum bản build, sao lưu SHA/build cũ trong
   `.git/deployments`, và **git pull --ff-only đúng SHA đã test**. Tiếp theo migration,
   cache, restart worker và kiểm tra HTTP trang chủ.
5. Kết thúc phải có HEAD local = origin/main = HEAD VPS và hai working tree sạch.

Lệnh trên máy khác:

```sh
DEPLOY_HOST=user@host DEPLOY_ROOT=/www/wwwroot/main ./scripts/deploy.sh --go
```

`DEPLOY_PHP`, `DEPLOY_USER`, `DEPLOY_SERVICE`, `DEPLOY_URL` có thể cấu hình theo máy.
Remote script cần quyền vận hành service và `runuser` (VPS hiện tại dùng SSH root).
Các bước chạy test cần MariaDB local với database riêng `catalog_cars_test` như
`phpunit.xml`; không dùng database production làm database kiểm thử.

Script dừng khi local/VPS bẩn, nhánh diverge, test/build lỗi hoặc composer.lock thay đổi.
Khi dependency thay đổi, chuẩn bị vendor production phù hợp và cập nhật quy trình
triển khai theo thay đổi đó; không bỏ qua chốt chặn để deploy một bộ vendor không khớp.
Build front-end được tạo từ commit đã test và tải riêng vì `public/build` không nằm trong Git.
Không cài dependency mới hoặc thay `.env` một cách ngầm định.

Nếu triển khai lỗi, kiểm tra output/receipt và SHA `*.previous`; chưa xem là hoàn tất.
Không tự rollback database: migration và nội dung có thể đã được ghi. Đối chiếu schema
trước khi khôi phục phiên bản code/build cũ. Bản sao/stash cũ giữ trên VPS phục vụ phục hồi.

## Local

Sau khi pull code: cài dependencies nếu cần, `php artisan migrate`, rồi `composer dev`.
Lệnh dev chạy cả worker mặc định lẫn worker `articles`; vì vậy local có cùng pipeline
Gemini chạy nền như VPS. Các yêu cầu AI thực tế vẫn phụ thuộc API key/hạn mức riêng.

Nội dung Limo Green được ghi trong `docs/content/limo-green-2026-11-13.json`; media được
lưu ngoài Git. Đồng bộ nội dung phải chọn riêng sản phẩm đó, không sao chép database
khách hàng, tài khoản, leads hoặc khóa API giữa hai môi trường.
