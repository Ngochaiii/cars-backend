{{--
    BẢN CẮT TĨNH trang "Ô tô điện" — chép 1:1 từ bản thiết kế trong
    resources/views/frontend/website 2 (màn hình #list).

    ĐÂY LÀ BẢN ĐỐI CHIẾU, CHƯA NỐI BACKEND. Không có biến, không truy vấn,
    không route động: mọi chữ và số đều là dữ liệu mẫu của bản thiết kế.
    Mở /mau/o-to-dien để so cạnh trang thật rồi ghép từng khối một.

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
    <title>Mẫu — Ô tô điện</title>

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
      
        <span class="scp0" style="cursor: pointer; color: rgb(17, 17, 17); border-bottom: 2px solid rgb(17, 17, 17); padding-bottom: 2px;"><span class="sc-interp">Ô tô</span></span>
      
        <span class="scp0" style="cursor: pointer; color: rgb(58, 58, 56); border-bottom: 2px solid transparent; padding-bottom: 2px;"><span class="sc-interp">Phụ kiện</span></span>
      
        <span class="scp0" style="cursor: pointer; color: rgb(58, 58, 56); border-bottom: 2px solid transparent; padding-bottom: 2px;"><span class="sc-interp">Trạm sạc &amp; Dịch vụ</span></span>
      
        <span class="scp0" style="cursor: pointer; color: rgb(58, 58, 56); border-bottom: 2px solid transparent; padding-bottom: 2px;"><span class="sc-interp">Tin tức</span></span>
      
        <span class="scp0" style="cursor: pointer; color: rgb(58, 58, 56); border-bottom: 2px solid transparent; padding-bottom: 2px;"><span class="sc-interp">Về chúng tôi</span></span>
      
    </nav>
    <span class="scp1" style="flex: 0 0 auto; display: inline-flex; align-items: center; height: 42px; padding: 0px 22px; background: rgb(17, 17, 17); color: rgb(255, 255, 255); font-size: 13.5px; font-weight: 600; border-radius: 2px; cursor: pointer; white-space: nowrap;">Đăng ký lái thử</span>
  </div></header>
  <div style="max-width: 1280px; margin: 0px auto; padding: 56px 32px 72px;">
    <div style="font-family: ui-monospace, Menlo, monospace; font-size: 11px; letter-spacing: 0.2em; color: rgb(152, 152, 143); text-transform: uppercase; margin-bottom: 14px;"><span style="cursor: pointer;">Trang chủ</span> / Sản phẩm</div>
    <h1 style="margin: 0px 0px 28px; font-size: 44px; font-weight: 700; letter-spacing: -0.02em; color: rgb(17, 17, 17);">Ô tô điện</h1>
    <div style="display: flex; gap: 10px; border-bottom: 1px solid rgb(236, 236, 234); padding-bottom: 20px; margin-bottom: 36px;">
      
        <span style="display: inline-flex; align-items: center; height: 38px; padding: 0px 18px; background: rgb(17, 17, 17); border: 1px solid rgb(17, 17, 17); color: rgb(255, 255, 255); font-size: 13px; font-weight: 600; border-radius: 999px; cursor: pointer;"><span class="sc-interp">Tất cả (6)</span></span>
      
        <span style="display: inline-flex; align-items: center; height: 38px; padding: 0px 18px; background: rgb(255, 255, 255); border: 1px solid rgb(214, 214, 210); color: rgb(58, 58, 56); font-size: 13px; font-weight: 600; border-radius: 999px; cursor: pointer;"><span class="sc-interp">Đô thị</span></span>
      
        <span style="display: inline-flex; align-items: center; height: 38px; padding: 0px 18px; background: rgb(255, 255, 255); border: 1px solid rgb(214, 214, 210); color: rgb(58, 58, 56); font-size: 13px; font-weight: 600; border-radius: 999px; cursor: pointer;"><span class="sc-interp">Gia đình</span></span>
      
        <span style="display: inline-flex; align-items: center; height: 38px; padding: 0px 18px; background: rgb(255, 255, 255); border: 1px solid rgb(214, 214, 210); color: rgb(58, 58, 56); font-size: 13px; font-weight: 600; border-radius: 999px; cursor: pointer;"><span class="sc-interp">7 chỗ</span></span>
      
    </div>
    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 28px;">
      
        <div class="scp9" style="border: 1px solid rgb(236, 236, 234); border-radius: 4px; overflow: hidden; background: rgb(255, 255, 255); cursor: pointer;">
          <div style="height: 240px; background: repeating-linear-gradient(45deg, rgb(236, 236, 234) 0px, rgb(236, 236, 234) 12px, rgb(245, 245, 243) 12px, rgb(245, 245, 243) 24px); display: flex; align-items: center; justify-content: center; position: relative;">
            <span style="font-family: ui-monospace, Menlo, monospace; font-size: 11px; color: rgb(152, 152, 143);">[ ảnh <span class="sc-interp">VF 3</span> — ngang ]</span>
            <span style="position: absolute; top: 16px; left: 16px; font-size: 11px; font-weight: 600; letter-spacing: 0.14em; text-transform: uppercase; background: rgb(255, 255, 255); border: 1px solid rgb(236, 236, 234); padding: 6px 10px; border-radius: 2px; color: rgb(58, 58, 56);"><span class="sc-interp">Mini SUV</span></span>
          </div>
          <div style="padding: 26px 28px 28px;">
            <div style="display: flex; align-items: baseline; justify-content: space-between; margin-bottom: 14px;">
              <span style="font-size: 24px; font-weight: 700; color: rgb(17, 17, 17);"><span class="sc-interp">VF 3</span></span>
              <span style="font-size: 15px; color: rgb(107, 107, 104);">Từ <b style="color: rgb(17, 17, 17); font-weight: 700;"><span class="sc-interp">299 triệu</span></b></span>
            </div>
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1px; background: rgb(236, 236, 234); border: 1px solid rgb(236, 236, 234); border-radius: 2px; overflow: hidden; margin-bottom: 18px;">
              <div style="background: rgb(250, 250, 248); padding: 12px 14px;"><div style="font-size: 15px; font-weight: 600; color: rgb(17, 17, 17);"><span class="sc-interp">215 km</span></div><div style="font-size: 11.5px; color: rgb(138, 138, 134); margin-top: 2px;">Quãng đường</div></div>
              <div style="background: rgb(250, 250, 248); padding: 12px 14px;"><div style="font-size: 15px; font-weight: 600; color: rgb(17, 17, 17);"><span class="sc-interp">4</span> chỗ</div><div style="font-size: 11.5px; color: rgb(138, 138, 134); margin-top: 2px;">Số ghế</div></div>
              <div style="background: rgb(250, 250, 248); padding: 12px 14px;"><div style="font-size: 15px; font-weight: 600; color: rgb(17, 17, 17);"><span class="sc-interp">~36 phút</span></div><div style="font-size: 11.5px; color: rgb(138, 138, 134); margin-top: 2px;">Sạc 10–70%</div></div>
            </div>
            <div style="display: flex; gap: 10px;">
              <span class="scp1" style="flex: 1 1 0%; display: inline-flex; align-items: center; justify-content: center; height: 44px; background: rgb(17, 17, 17); color: rgb(255, 255, 255); font-size: 13.5px; font-weight: 600; border-radius: 2px; cursor: pointer;">Đặt cọc</span>
              <span class="scpa" style="flex: 1 1 0%; display: inline-flex; align-items: center; justify-content: center; height: 44px; border: 1px solid rgb(214, 214, 210); color: rgb(17, 17, 17); font-size: 13.5px; font-weight: 600; border-radius: 2px; cursor: pointer;">Xem chi tiết</span>
            </div>
          </div>
        </div>
      
        <div class="scp9" style="border: 1px solid rgb(236, 236, 234); border-radius: 4px; overflow: hidden; background: rgb(255, 255, 255); cursor: pointer;">
          <div style="height: 240px; background: repeating-linear-gradient(45deg, rgb(236, 236, 234) 0px, rgb(236, 236, 234) 12px, rgb(245, 245, 243) 12px, rgb(245, 245, 243) 24px); display: flex; align-items: center; justify-content: center; position: relative;">
            <span style="font-family: ui-monospace, Menlo, monospace; font-size: 11px; color: rgb(152, 152, 143);">[ ảnh <span class="sc-interp">VF 5</span> — ngang ]</span>
            <span style="position: absolute; top: 16px; left: 16px; font-size: 11px; font-weight: 600; letter-spacing: 0.14em; text-transform: uppercase; background: rgb(255, 255, 255); border: 1px solid rgb(236, 236, 234); padding: 6px 10px; border-radius: 2px; color: rgb(58, 58, 56);"><span class="sc-interp">SUV hạng A</span></span>
          </div>
          <div style="padding: 26px 28px 28px;">
            <div style="display: flex; align-items: baseline; justify-content: space-between; margin-bottom: 14px;">
              <span style="font-size: 24px; font-weight: 700; color: rgb(17, 17, 17);"><span class="sc-interp">VF 5</span></span>
              <span style="font-size: 15px; color: rgb(107, 107, 104);">Từ <b style="color: rgb(17, 17, 17); font-weight: 700;"><span class="sc-interp">529 triệu</span></b></span>
            </div>
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1px; background: rgb(236, 236, 234); border: 1px solid rgb(236, 236, 234); border-radius: 2px; overflow: hidden; margin-bottom: 18px;">
              <div style="background: rgb(250, 250, 248); padding: 12px 14px;"><div style="font-size: 15px; font-weight: 600; color: rgb(17, 17, 17);"><span class="sc-interp">326 km</span></div><div style="font-size: 11.5px; color: rgb(138, 138, 134); margin-top: 2px;">Quãng đường</div></div>
              <div style="background: rgb(250, 250, 248); padding: 12px 14px;"><div style="font-size: 15px; font-weight: 600; color: rgb(17, 17, 17);"><span class="sc-interp">5</span> chỗ</div><div style="font-size: 11.5px; color: rgb(138, 138, 134); margin-top: 2px;">Số ghế</div></div>
              <div style="background: rgb(250, 250, 248); padding: 12px 14px;"><div style="font-size: 15px; font-weight: 600; color: rgb(17, 17, 17);"><span class="sc-interp">~33 phút</span></div><div style="font-size: 11.5px; color: rgb(138, 138, 134); margin-top: 2px;">Sạc 10–70%</div></div>
            </div>
            <div style="display: flex; gap: 10px;">
              <span class="scp1" style="flex: 1 1 0%; display: inline-flex; align-items: center; justify-content: center; height: 44px; background: rgb(17, 17, 17); color: rgb(255, 255, 255); font-size: 13.5px; font-weight: 600; border-radius: 2px; cursor: pointer;">Đặt cọc</span>
              <span class="scpa" style="flex: 1 1 0%; display: inline-flex; align-items: center; justify-content: center; height: 44px; border: 1px solid rgb(214, 214, 210); color: rgb(17, 17, 17); font-size: 13.5px; font-weight: 600; border-radius: 2px; cursor: pointer;">Xem chi tiết</span>
            </div>
          </div>
        </div>
      
        <div class="scp9" style="border: 1px solid rgb(236, 236, 234); border-radius: 4px; overflow: hidden; background: rgb(255, 255, 255); cursor: pointer;">
          <div style="height: 240px; background: repeating-linear-gradient(45deg, rgb(236, 236, 234) 0px, rgb(236, 236, 234) 12px, rgb(245, 245, 243) 12px, rgb(245, 245, 243) 24px); display: flex; align-items: center; justify-content: center; position: relative;">
            <span style="font-family: ui-monospace, Menlo, monospace; font-size: 11px; color: rgb(152, 152, 143);">[ ảnh <span class="sc-interp">VF 6</span> — ngang ]</span>
            <span style="position: absolute; top: 16px; left: 16px; font-size: 11px; font-weight: 600; letter-spacing: 0.14em; text-transform: uppercase; background: rgb(255, 255, 255); border: 1px solid rgb(236, 236, 234); padding: 6px 10px; border-radius: 2px; color: rgb(58, 58, 56);"><span class="sc-interp">SUV hạng B</span></span>
          </div>
          <div style="padding: 26px 28px 28px;">
            <div style="display: flex; align-items: baseline; justify-content: space-between; margin-bottom: 14px;">
              <span style="font-size: 24px; font-weight: 700; color: rgb(17, 17, 17);"><span class="sc-interp">VF 6</span></span>
              <span style="font-size: 15px; color: rgb(107, 107, 104);">Từ <b style="color: rgb(17, 17, 17); font-weight: 700;"><span class="sc-interp">694 triệu</span></b></span>
            </div>
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1px; background: rgb(236, 236, 234); border: 1px solid rgb(236, 236, 234); border-radius: 2px; overflow: hidden; margin-bottom: 18px;">
              <div style="background: rgb(250, 250, 248); padding: 12px 14px;"><div style="font-size: 15px; font-weight: 600; color: rgb(17, 17, 17);"><span class="sc-interp">480 km</span></div><div style="font-size: 11.5px; color: rgb(138, 138, 134); margin-top: 2px;">Quãng đường</div></div>
              <div style="background: rgb(250, 250, 248); padding: 12px 14px;"><div style="font-size: 15px; font-weight: 600; color: rgb(17, 17, 17);"><span class="sc-interp">5</span> chỗ</div><div style="font-size: 11.5px; color: rgb(138, 138, 134); margin-top: 2px;">Số ghế</div></div>
              <div style="background: rgb(250, 250, 248); padding: 12px 14px;"><div style="font-size: 15px; font-weight: 600; color: rgb(17, 17, 17);"><span class="sc-interp">~25 phút</span></div><div style="font-size: 11.5px; color: rgb(138, 138, 134); margin-top: 2px;">Sạc 10–70%</div></div>
            </div>
            <div style="display: flex; gap: 10px;">
              <span class="scp1" style="flex: 1 1 0%; display: inline-flex; align-items: center; justify-content: center; height: 44px; background: rgb(17, 17, 17); color: rgb(255, 255, 255); font-size: 13.5px; font-weight: 600; border-radius: 2px; cursor: pointer;">Đặt cọc</span>
              <span class="scpa" style="flex: 1 1 0%; display: inline-flex; align-items: center; justify-content: center; height: 44px; border: 1px solid rgb(214, 214, 210); color: rgb(17, 17, 17); font-size: 13.5px; font-weight: 600; border-radius: 2px; cursor: pointer;">Xem chi tiết</span>
            </div>
          </div>
        </div>
      
        <div class="scp9" style="border: 1px solid rgb(236, 236, 234); border-radius: 4px; overflow: hidden; background: rgb(255, 255, 255); cursor: pointer;">
          <div style="height: 240px; background: repeating-linear-gradient(45deg, rgb(236, 236, 234) 0px, rgb(236, 236, 234) 12px, rgb(245, 245, 243) 12px, rgb(245, 245, 243) 24px); display: flex; align-items: center; justify-content: center; position: relative;">
            <span style="font-family: ui-monospace, Menlo, monospace; font-size: 11px; color: rgb(152, 152, 143);">[ ảnh <span class="sc-interp">VF 7</span> — ngang ]</span>
            <span style="position: absolute; top: 16px; left: 16px; font-size: 11px; font-weight: 600; letter-spacing: 0.14em; text-transform: uppercase; background: rgb(255, 255, 255); border: 1px solid rgb(236, 236, 234); padding: 6px 10px; border-radius: 2px; color: rgb(58, 58, 56);"><span class="sc-interp">SUV hạng C</span></span>
          </div>
          <div style="padding: 26px 28px 28px;">
            <div style="display: flex; align-items: baseline; justify-content: space-between; margin-bottom: 14px;">
              <span style="font-size: 24px; font-weight: 700; color: rgb(17, 17, 17);"><span class="sc-interp">VF 7</span></span>
              <span style="font-size: 15px; color: rgb(107, 107, 104);">Từ <b style="color: rgb(17, 17, 17); font-weight: 700;"><span class="sc-interp">799 triệu</span></b></span>
            </div>
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1px; background: rgb(236, 236, 234); border: 1px solid rgb(236, 236, 234); border-radius: 2px; overflow: hidden; margin-bottom: 18px;">
              <div style="background: rgb(250, 250, 248); padding: 12px 14px;"><div style="font-size: 15px; font-weight: 600; color: rgb(17, 17, 17);"><span class="sc-interp">496 km</span></div><div style="font-size: 11.5px; color: rgb(138, 138, 134); margin-top: 2px;">Quãng đường</div></div>
              <div style="background: rgb(250, 250, 248); padding: 12px 14px;"><div style="font-size: 15px; font-weight: 600; color: rgb(17, 17, 17);"><span class="sc-interp">5</span> chỗ</div><div style="font-size: 11.5px; color: rgb(138, 138, 134); margin-top: 2px;">Số ghế</div></div>
              <div style="background: rgb(250, 250, 248); padding: 12px 14px;"><div style="font-size: 15px; font-weight: 600; color: rgb(17, 17, 17);"><span class="sc-interp">~25 phút</span></div><div style="font-size: 11.5px; color: rgb(138, 138, 134); margin-top: 2px;">Sạc 10–70%</div></div>
            </div>
            <div style="display: flex; gap: 10px;">
              <span class="scp1" style="flex: 1 1 0%; display: inline-flex; align-items: center; justify-content: center; height: 44px; background: rgb(17, 17, 17); color: rgb(255, 255, 255); font-size: 13.5px; font-weight: 600; border-radius: 2px; cursor: pointer;">Đặt cọc</span>
              <span class="scpa" style="flex: 1 1 0%; display: inline-flex; align-items: center; justify-content: center; height: 44px; border: 1px solid rgb(214, 214, 210); color: rgb(17, 17, 17); font-size: 13.5px; font-weight: 600; border-radius: 2px; cursor: pointer;">Xem chi tiết</span>
            </div>
          </div>
        </div>
      
        <div class="scp9" style="border: 1px solid rgb(236, 236, 234); border-radius: 4px; overflow: hidden; background: rgb(255, 255, 255); cursor: pointer;">
          <div style="height: 240px; background: repeating-linear-gradient(45deg, rgb(236, 236, 234) 0px, rgb(236, 236, 234) 12px, rgb(245, 245, 243) 12px, rgb(245, 245, 243) 24px); display: flex; align-items: center; justify-content: center; position: relative;">
            <span style="font-family: ui-monospace, Menlo, monospace; font-size: 11px; color: rgb(152, 152, 143);">[ ảnh <span class="sc-interp">VF 8</span> — ngang ]</span>
            <span style="position: absolute; top: 16px; left: 16px; font-size: 11px; font-weight: 600; letter-spacing: 0.14em; text-transform: uppercase; background: rgb(255, 255, 255); border: 1px solid rgb(236, 236, 234); padding: 6px 10px; border-radius: 2px; color: rgb(58, 58, 56);"><span class="sc-interp">SUV hạng D</span></span>
          </div>
          <div style="padding: 26px 28px 28px;">
            <div style="display: flex; align-items: baseline; justify-content: space-between; margin-bottom: 14px;">
              <span style="font-size: 24px; font-weight: 700; color: rgb(17, 17, 17);"><span class="sc-interp">VF 8</span></span>
              <span style="font-size: 15px; color: rgb(107, 107, 104);">Từ <b style="color: rgb(17, 17, 17); font-weight: 700;"><span class="sc-interp">1,09 tỷ</span></b></span>
            </div>
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1px; background: rgb(236, 236, 234); border: 1px solid rgb(236, 236, 234); border-radius: 2px; overflow: hidden; margin-bottom: 18px;">
              <div style="background: rgb(250, 250, 248); padding: 12px 14px;"><div style="font-size: 15px; font-weight: 600; color: rgb(17, 17, 17);"><span class="sc-interp">471 km</span></div><div style="font-size: 11.5px; color: rgb(138, 138, 134); margin-top: 2px;">Quãng đường</div></div>
              <div style="background: rgb(250, 250, 248); padding: 12px 14px;"><div style="font-size: 15px; font-weight: 600; color: rgb(17, 17, 17);"><span class="sc-interp">5</span> chỗ</div><div style="font-size: 11.5px; color: rgb(138, 138, 134); margin-top: 2px;">Số ghế</div></div>
              <div style="background: rgb(250, 250, 248); padding: 12px 14px;"><div style="font-size: 15px; font-weight: 600; color: rgb(17, 17, 17);"><span class="sc-interp">~31 phút</span></div><div style="font-size: 11.5px; color: rgb(138, 138, 134); margin-top: 2px;">Sạc 10–70%</div></div>
            </div>
            <div style="display: flex; gap: 10px;">
              <span class="scp1" style="flex: 1 1 0%; display: inline-flex; align-items: center; justify-content: center; height: 44px; background: rgb(17, 17, 17); color: rgb(255, 255, 255); font-size: 13.5px; font-weight: 600; border-radius: 2px; cursor: pointer;">Đặt cọc</span>
              <span class="scpa" style="flex: 1 1 0%; display: inline-flex; align-items: center; justify-content: center; height: 44px; border: 1px solid rgb(214, 214, 210); color: rgb(17, 17, 17); font-size: 13.5px; font-weight: 600; border-radius: 2px; cursor: pointer;">Xem chi tiết</span>
            </div>
          </div>
        </div>
      
        <div class="scp9" style="border: 1px solid rgb(236, 236, 234); border-radius: 4px; overflow: hidden; background: rgb(255, 255, 255); cursor: pointer;">
          <div style="height: 240px; background: repeating-linear-gradient(45deg, rgb(236, 236, 234) 0px, rgb(236, 236, 234) 12px, rgb(245, 245, 243) 12px, rgb(245, 245, 243) 24px); display: flex; align-items: center; justify-content: center; position: relative;">
            <span style="font-family: ui-monospace, Menlo, monospace; font-size: 11px; color: rgb(152, 152, 143);">[ ảnh <span class="sc-interp">VF 9</span> — ngang ]</span>
            <span style="position: absolute; top: 16px; left: 16px; font-size: 11px; font-weight: 600; letter-spacing: 0.14em; text-transform: uppercase; background: rgb(255, 255, 255); border: 1px solid rgb(236, 236, 234); padding: 6px 10px; border-radius: 2px; color: rgb(58, 58, 56);"><span class="sc-interp">SUV hạng E</span></span>
          </div>
          <div style="padding: 26px 28px 28px;">
            <div style="display: flex; align-items: baseline; justify-content: space-between; margin-bottom: 14px;">
              <span style="font-size: 24px; font-weight: 700; color: rgb(17, 17, 17);"><span class="sc-interp">VF 9</span></span>
              <span style="font-size: 15px; color: rgb(107, 107, 104);">Từ <b style="color: rgb(17, 17, 17); font-weight: 700;"><span class="sc-interp">1,49 tỷ</span></b></span>
            </div>
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1px; background: rgb(236, 236, 234); border: 1px solid rgb(236, 236, 234); border-radius: 2px; overflow: hidden; margin-bottom: 18px;">
              <div style="background: rgb(250, 250, 248); padding: 12px 14px;"><div style="font-size: 15px; font-weight: 600; color: rgb(17, 17, 17);"><span class="sc-interp">626 km</span></div><div style="font-size: 11.5px; color: rgb(138, 138, 134); margin-top: 2px;">Quãng đường</div></div>
              <div style="background: rgb(250, 250, 248); padding: 12px 14px;"><div style="font-size: 15px; font-weight: 600; color: rgb(17, 17, 17);"><span class="sc-interp">7</span> chỗ</div><div style="font-size: 11.5px; color: rgb(138, 138, 134); margin-top: 2px;">Số ghế</div></div>
              <div style="background: rgb(250, 250, 248); padding: 12px 14px;"><div style="font-size: 15px; font-weight: 600; color: rgb(17, 17, 17);"><span class="sc-interp">~35 phút</span></div><div style="font-size: 11.5px; color: rgb(138, 138, 134); margin-top: 2px;">Sạc 10–70%</div></div>
            </div>
            <div style="display: flex; gap: 10px;">
              <span class="scp1" style="flex: 1 1 0%; display: inline-flex; align-items: center; justify-content: center; height: 44px; background: rgb(17, 17, 17); color: rgb(255, 255, 255); font-size: 13.5px; font-weight: 600; border-radius: 2px; cursor: pointer;">Đặt cọc</span>
              <span class="scpa" style="flex: 1 1 0%; display: inline-flex; align-items: center; justify-content: center; height: 44px; border: 1px solid rgb(214, 214, 210); color: rgb(17, 17, 17); font-size: 13.5px; font-weight: 600; border-radius: 2px; cursor: pointer;">Xem chi tiết</span>
            </div>
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
