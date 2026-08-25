{{--
    BẢN CẮT TĨNH trang "Trang chủ" — chép 1:1 từ bản thiết kế trong
    resources/views/frontend/website 2 (màn hình #home (mặc định)).

    ĐÂY LÀ BẢN ĐỐI CHIẾU, CHƯA NỐI BACKEND. Không có biến, không truy vấn,
    không route động: mọi chữ và số đều là dữ liệu mẫu của bản thiết kế.
    Mở /mau/trang-chu để so cạnh trang thật rồi ghép từng khối một.

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
    <title>Mẫu — Trang chủ</title>

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
      
        <span class="scp0" style="cursor: pointer; color: rgb(58, 58, 56); border-bottom: 2px solid transparent; padding-bottom: 2px;"><span class="sc-interp">Trạm sạc &amp; Dịch vụ</span></span>
      
        <span class="scp0" style="cursor: pointer; color: rgb(58, 58, 56); border-bottom: 2px solid transparent; padding-bottom: 2px;"><span class="sc-interp">Tin tức</span></span>
      
        <span class="scp0" style="cursor: pointer; color: rgb(58, 58, 56); border-bottom: 2px solid transparent; padding-bottom: 2px;"><span class="sc-interp">Về chúng tôi</span></span>
      
    </nav>
    <span class="scp1" style="flex: 0 0 auto; display: inline-flex; align-items: center; height: 42px; padding: 0px 22px; background: rgb(17, 17, 17); color: rgb(255, 255, 255); font-size: 13.5px; font-weight: 600; border-radius: 2px; cursor: pointer; white-space: nowrap;">Đăng ký lái thử</span>
  </div></header>

  
  <div style="position: relative; height: 720px; background: rgb(38, 38, 42); overflow: hidden; transition: background 0.6s;">
    <div style="position: absolute; inset: 0px; background: linear-gradient(100deg, rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.15));"></div>
    <div style="position: relative; z-index: 1; height: 100%; max-width: 1360px; margin: 0px auto; padding: 0px 64px; display: flex; flex-direction: column; justify-content: center; align-items: flex-start; text-align: left; opacity: 1; transition: opacity 0.6s;">
      <div style="font-size: 12px; font-weight: 600; letter-spacing: 0.26em; text-transform: uppercase; color: rgba(255, 255, 255, 0.72); margin-bottom: 22px;"><span class="sc-interp">VF 7 · Bản lĩnh phong cách</span></div>
      <h1 style="margin: 0px 0px 20px; font-size: 62px; line-height: 1.08; font-weight: 500; letter-spacing: -0.015em; color: rgb(255, 255, 255); max-width: 760px;"><span class="sc-interp">Khi phong cách trở thành dấu ấn</span></h1>
      <p style="margin: 0px 0px 36px; font-size: 18px; line-height: 1.55; color: rgba(255, 255, 255, 0.72); max-width: 520px;"><span class="sc-interp">Thiết kế hoàn toàn mới, công nghệ dẫn đầu và trải nghiệm chuẩn 5 sao cho gia đình Việt.</span></p>
      <div style="display: flex; gap: 14px;">
        <span class="scp2" style="display: inline-flex; align-items: center; height: 52px; padding: 0px 38px; background: rgb(255, 255, 255); color: rgb(17, 17, 17); font-size: 13px; font-weight: 700; letter-spacing: 0.14em; text-transform: uppercase; white-space: nowrap; cursor: pointer; transition: opacity 0.2s, transform 0.2s;"><span class="sc-interp">Khám phá VF 7</span></span>
        <span class="scp3" style="display: inline-flex; align-items: center; height: 52px; padding: 0px 38px; border: 1px solid rgba(255, 255, 255, 0.5); color: rgb(255, 255, 255); font-size: 13px; font-weight: 700; letter-spacing: 0.14em; text-transform: uppercase; white-space: nowrap; cursor: pointer; transition: background 0.2s;"><span class="sc-interp">Đặt cọc</span></span>
      </div>
      <div style="margin-top: 28px; font-size: 11.5px; color: rgba(255, 255, 255, 0.72);"><span class="sc-interp">Hình ảnh mang tính minh họa. Giá và ưu đãi áp dụng theo điều khoản của đại lý.</span></div>
    </div>
    <div style="position: absolute; z-index: 2; left: 64px; bottom: 36px; display: flex; align-items: center; gap: 18px;">
      
        <span style="width: 44px; height: 2px; background: rgb(255, 255, 255); cursor: pointer; transition: width 0.3s, background 0.3s; display: inline-block;"></span>
      
        <span style="width: 18px; height: 2px; background: rgba(255, 255, 255, 0.72); cursor: pointer; transition: width 0.3s, background 0.3s; display: inline-block;"></span>
      
        <span style="width: 18px; height: 2px; background: rgba(255, 255, 255, 0.72); cursor: pointer; transition: width 0.3s, background 0.3s; display: inline-block;"></span>
      
      <span style="font-size: 11.5px; letter-spacing: 0.14em; color: rgba(255, 255, 255, 0.72); margin-left: 6px;"><span class="sc-interp">01 / 03</span></span>
    </div>
    <div style="position: absolute; z-index: 2; right: 64px; bottom: 30px; display: flex; gap: 10px;">
      <span class="scp4" style="width: 44px; height: 44px; border: 1px solid rgba(255, 255, 255, 0.5); display: flex; align-items: center; justify-content: center; font-size: 17px; color: rgb(255, 255, 255); cursor: pointer;">‹</span>
      <span class="scp4" style="width: 44px; height: 44px; border: 1px solid rgba(255, 255, 255, 0.5); display: flex; align-items: center; justify-content: center; font-size: 17px; color: rgb(255, 255, 255); cursor: pointer;">›</span>
    </div>
    <div style="position: absolute; z-index: 1; right: 64px; top: 50%; transform: translateY(-50%); font-family: ui-monospace, Menlo, monospace; font-size: 11px; color: rgba(255, 255, 255, 0.72);">[ <span class="sc-interp">VF 7 trên đường ven biển lúc hoàng hôn</span> ]</div>
  </div>

  
  <div style="border-bottom: 1px solid rgb(229, 229, 229);">
    <div style="max-width: 1360px; margin: 0px auto; padding: 44px 64px;">
      <div style="font-size: 11.5px; font-weight: 700; letter-spacing: 0.24em; text-transform: uppercase; color: rgb(102, 102, 102); margin-bottom: 26px;">Công cụ mua xe</div>
      <div style="display: grid; grid-template-columns: repeat(5, 1fr);">
        
          <div style="padding: 0px 28px 0px 0px; border-right: 1px solid rgb(229, 229, 229); cursor: pointer;">
            <div style="font-size: 17px; font-weight: 600; color: rgb(17, 17, 17); margin-bottom: 6px;"><span class="sc-interp">Đặt cọc online</span></div>
            <div style="font-size: 13px; line-height: 1.5; color: rgb(102, 102, 102);"><span class="sc-interp">Giữ suất xe chỉ với 15 triệu, hoàn cọc trong 7 ngày.</span></div>
          </div>
        
          <div style="padding: 0px 28px 0px 0px; border-right: 1px solid rgb(229, 229, 229); cursor: pointer;">
            <div style="font-size: 17px; font-weight: 600; color: rgb(17, 17, 17); margin-bottom: 6px;"><span class="sc-interp">Đăng ký lái thử</span></div>
            <div style="font-size: 13px; line-height: 1.5; color: rgb(102, 102, 102);"><span class="sc-interp">Lái thử tận nhà trong bán kính 30km.</span></div>
          </div>
        
          <div style="padding: 0px 28px 0px 0px; border-right: 1px solid rgb(229, 229, 229); cursor: pointer;">
            <div style="font-size: 17px; font-weight: 600; color: rgb(17, 17, 17); margin-bottom: 6px;"><span class="sc-interp">Xe sẵn giao ngay</span></div>
            <div style="font-size: 13px; line-height: 1.5; color: rgb(102, 102, 102);"><span class="sc-interp">Xem các mẫu còn xe tại showroom Bắc Giang.</span></div>
          </div>
        
          <div style="padding: 0px 28px 0px 0px; border-right: 1px solid rgb(229, 229, 229); cursor: pointer;">
            <div style="font-size: 17px; font-weight: 600; color: rgb(17, 17, 17); margin-bottom: 6px;"><span class="sc-interp">Ưu đãi hiện hành</span></div>
            <div style="font-size: 13px; line-height: 1.5; color: rgb(102, 102, 102);"><span class="sc-interp">Trả góp 0% và các chương trình trong tháng.</span></div>
          </div>
        
          <div style="padding: 0px 28px 0px 0px; border-right-width: medium; border-right-style: none; border-right-color: currentcolor; cursor: pointer;">
            <div style="font-size: 17px; font-weight: 600; color: rgb(17, 17, 17); margin-bottom: 6px;"><span class="sc-interp">Trạm sạc &amp; dịch vụ</span></div>
            <div style="font-size: 13px; line-height: 1.5; color: rgb(102, 102, 102);"><span class="sc-interp">Bản đồ trạm sạc và đặt lịch bảo dưỡng.</span></div>
          </div>
        
      </div>
    </div>
  </div>

  
  <div style="max-width: 1360px; margin: 0px auto; padding: 104px 64px 96px;">
    <h2 style="margin: 0px 0px 40px; font-size: 44px; font-weight: 500; letter-spacing: -0.015em; color: rgb(17, 17, 17); text-align: center;">Khám phá dải sản phẩm</h2>
    <div style="display: flex; justify-content: center; gap: 64px; border-bottom: 1px solid rgb(229, 229, 229); margin-bottom: 12px;">
      
        <span style="display: inline-flex; align-items: center; gap: 10px; padding: 0px 0px 18px; font-size: 13px; font-weight: 600; letter-spacing: 0.14em; text-transform: uppercase; color: rgb(17, 17, 17); border-bottom: 2px solid rgb(17, 17, 17); margin-bottom: -1px; cursor: pointer; white-space: nowrap; transition: color 0.25s;"><span class="sc-interp">SUV gia đình</span><span style="display: inline-flex; align-items: center; justify-content: center; width: 22px; height: 22px; border: 1px solid rgb(17, 17, 17); border-radius: 999px; font-size: 11px; letter-spacing: 0px; color: rgb(17, 17, 17);"><span class="sc-interp">3</span></span></span>
      
        <span style="display: inline-flex; align-items: center; gap: 10px; padding: 0px 0px 18px; font-size: 13px; font-weight: 600; letter-spacing: 0.14em; text-transform: uppercase; color: rgb(138, 138, 138); border-bottom: 2px solid transparent; margin-bottom: -1px; cursor: pointer; white-space: nowrap; transition: color 0.25s;"><span class="sc-interp">Đô thị</span><span style="display: inline-flex; align-items: center; justify-content: center; width: 22px; height: 22px; border: 1px solid rgb(208, 208, 204); border-radius: 999px; font-size: 11px; letter-spacing: 0px; color: rgb(138, 138, 138);"><span class="sc-interp">2</span></span></span>
      
        <span style="display: inline-flex; align-items: center; gap: 10px; padding: 0px 0px 18px; font-size: 13px; font-weight: 600; letter-spacing: 0.14em; text-transform: uppercase; color: rgb(138, 138, 138); border-bottom: 2px solid transparent; margin-bottom: -1px; cursor: pointer; white-space: nowrap; transition: color 0.25s;"><span class="sc-interp">7 chỗ</span><span style="display: inline-flex; align-items: center; justify-content: center; width: 22px; height: 22px; border: 1px solid rgb(208, 208, 204); border-radius: 999px; font-size: 11px; letter-spacing: 0px; color: rgb(138, 138, 138);"><span class="sc-interp">1</span></span></span>
      
        <span style="display: inline-flex; align-items: center; gap: 10px; padding: 0px 0px 18px; font-size: 13px; font-weight: 600; letter-spacing: 0.14em; text-transform: uppercase; color: rgb(138, 138, 138); border-bottom: 2px solid transparent; margin-bottom: -1px; cursor: pointer; white-space: nowrap; transition: color 0.25s;"><span class="sc-interp">Tất cả</span><span style="display: inline-flex; align-items: center; justify-content: center; width: 22px; height: 22px; border: 1px solid rgb(208, 208, 204); border-radius: 999px; font-size: 11px; letter-spacing: 0px; color: rgb(138, 138, 138);"><span class="sc-interp">6</span></span></span>
      
    </div>

    <div style="position: relative; height: 440px; overflow: hidden;">
      <div style="position: absolute; inset: 0px; display: flex; align-items: center; justify-content: center; gap: 0px; opacity: 1; transition: opacity 0.3s;">
        
          <div style="width: 300px; height: 220px; flex: 0 0 auto; background: repeating-linear-gradient(45deg, rgb(239, 239, 236) 0px, rgb(239, 239, 236) 14px, rgb(247, 247, 245) 14px, rgb(247, 247, 245) 28px); display: flex; align-items: center; justify-content: center; transform: translateX(60px); opacity: 0.55;"><span style="font-family: ui-monospace, Menlo, monospace; font-size: 10px; color: rgb(169, 169, 161);">[ <span class="sc-interp">VF 8</span> ]</span></div>
        
        <div style="width: 660px; height: 400px; flex: 0 0 auto; position: relative; z-index: 2; background: repeating-linear-gradient(45deg, rgb(233, 233, 230) 0px, rgb(233, 233, 230) 16px, rgb(244, 244, 242) 16px, rgb(244, 244, 242) 32px); display: flex; align-items: center; justify-content: center; cursor: pointer;"><span style="font-family: ui-monospace, Menlo, monospace; font-size: 11px; color: rgb(152, 152, 143);">[ <span class="sc-interp">VF 6</span> — ảnh sản phẩm nền trắng ]</span></div>
        
          <div style="width: 300px; height: 220px; flex: 0 0 auto; background: repeating-linear-gradient(45deg, rgb(239, 239, 236) 0px, rgb(239, 239, 236) 14px, rgb(247, 247, 245) 14px, rgb(247, 247, 245) 28px); display: flex; align-items: center; justify-content: center; transform: translateX(-60px); opacity: 0.55;"><span style="font-family: ui-monospace, Menlo, monospace; font-size: 10px; color: rgb(169, 169, 161);">[ <span class="sc-interp">VF 7</span> ]</span></div>
        
      </div>
      
      <span class="scp5" style="position: absolute; z-index: 3; left: 0px; top: 50%; transform: translateY(-50%); width: 52px; height: 52px; border-radius: 999px; background: rgb(17, 17, 17); color: rgb(255, 255, 255); display: flex; align-items: center; justify-content: center; font-size: 19px; cursor: pointer; transition: opacity 0.2s;">‹</span>
      <span class="scp5" style="position: absolute; z-index: 3; right: 0px; top: 50%; transform: translateY(-50%); width: 52px; height: 52px; border-radius: 999px; background: rgb(17, 17, 17); color: rgb(255, 255, 255); display: flex; align-items: center; justify-content: center; font-size: 19px; cursor: pointer; transition: opacity 0.2s;">›</span>
      
    </div>

    <div style="border-top: 1px solid rgb(229, 229, 229); padding-top: 36px; text-align: center; opacity: 1; transition: opacity 0.3s;">
      <h3 style="margin: 0px 0px 10px; font-size: 52px; font-weight: 400; letter-spacing: -0.02em; color: rgb(17, 17, 17);"><span class="sc-interp">VF 6</span></h3>
      <div style="font-size: 15px; color: rgb(102, 102, 102); margin-bottom: 20px;"><span class="sc-interp">SUV hạng B</span> · <span class="sc-interp">5</span> chỗ · <span class="sc-interp">480 km</span> mỗi lần sạc</div>
      <span style="display: inline-flex; align-items: center; gap: 8px; font-size: 12.5px; font-weight: 700; letter-spacing: 0.14em; text-transform: uppercase; color: rgb(17, 17, 17); cursor: pointer; margin-bottom: 32px;">Xem ưu đãi ›</span>
      <div style="border-top: 1px solid rgb(229, 229, 229); padding-top: 32px; display: flex; justify-content: center; gap: 16px;">
        <span class="scp6" style="display: inline-flex; align-items: center; justify-content: center; width: 260px; height: 56px; background: rgb(17, 17, 17); color: rgb(255, 255, 255); font-size: 12.5px; font-weight: 700; letter-spacing: 0.14em; text-transform: uppercase; white-space: nowrap; cursor: pointer;">Khám phá</span>
        <span class="scp7" style="display: inline-flex; align-items: center; justify-content: center; width: 260px; height: 56px; border: 1px solid rgb(17, 17, 17); color: rgb(17, 17, 17); font-size: 12.5px; font-weight: 700; letter-spacing: 0.14em; text-transform: uppercase; white-space: nowrap; cursor: pointer; box-sizing: border-box;">Đặt cọc</span>
      </div>
      <div style="margin-top: 24px; font-size: 12px; color: rgb(138, 138, 134);">Giá từ <span class="sc-interp">694 triệu</span> · <span class="sc-interp">01 / 03</span></div>
    </div>
  </div>

  
  <div style="position: relative; height: 560px; background: rgb(28, 28, 26); overflow: hidden;">
    <div style="position: absolute; inset: 0px; background: repeating-linear-gradient(45deg, rgb(35, 35, 32) 0px, rgb(35, 35, 32) 18px, rgb(28, 28, 26) 18px, rgb(28, 28, 26) 36px);"></div>
    <div style="position: relative; z-index: 1; height: 100%; max-width: 1360px; margin: 0px auto; padding: 0px 64px; display: flex; flex-direction: column; justify-content: center;">
      <div style="font-size: 11.5px; font-weight: 700; letter-spacing: 0.26em; text-transform: uppercase; color: rgba(255, 255, 255, 0.6); margin-bottom: 20px;">Ưu đãi mùa hè · đến 31/08</div>
      <h2 style="margin: 0px 0px 18px; font-size: 48px; line-height: 1.1; font-weight: 500; letter-spacing: -0.015em; color: rgb(255, 255, 255); max-width: 640px;">Trả góp 0% lãi suất 24 tháng cho VF 6 và VF 7</h2>
      <p style="margin: 0px 0px 34px; font-size: 17px; line-height: 1.55; color: rgba(255, 255, 255, 0.72); max-width: 520px;">Kèm gói lắp sạc tại nhà miễn phí công lắp đặt cho khách đặt cọc tại đại lý Bắc Giang.</p>
      <div style="display: flex; gap: 14px;">
        <span class="scp8" style="display: inline-flex; align-items: center; height: 52px; padding: 0px 36px; background: rgb(255, 255, 255); color: rgb(17, 17, 17); font-size: 12.5px; font-weight: 700; letter-spacing: 0.14em; text-transform: uppercase; white-space: nowrap; cursor: pointer;">Xem ưu đãi</span>
        <span class="scp3" style="display: inline-flex; align-items: center; height: 52px; padding: 0px 36px; border: 1px solid rgba(255, 255, 255, 0.5); color: rgb(255, 255, 255); font-size: 12.5px; font-weight: 700; letter-spacing: 0.14em; text-transform: uppercase; white-space: nowrap; cursor: pointer;">Đặt cọc ngay</span>
      </div>
    </div>
  </div>

  
  <div style="max-width: 1360px; margin: 0px auto; padding: 104px 64px; display: grid; grid-template-columns: 1fr 1fr; gap: 72px; align-items: center;">
    <div>
      <div style="font-size: 11.5px; font-weight: 700; letter-spacing: 0.24em; text-transform: uppercase; color: rgb(102, 102, 102); margin-bottom: 18px;">Pin &amp; trạm sạc</div>
      <h2 style="margin: 0px 0px 18px; font-size: 42px; line-height: 1.12; font-weight: 500; letter-spacing: -0.015em; color: rgb(17, 17, 17);">Sạc đầy trong lúc bạn đi chợ</h2>
      <p style="margin: 0px 0px 30px; font-size: 17px; line-height: 1.65; color: rgb(102, 102, 102); max-width: 460px;">Hơn 40 điểm sạc trong tỉnh Bắc Giang, sạc nhanh 10–70% dưới 30 phút, cùng dịch vụ khảo sát và lắp đặt sạc tại nhà miễn phí công lắp.</p>
      <span class="scp6" style="display: inline-flex; align-items: center; height: 50px; padding: 0px 32px; background: rgb(17, 17, 17); color: rgb(255, 255, 255); font-size: 12.5px; font-weight: 700; letter-spacing: 0.14em; text-transform: uppercase; white-space: nowrap; cursor: pointer;">Xem bản đồ trạm sạc</span>
    </div>
    <div style="height: 460px; background: repeating-linear-gradient(45deg, rgb(236, 236, 234) 0px, rgb(236, 236, 234) 16px, rgb(245, 245, 243) 16px, rgb(245, 245, 243) 32px); display: flex; align-items: center; justify-content: center;"><span style="font-family: ui-monospace, Menlo, monospace; font-size: 11px; color: rgb(152, 152, 143);">[ trạm sạc ban đêm ]</span></div>
  </div>

  
  <div style="max-width: 1360px; margin: 0px auto; padding: 104px 64px;">
    <h2 style="margin: 0px 0px 40px; font-size: 42px; font-weight: 500; letter-spacing: -0.015em; color: rgb(17, 17, 17);">Khám phá đại lý</h2>
    <div style="display: grid; grid-template-columns: 1.4fr 1fr; gap: 24px;">
      <div style="cursor: pointer;">
        <div style="height: 460px; background: repeating-linear-gradient(45deg, rgb(236, 236, 234) 0px, rgb(236, 236, 234) 16px, rgb(245, 245, 243) 16px, rgb(245, 245, 243) 32px); display: flex; align-items: center; justify-content: center; margin-bottom: 20px; overflow: hidden;"><span style="font-family: ui-monospace, Menlo, monospace; font-size: 11px; color: rgb(152, 152, 143);">[ showroom Bắc Giang ]</span></div>
        <div style="font-size: 11.5px; font-weight: 700; letter-spacing: 0.2em; text-transform: uppercase; color: rgb(102, 102, 102); margin-bottom: 10px;">Câu chuyện đại lý</div>
        <div style="font-size: 26px; font-weight: 500; color: rgb(17, 17, 17); margin-bottom: 8px;">1.200 gia đình Bắc Giang đã chuyển sang xe điện</div>
        <p style="margin: 0px; font-size: 15px; line-height: 1.6; color: rgb(102, 102, 102); max-width: 560px;">Từ năm 2022, showroom trên đường Xương Giang là nơi nhiều gia đình lần đầu cầm lái một chiếc xe điện.</p>
      </div>
      <div style="display: grid; grid-template-rows: 1fr 1fr; gap: 24px;">
        <div style="cursor: pointer;">
          <div style="height: 200px; background: repeating-linear-gradient(45deg, rgb(236, 236, 234) 0px, rgb(236, 236, 234) 14px, rgb(245, 245, 243) 14px, rgb(245, 245, 243) 28px); display: flex; align-items: center; justify-content: center; margin-bottom: 16px;"><span style="font-family: ui-monospace, Menlo, monospace; font-size: 10px; color: rgb(152, 152, 143);">[ sự kiện lái thử ]</span></div>
          <div style="font-size: 11.5px; font-weight: 700; letter-spacing: 0.2em; text-transform: uppercase; color: rgb(102, 102, 102); margin-bottom: 8px;">Sự kiện</div>
          <div style="font-size: 20px; font-weight: 500; color: rgb(17, 17, 17);">Cuối tuần lái thử tại Quảng trường 3/2</div>
        </div>
        <div style="cursor: pointer;">
          <div style="height: 200px; background: repeating-linear-gradient(45deg, rgb(236, 236, 234) 0px, rgb(236, 236, 234) 14px, rgb(245, 245, 243) 14px, rgb(245, 245, 243) 28px); display: flex; align-items: center; justify-content: center; margin-bottom: 16px;"><span style="font-family: ui-monospace, Menlo, monospace; font-size: 10px; color: rgb(152, 152, 143);">[ xưởng dịch vụ ]</span></div>
          <div style="font-size: 11.5px; font-weight: 700; letter-spacing: 0.2em; text-transform: uppercase; color: rgb(102, 102, 102); margin-bottom: 8px;">Dịch vụ</div>
          <div style="font-size: 20px; font-weight: 500; color: rgb(17, 17, 17);">Xưởng 8 khoang, nhận xe trong ngày</div>
        </div>
      </div>
    </div>
  </div>

  
  <div style="background: rgb(244, 244, 242);">
    <div style="max-width: 1360px; margin: 0px auto; padding: 96px 64px; display: grid; grid-template-columns: 1fr 1fr; gap: 72px; align-items: center;">
      <div style="height: 400px; background: repeating-linear-gradient(45deg, rgb(228, 228, 225) 0px, rgb(228, 228, 225) 16px, rgb(238, 238, 235) 16px, rgb(238, 238, 235) 32px); display: flex; align-items: center; justify-content: center;"><span style="font-family: ui-monospace, Menlo, monospace; font-size: 11px; color: rgb(152, 152, 143);">[ cố vấn dịch vụ và khách hàng ]</span></div>
      <div>
        <div style="font-size: 11.5px; font-weight: 700; letter-spacing: 0.24em; text-transform: uppercase; color: rgb(102, 102, 102); margin-bottom: 18px;">Chăm sóc chủ xe</div>
        <h2 style="margin: 0px 0px 18px; font-size: 38px; line-height: 1.15; font-weight: 500; letter-spacing: -0.015em; color: rgb(17, 17, 17);">Yên tâm suốt 10 năm sở hữu</h2>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 32px;">
          <div><div style="font-size: 26px; font-weight: 600; color: rgb(17, 17, 17);">10 năm</div><div style="font-size: 13.5px; color: rgb(102, 102, 102); margin-top: 4px;">Bảo hành xe và pin</div></div>
          <div><div style="font-size: 26px; font-weight: 600; color: rgb(17, 17, 17);">24/7</div><div style="font-size: 13.5px; color: rgb(102, 102, 102); margin-top: 4px;">Cứu hộ lưu động toàn tỉnh</div></div>
          <div><div style="font-size: 26px; font-weight: 600; color: rgb(17, 17, 17);">45 phút</div><div style="font-size: 13.5px; color: rgb(102, 102, 102); margin-top: 4px;">Thời gian cứu hộ trung bình</div></div>
          <div><div style="font-size: 26px; font-weight: 600; color: rgb(17, 17, 17);">4,9/5</div><div style="font-size: 13.5px; color: rgb(102, 102, 102); margin-top: 4px;">Điểm hài lòng dịch vụ</div></div>
        </div>
        <div style="display: flex; gap: 12px;">
          <span class="scp6" style="display: inline-flex; align-items: center; height: 50px; padding: 0px 32px; background: rgb(17, 17, 17); color: rgb(255, 255, 255); font-size: 12.5px; font-weight: 700; letter-spacing: 0.14em; text-transform: uppercase; white-space: nowrap; cursor: pointer;">Đặt lịch bảo dưỡng</span>
          <span class="scp7" style="display: inline-flex; align-items: center; height: 50px; padding: 0px 32px; border: 1px solid rgb(17, 17, 17); color: rgb(17, 17, 17); font-size: 12.5px; font-weight: 700; letter-spacing: 0.14em; text-transform: uppercase; white-space: nowrap; cursor: pointer;">Đăng ký lái thử</span>
        </div>
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
