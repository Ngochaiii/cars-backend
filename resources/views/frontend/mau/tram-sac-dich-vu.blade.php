{{--
    BẢN CẮT TĨNH trang "Trạm sạc & dịch vụ" — chép 1:1 từ bản thiết kế trong
    resources/views/frontend/website 2 (màn hình #charging).

    ĐÂY LÀ BẢN ĐỐI CHIẾU, CHƯA NỐI BACKEND. Không có biến, không truy vấn,
    không route động: mọi chữ và số đều là dữ liệu mẫu của bản thiết kế.
    Mở /mau/tram-sac-dich-vu để so cạnh trang thật rồi ghép từng khối một.

    KHÔNG thêm `box-sizing: border-box`. Bản thiết kế dựng theo content-box:
    khối `max-width:1280px; padding:0 32px` ăn 1344px chứ không phải 1280px.
    Đặt border-box vào là mọi khối hụt đúng 64px và chữ xuống dòng khác bản
    gốc — đây đúng là chỗ bản cắt từng lệch 5% chiều ngang.

    Style để nguyên dạng inline như bản thiết kế xuất ra — cố tình không gom
    vào frontend.css, để bản này không trôi khỏi bản gốc khi CSS site đổi.

    Nút bấm ở đây là chữ tĩnh, không có JS: băng chuyền, tab, bộ chọn màu
    đứng yên ở trạng thái đầu.

    File này SINH RA từ DOM đã render, đừng sửa tay — sửa xong lần sau dump
    lại là mất.
--}}
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mẫu — Trạm sạc & dịch vụ</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@300;400;500;600;700;800&display=swap">

    <style>
        body { margin: 0; background: #fff; font-family: 'Be Vietnam Pro', system-ui, sans-serif; }
        @keyframes dv-pulse { 0%, 100% { opacity: 1 } 50% { opacity: .45 } }
        @keyframes dv-spin { from { transform: rotate(0deg) } to { transform: rotate(360deg) } }

    .sc-placeholder{background:color-mix(in srgb,currentColor 8%,transparent);
      border:1px solid color-mix(in srgb,currentColor 50%,transparent);
      border-radius:2px;box-sizing:border-box;overflow:hidden}
    @keyframes sc-shine{0%{background-position:100% 50%}100%{background-position:0% 50%}}
    html.sc-dc-streaming .sc-placeholder,
    html.sc-dc-streaming .sc-interp.sc-missing{position:relative;
      background:color-mix(in srgb,currentColor 5%,transparent);
      border-color:transparent}
    html.sc-dc-streaming .sc-placeholder::before,
    html.sc-dc-streaming .sc-interp.sc-missing::before{content:'';
      position:absolute;inset:0;pointer-events:none;
      background:linear-gradient(90deg,rgba(217,119,87,0) 25%,rgba(247,225,211,.95) 37%,rgba(217,119,87,0) 63%);
      background-size:400% 100%;animation:sc-shine 1.4s ease infinite}
    html.sc-dc-streaming .sc-placeholder:nth-child(n+9 of .sc-placeholder)::before,
    html.sc-dc-streaming .sc-interp.sc-missing:nth-child(n+9 of .sc-interp.sc-missing)::before{animation:none;
      background:color-mix(in srgb,currentColor 8%,transparent)}
    .sc-placeholder-error{padding:4px 8px;font:11px/1.4 ui-monospace,monospace;
      color:color-mix(in srgb,currentColor 70%,transparent);word-break:break-word}
    .sc-interp.sc-missing{display:inline-block;width:2em;height:1em;overflow:hidden;
      vertical-align:text-bottom;background:rgba(255,255,255,.3);border:1px solid rgba(0,0,0,.5);
      border-radius:2px;box-sizing:border-box;color:transparent;
      user-select:none}
    .sc-interp.sc-unresolved{font-family:ui-monospace,monospace;font-size:.85em;
      color:color-mix(in srgb,currentColor 50%,transparent);
      background:color-mix(in srgb,currentColor 10%,transparent);border-radius:3px;
      padding:0 3px}
    .sc-host.sc-has-error{position:relative}
    .sc-logic-error{position:absolute;top:8px;left:8px;z-index:2147483647;max-width:60ch;
      padding:6px 10px;background:#b00020;color:#fff;font:12px/1.4 ui-monospace,monospace;
      border-radius:4px;white-space:pre-wrap;pointer-events:none}
    /* Mirrors PRINT_BASELINE_CSS in apps/web deck-stage-export.ts — keep both
       in sync until dc-runtime regains a build step. */
    @media print {
      @page { margin: 0.5cm; }
      figure, table { break-inside: avoid; }
      #dc-root, #dc-root > .sc-host { height: auto; }
      *, *::before, *::after {
        print-color-adjust: exact; -webkit-print-color-adjust: exact;
        backdrop-filter: none !important; -webkit-backdrop-filter: none !important;
        animation-delay: -99s !important; animation-duration: .001s !important;
        animation-iteration-count: 1 !important; animation-fill-mode: both !important;
        animation-play-state: running !important; transition-duration: 0s !important;
      }
    }
  
x-dc{display:none!important}
.fx{display:flex}.col{display:flex;flex-direction:column}.grid{display:grid}.ac{align-items:center}.jc{justify-content:center}.jb{justify-content:space-between}.f1{flex:1}.noshrink{flex-shrink:0}.wrap{flex-wrap:wrap}.fw5{font-weight:500}.fw6{font-weight:600}.fw7{font-weight:700}.fw8{font-weight:800}.fs11{font-size:11px}.fs12{font-size:12px}.fs13{font-size:13px}.fs14{font-size:14px}.fs15{font-size:15px}.fs16{font-size:16px}.fs20{font-size:20px}.fs22{font-size:22px}.upper{text-transform:uppercase}.tc{text-align:center}.nowrap{white-space:nowrap}.gap8{gap:8px}.gap10{gap:10px}.gap12{gap:12px}.gap16{gap:16px}.gap24{gap:24px}.m0{margin:0}.mt8{margin-top:8px}.mt12{margin-top:12px}.mt16{margin-top:16px}.mb8{margin-bottom:8px}.mb12{margin-bottom:12px}.mb16{margin-bottom:16px}.posrel{position:relative}.posabs{position:absolute}.round{border-radius:50%}.ohide{overflow:hidden}.bbox{box-sizing:border-box}.pointer{cursor:pointer}.w100{width:100%}.b0{border:none}

/* vietnamese */
    </style>
</head>
<body>
<div style="min-height: 100vh; background: rgb(255, 255, 255); min-width: 1320px;">
  <div style="background: rgb(17, 17, 17); color: rgb(255, 255, 255); text-align: center; padding: 10px 24px; font-size: 13px; cursor: pointer;">
    Tháng 7: trả góp 0% lãi suất 24 tháng cho VF 6 &amp; VF 7 — <span style="text-decoration: underline; font-weight: 600;">Xem chi tiết</span>
  </div><header style="border-bottom: 1px solid rgb(236, 236, 234); background: rgb(255, 255, 255); position: sticky; top: 0px; z-index: 50;">
  <div style="max-width: 1280px; margin: 0px auto; display: flex; align-items: center; justify-content: space-between; padding: 0px 32px; height: 76px;">
    <div style="display: flex; align-items: baseline; gap: 10px; cursor: pointer;">
      <span style="font-size: 19px; font-weight: 800; letter-spacing: 0.04em; color: rgb(17, 17, 17);">VINFAST</span>
      <span style="font-size: 12px; font-weight: 500; letter-spacing: 0.22em; color: rgb(138, 138, 134); text-transform: uppercase;">Bắc Giang</span>
    </div>
    <nav style="display: flex; gap: 28px; font-size: 14px; font-weight: 500; white-space: nowrap;">
      
        <span class="scp0" style="cursor: pointer; color: rgb(58, 58, 56); border-bottom: 2px solid transparent; padding-bottom: 2px;"><span class="sc-interp">Ô tô</span></span>
      
        <span class="scp0" style="cursor: pointer; color: rgb(58, 58, 56); border-bottom: 2px solid transparent; padding-bottom: 2px;"><span class="sc-interp">Phụ kiện</span></span>
      
        <span class="scp0" style="cursor: pointer; color: rgb(17, 17, 17); border-bottom: 2px solid rgb(17, 17, 17); padding-bottom: 2px;"><span class="sc-interp">Trạm sạc &amp; Dịch vụ</span></span>
      
        <span class="scp0" style="cursor: pointer; color: rgb(58, 58, 56); border-bottom: 2px solid transparent; padding-bottom: 2px;"><span class="sc-interp">Tin tức</span></span>
      
        <span class="scp0" style="cursor: pointer; color: rgb(58, 58, 56); border-bottom: 2px solid transparent; padding-bottom: 2px;"><span class="sc-interp">Về chúng tôi</span></span>
      
    </nav>
    <span class="scp1" style="flex: 0 0 auto; display: inline-flex; align-items: center; height: 42px; padding: 0px 22px; background: rgb(17, 17, 17); color: rgb(255, 255, 255); font-size: 13.5px; font-weight: 600; border-radius: 2px; cursor: pointer; white-space: nowrap;">Đăng ký lái thử</span>
  </div></header>
  <div style="max-width: 1280px; margin: 0px auto; padding: 56px 32px;">
    <div style="font-family: ui-monospace, Menlo, monospace; font-size: 11px; letter-spacing: 0.2em; color: rgb(152, 152, 143); text-transform: uppercase; margin-bottom: 14px;">Trạm sạc &amp; Dịch vụ</div>
    <h1 style="margin: 0px 0px 36px; font-size: 44px; font-weight: 700; letter-spacing: -0.02em; color: rgb(17, 17, 17); max-width: 640px;">Sạc và bảo dưỡng, ngay trong tỉnh</h1>
    <div style="display: grid; grid-template-columns: 1fr 400px; gap: 24px;">
      <div style="height: 520px; border-radius: 4px; background: repeating-linear-gradient(45deg, rgb(236, 236, 234) 0px, rgb(236, 236, 234) 14px, rgb(245, 245, 243) 14px, rgb(245, 245, 243) 28px); display: flex; align-items: center; justify-content: center;">
        <span style="font-family: ui-monospace, Menlo, monospace; font-size: 12px; color: rgb(152, 152, 143); letter-spacing: 0.08em;">[ bản đồ trạm sạc tỉnh Bắc Giang ]</span>
      </div>
      <div style="display: flex; flex-direction: column; gap: 12px;">
        
          <div class="scpk" style="border: 1px solid rgb(236, 236, 234); border-radius: 4px; padding: 20px 22px; background: rgb(255, 255, 255);">
            <div style="display: flex; justify-content: space-between; align-items: baseline;"><span style="font-size: 15px; font-weight: 600; color: rgb(17, 17, 17);"><span class="sc-interp">Vincom Bắc Giang</span></span><span style="font-size: 12px; font-weight: 600; color: rgb(44, 122, 75);"><span class="sc-interp">Còn 6/8 cổng</span></span></div>
            <div style="font-size: 13px; color: rgb(138, 138, 134); margin-top: 4px;"><span class="sc-interp">DC 150 kW · 1,2 km · Mở 24/7</span></div>
          </div>
        
          <div class="scpk" style="border: 1px solid rgb(236, 236, 234); border-radius: 4px; padding: 20px 22px; background: rgb(255, 255, 255);">
            <div style="display: flex; justify-content: space-between; align-items: baseline;"><span style="font-size: 15px; font-weight: 600; color: rgb(17, 17, 17);"><span class="sc-interp">Big C Bắc Giang</span></span><span style="font-size: 12px; font-weight: 600; color: rgb(44, 122, 75);"><span class="sc-interp">Còn 3/6 cổng</span></span></div>
            <div style="font-size: 13px; color: rgb(138, 138, 134); margin-top: 4px;"><span class="sc-interp">DC 60 kW · 2,8 km · 8:00–22:00</span></div>
          </div>
        
          <div class="scpk" style="border: 1px solid rgb(236, 236, 234); border-radius: 4px; padding: 20px 22px; background: rgb(255, 255, 255);">
            <div style="display: flex; justify-content: space-between; align-items: baseline;"><span style="font-size: 15px; font-weight: 600; color: rgb(17, 17, 17);"><span class="sc-interp">BX Khách Bắc Giang</span></span><span style="font-size: 12px; font-weight: 600; color: rgb(180, 83, 9);"><span class="sc-interp">Còn 1/10 cổng</span></span></div>
            <div style="font-size: 13px; color: rgb(138, 138, 134); margin-top: 4px;"><span class="sc-interp">DC 250 kW · 3,5 km · Mở 24/7</span></div>
          </div>
        
          <div class="scpk" style="border: 1px solid rgb(236, 236, 234); border-radius: 4px; padding: 20px 22px; background: rgb(255, 255, 255);">
            <div style="display: flex; justify-content: space-between; align-items: baseline;"><span style="font-size: 15px; font-weight: 600; color: rgb(17, 17, 17);"><span class="sc-interp">KCN Vân Trung</span></span><span style="font-size: 12px; font-weight: 600; color: rgb(44, 122, 75);"><span class="sc-interp">Còn 8/12 cổng</span></span></div>
            <div style="font-size: 13px; color: rgb(138, 138, 134); margin-top: 4px;"><span class="sc-interp">AC 11 kW + DC 60 kW · 9,4 km</span></div>
          </div>
        
        <span class="scpa" style="margin-top: 6px; display: inline-flex; align-items: center; justify-content: center; height: 46px; border: 1px solid rgb(214, 214, 210); color: rgb(17, 17, 17); font-size: 13.5px; font-weight: 600; border-radius: 2px; cursor: pointer;">Xem cả 40+ trạm trong tỉnh</span>
      </div>
    </div>
  </div>
  <div style="background: rgb(250, 250, 248); border-top: 1px solid rgb(236, 236, 234);">
    <div style="max-width: 1280px; margin: 0px auto; padding: 64px 32px;">
      <h2 style="margin: 0px 0px 32px; font-size: 30px; font-weight: 700; color: rgb(17, 17, 17);">Dịch vụ tại showroom</h2>
      <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px;">
        <div style="background: rgb(255, 255, 255); border: 1px solid rgb(236, 236, 234); border-radius: 4px; padding: 28px;"><div style="font-size: 17px; font-weight: 600; color: rgb(17, 17, 17); margin-bottom: 8px;">Bảo dưỡng định kỳ</div><p style="margin: 0px 0px 18px; font-size: 13.5px; line-height: 1.65; color: rgb(107, 107, 104);">Đặt lịch trước, nhận xe trong ngày. Xe thay thế miễn phí cho bảo dưỡng trên 4 giờ.</p><span style="font-size: 13px; font-weight: 600; color: rgb(17, 17, 17); border-bottom: 1px solid rgb(17, 17, 17); padding-bottom: 2px; cursor: pointer;">Đặt lịch →</span></div>
        <div style="background: rgb(255, 255, 255); border: 1px solid rgb(236, 236, 234); border-radius: 4px; padding: 28px;"><div style="font-size: 17px; font-weight: 600; color: rgb(17, 17, 17); margin-bottom: 8px;">Lắp sạc tại nhà</div><p style="margin: 0px 0px 18px; font-size: 13.5px; line-height: 1.65; color: rgb(107, 107, 104);">Khảo sát miễn phí, lắp đặt bộ sạc AC 7,4 kW đạt chuẩn an toàn trong 1 buổi.</p><span style="font-size: 13px; font-weight: 600; color: rgb(17, 17, 17); border-bottom: 1px solid rgb(17, 17, 17); padding-bottom: 2px; cursor: pointer;">Đăng ký khảo sát →</span></div>
        <div style="background: rgb(255, 255, 255); border: 1px solid rgb(236, 236, 234); border-radius: 4px; padding: 28px;"><div style="font-size: 17px; font-weight: 600; color: rgb(17, 17, 17); margin-bottom: 8px;">Cứu hộ 24/7</div><p style="margin: 0px 0px 18px; font-size: 13.5px; line-height: 1.65; color: rgb(107, 107, 104);">Đội cứu hộ lưu động toàn tỉnh Bắc Giang, có mặt trung bình trong 45 phút.</p><span style="font-size: 13px; font-weight: 600; color: rgb(17, 17, 17); border-bottom: 1px solid rgb(17, 17, 17); padding-bottom: 2px; cursor: pointer;">Hotline cứu hộ →</span></div>
      </div>
    </div>
  </div><div style="background: rgb(17, 17, 17);">
  <div style="max-width: 1280px; margin: 0px auto; padding: 56px 32px; display: flex; align-items: center; justify-content: space-between; gap: 48px;">
    <div>
      <div style="font-size: 24px; font-weight: 700; color: rgb(255, 255, 255); margin-bottom: 6px;">Đăng ký nhận thông tin</div>
      <div style="font-size: 13.5px; color: rgb(168, 168, 164);">Chương trình khuyến mãi và tin dịch vụ từ đại lý — 1–2 email mỗi tháng.</div>
    </div>
    
      <div style="display: flex; gap: 10px; flex: 0 0 auto;">
        <input placeholder="Nhập email của bạn" class="scpm" value="" style="width: 320px; box-sizing: border-box; height: 50px; border: 1px solid rgb(58, 58, 56); background: rgb(28, 28, 26); border-radius: 2px; padding: 0px 18px; font-size: 14px; color: rgb(255, 255, 255); font-family: inherit; outline: none;">
        <span class="scpn" style="display: inline-flex; align-items: center; height: 50px; padding: 0px 28px; background: rgb(255, 255, 255); color: rgb(17, 17, 17); font-size: 14px; font-weight: 600; border-radius: 2px; cursor: pointer;">Đăng ký</span>
      </div>
    
    
  </div></div><footer style="background: rgb(250, 250, 248); border-top: 1px solid rgb(236, 236, 234);">
  <div style="max-width: 1280px; margin: 0px auto; padding: 48px 32px; display: flex; justify-content: space-between; gap: 40px;">
    <div>
      <div style="font-size: 16px; font-weight: 800; letter-spacing: 0.04em; color: rgb(17, 17, 17); margin-bottom: 8px;">VINFAST <span style="font-weight: 500; color: rgb(138, 138, 134);">BẮC GIANG</span></div>
      <p style="margin: 0px; font-size: 13px; color: rgb(138, 138, 134); line-height: 1.7;">Đại lý ủy quyền · Đường Xương Giang, TP. Bắc Giang<br>Mở cửa 8:00–19:00 hằng ngày</p>
    </div>
    <div style="display: flex; gap: 56px; font-size: 13px; color: rgb(107, 107, 104); line-height: 2.1;">
      <div><b style="color: rgb(17, 17, 17); font-weight: 600;">Sản phẩm</b><br><span style="cursor: pointer;">Ô tô điện</span><br><span style="cursor: pointer;">Phụ kiện xe</span></div>
      <div><b style="color: rgb(17, 17, 17); font-weight: 600;">Dịch vụ</b><br><span style="cursor: pointer;">Đặt cọc online</span><br><span style="cursor: pointer;">Lái thử</span><br><span style="cursor: pointer;">Trạm sạc &amp; bảo dưỡng</span></div>
      <div><b style="color: rgb(17, 17, 17); font-weight: 600;">Đại lý</b><br><span style="cursor: pointer;">Tin tức &amp; ưu đãi</span><br><span style="cursor: pointer;">Về chúng tôi</span><br><span style="cursor: pointer;">Điều khoản &amp; chính sách</span></div>
    </div>
  </div>
  <div style="border-top: 1px solid rgb(236, 236, 234);">
    <div style="max-width: 1280px; margin: 0px auto; padding: 20px 32px; display: flex; justify-content: space-between; align-items: center; font-size: 12px; color: rgb(152, 152, 143);">
      <span>© 2026 Đại lý VinFast Bắc Giang. MST 0108xxxxxx. Người đại diện: Nguyễn Văn B.</span>
      <span>Hotline 0204 xxx xxx · Zalo OA · Facebook</span>
    </div>
  </div></footer></div>
</body>
</html>
