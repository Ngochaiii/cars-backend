{{--
    BẢN CẮT TĨNH trang "Chi tiết xe" — chép 1:1 từ bản thiết kế trong
    resources/views/frontend/website 2 (màn hình #detail).

    ĐÂY LÀ BẢN ĐỐI CHIẾU, CHƯA NỐI BACKEND. Không có biến, không truy vấn,
    không route động: mọi chữ và số đều là dữ liệu mẫu của bản thiết kế
    (đang ở xe VF 7). Mở /mau/chi-tiet-xe để so cạnh trang thật
    /san-pham/{slug} rồi ghép từng khối một.

    Style để nguyên dạng inline như bản thiết kế xuất ra — cố tình không
    gom vào frontend.css, để bản này không bao giờ trôi khỏi bản gốc khi
    CSS của site thay đổi.

    Nút bấm ở đây là chữ tĩnh, không có JS: băng chuyền, tab, bộ chọn màu
    đứng yên ở trạng thái đầu.
--}}
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mẫu — Chi tiết xe</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@300;400;500;600;700;800&display=swap">

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
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

  
  <div style="position: relative; height: 760px; background: rgb(242, 241, 236); display: flex; align-items: center; overflow: hidden; opacity: 1; transition: opacity 0.45s, background 0.4s;">
    <div style="position: absolute; inset: 0px; background: linear-gradient(100deg, rgba(0, 0, 0, 0.55), rgba(0, 0, 0, 0.15));"></div>
    <div style="position: relative; z-index: 1; max-width: 1320px; margin: 0px auto; width: 100%; padding: 0px 64px;">
      <div style="max-width: 620px;">
        <div style="font-size: 12px; font-weight: 700; letter-spacing: 0.24em; text-transform: uppercase; color: rgba(255, 255, 255, 0.7); margin-bottom: 20px;"><span class="sc-interp">VF 7</span> · <span class="sc-interp">SUV hạng C</span></div>
        <h1 style="margin: 0px 0px 22px; font-size: 64px; line-height: 1.06; font-weight: 700; letter-spacing: -0.02em; color: rgb(255, 255, 255);">Khi phong cách<br>trở thành dấu ấn</h1>
        <p style="margin: 0px 0px 30px; font-size: 18px; line-height: 1.6; color: rgba(255, 255, 255, 0.82); max-width: 520px;">Thiết kế hoàn toàn mới, công nghệ dẫn đầu và trải nghiệm chuẩn 5 sao — <span class="sc-interp">VF 7</span> sẵn sàng đồng hành cùng gia đình bạn trên mọi hành trình.</p>
        <div style="display: flex; align-items: baseline; gap: 14px; margin-bottom: 32px;">
          <span style="font-size: 15px; color: rgba(255, 255, 255, 0.75);">Giá từ</span>
          <span style="font-size: 30px; font-weight: 700; color: rgb(255, 255, 255);"><span class="sc-interp">799 triệu</span></span>
          <span style="font-size: 15px; color: rgba(255, 255, 255, 0.5); text-decoration: line-through;"><span class="sc-interp">899 triệu</span></span>
        </div>
        <div style="display: flex; gap: 14px;">
          <span class="scpb" style="display: inline-flex; align-items: center; height: 52px; padding: 0px 40px; background: rgb(20, 100, 244); color: rgb(255, 255, 255); font-size: 14px; font-weight: 700; letter-spacing: 0.06em; text-transform: uppercase; white-space: nowrap; cursor: pointer; transition: background 0.2s, transform 0.2s;">Đặt cọc</span>
          <span class="scpc" style="display: inline-flex; align-items: center; height: 52px; padding: 0px 32px; border: 1px solid rgba(255, 255, 255, 0.6); color: rgb(255, 255, 255); font-size: 14px; font-weight: 600; cursor: pointer;">Đăng ký lái thử</span>
        </div>
      </div>
    </div>
    <span class="scpd" style="position: absolute; z-index: 2; left: 24px; top: 50%; transform: translateY(-50%); width: 44px; height: 44px; border-radius: 999px; background: rgba(255, 255, 255, 0.18); display: flex; align-items: center; justify-content: center; font-size: 20px; color: rgb(255, 255, 255); cursor: pointer;">‹</span>
    <span class="scpd" style="position: absolute; z-index: 2; right: 24px; top: 50%; transform: translateY(-50%); width: 44px; height: 44px; border-radius: 999px; background: rgba(255, 255, 255, 0.18); display: flex; align-items: center; justify-content: center; font-size: 20px; color: rgb(255, 255, 255); cursor: pointer;">›</span>
    <div style="position: absolute; z-index: 2; left: 0px; right: 0px; bottom: 28px; display: flex; justify-content: center; gap: 8px;">
      
        <span style="width: 7px; height: 7px; border-radius: 999px; background: rgb(255, 255, 255); cursor: pointer;"></span>
      
        <span style="width: 7px; height: 7px; border-radius: 999px; background: rgba(255, 255, 255, 0.4); cursor: pointer;"></span>
      
        <span style="width: 7px; height: 7px; border-radius: 999px; background: rgba(255, 255, 255, 0.4); cursor: pointer;"></span>
      
        <span style="width: 7px; height: 7px; border-radius: 999px; background: rgba(255, 255, 255, 0.4); cursor: pointer;"></span>
      
    </div>
    <div style="position: absolute; z-index: 2; right: 64px; bottom: 26px; font-size: 12px; color: rgba(255, 255, 255, 0.6); letter-spacing: 0.1em; text-transform: uppercase;"><span class="sc-interp">3/4 phía trước</span></div>
  </div>

  
  <div style="max-width: 900px; margin: 0px auto; padding: 120px 64px; text-align: center;">
    <h2 style="margin: 0px 0px 22px; font-size: 46px; line-height: 1.15; font-weight: 600; letter-spacing: -0.02em; color: rgb(23, 23, 23);">Thiết kế phong cách cho thế hệ khách hàng hiện đại</h2>
    <p style="margin: 0px; font-size: 19px; line-height: 1.6; color: rgb(85, 85, 85);">Ngoại hình hoàn toàn mới với các đường nét liền mạch, tỷ lệ cân đối và chi tiết tinh giản — <span class="sc-interp">VF 7</span> thể hiện gu thẩm mỹ của người chủ động chọn lối sống xanh.</p>
  </div>

  
  <div style="max-width: 1320px; margin: 0px auto; padding: 0px 64px 120px;">
    <div style="display: grid; grid-template-columns: repeat(4, 1fr);">
      
        <div style="padding: 0px 32px; border-left-width: medium; border-left-style: none; border-left-color: currentcolor;">
          <div style="font-size: 42px; font-weight: 700; letter-spacing: -0.02em; color: rgb(23, 23, 23); white-space: nowrap;"><span class="sc-interp">260 kW</span></div>
          <div style="font-size: 15px; color: rgb(102, 102, 102); margin-top: 8px;"><span class="sc-interp">Công suất tối đa</span></div>
        </div>
      
        <div style="padding: 0px 32px; border-left: 1px solid rgb(227, 227, 227);">
          <div style="font-size: 42px; font-weight: 700; letter-spacing: -0.02em; color: rgb(23, 23, 23); white-space: nowrap;"><span class="sc-interp">500 Nm</span></div>
          <div style="font-size: 15px; color: rgb(102, 102, 102); margin-top: 8px;"><span class="sc-interp">Mô men xoắn cực đại</span></div>
        </div>
      
        <div style="padding: 0px 32px; border-left: 1px solid rgb(227, 227, 227);">
          <div style="font-size: 42px; font-weight: 700; letter-spacing: -0.02em; color: rgb(23, 23, 23); white-space: nowrap;"><span class="sc-interp">496 km</span></div>
          <div style="font-size: 15px; color: rgb(102, 102, 102); margin-top: 8px;"><span class="sc-interp">Quãng đường (NEDC)</span></div>
        </div>
      
        <div style="padding: 0px 32px; border-left: 1px solid rgb(227, 227, 227);">
          <div style="font-size: 42px; font-weight: 700; letter-spacing: -0.02em; color: rgb(23, 23, 23); white-space: nowrap;"><span class="sc-interp">75,3 kWh</span></div>
          <div style="font-size: 15px; color: rgb(102, 102, 102); margin-top: 8px;"><span class="sc-interp">Dung lượng pin khả dụng</span></div>
        </div>
      
    </div>
  </div>

  
  <div style="background: rgb(245, 246, 247); padding: 110px 64px;">
    <div style="max-width: 1320px; margin: 0px auto; text-align: center;">
      <div style="height: 420px; max-width: 820px; margin: 0px auto 32px; border-radius: 16px; background: rgb(242, 241, 236); display: flex; align-items: center; justify-content: center; opacity: 1; transition: opacity 0.25s, background 0.3s;">
        <span style="font-family: ui-monospace, Menlo, monospace; font-size: 12px; color: rgba(255, 255, 255, 0.65);">[ render <span class="sc-interp">VF 7</span> nền trong suốt — <span class="sc-interp">Trắng ngọc trai</span> ]</span>
      </div>
      <div style="font-size: 26px; font-weight: 600; color: rgb(23, 23, 23); margin-bottom: 36px;"><span class="sc-interp">Trắng ngọc trai</span></div>
      <div style="display: flex; justify-content: center; gap: 64px; margin-bottom: 44px;">
        <div>
          <div style="font-size: 13px; color: rgb(102, 102, 102); margin-bottom: 14px;">Màu tiêu chuẩn</div>
          <div style="display: flex; gap: 14px; justify-content: center;">
            
              <span title="Trắng ngọc trai" class="scpe" style="width: 34px; height: 34px; border-radius: 999px; background: rgb(242, 241, 236); border: 3px solid rgb(20, 100, 244); box-shadow: rgba(0, 0, 0, 0.04) 0px 0px 0px 1px; cursor: pointer; transition: transform 0.2s; display: inline-block; box-sizing: border-box;"></span>
            
              <span title="Đen ánh kim" class="scpe" style="width: 34px; height: 34px; border-radius: 999px; background: rgb(28, 28, 26); border: 1px solid rgb(217, 217, 217); box-shadow: rgba(0, 0, 0, 0.04) 0px 0px 0px 1px; cursor: pointer; transition: transform 0.2s; display: inline-block; box-sizing: border-box;"></span>
            
          </div>
        </div>
        <div>
          <div style="font-size: 13px; color: rgb(102, 102, 102); margin-bottom: 14px;">Màu nâng cao</div>
          <div style="display: flex; gap: 14px; justify-content: center;">
            
              <span title="Xanh rêu" class="scpe" style="width: 34px; height: 34px; border-radius: 999px; background: rgb(74, 92, 74); border: 1px solid rgb(217, 217, 217); box-shadow: rgba(0, 0, 0, 0.04) 0px 0px 0px 1px; cursor: pointer; transition: transform 0.2s; display: inline-block; box-sizing: border-box;"></span>
            
              <span title="Đỏ ruby" class="scpe" style="width: 34px; height: 34px; border-radius: 999px; background: rgb(122, 43, 40); border: 1px solid rgb(217, 217, 217); box-shadow: rgba(0, 0, 0, 0.04) 0px 0px 0px 1px; cursor: pointer; transition: transform 0.2s; display: inline-block; box-sizing: border-box;"></span>
            
          </div>
        </div>
      </div>
      <span class="scpb" style="display: inline-flex; align-items: center; height: 52px; padding: 0px 40px; background: rgb(20, 100, 244); color: rgb(255, 255, 255); font-size: 14px; font-weight: 700; letter-spacing: 0.06em; text-transform: uppercase; white-space: nowrap; cursor: pointer; transition: background 0.2s, transform 0.2s;">Đặt cọc ngay</span>
    </div>
  </div>

  
  <div style="max-width: 1320px; margin: 0px auto; padding: 120px 64px;">
    <div style="max-width: 860px;">
      <h2 style="margin: 0px 0px 24px; font-size: 46px; line-height: 1.15; font-weight: 600; letter-spacing: -0.02em; color: rgb(23, 23, 23);">Tech Fluid — dòng chảy công nghệ</h2>
      <p style="margin: 0px; font-size: 19px; line-height: 1.65; color: rgb(85, 85, 85);">Công nghệ không còn là chi tiết được gắn thêm. Trên <span class="sc-interp">VF 7</span>, công nghệ hòa vào từng đường nét thân xe, từng bề mặt nội thất và từng khoảnh khắc vận hành — tự nhiên như dòng chảy.</p>
    </div>
  </div>

  
  <div style="padding: 0px 40px 120px;">
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 18px; max-width: 1600px; margin: 0px auto;">
      <div style="height: 660px; border-radius: 16px; overflow: hidden; background: repeating-linear-gradient(45deg, rgb(236, 236, 234) 0px, rgb(236, 236, 234) 16px, rgb(245, 245, 243) 16px, rgb(245, 245, 243) 32px); display: flex; align-items: center; justify-content: center;"><span style="font-family: ui-monospace, Menlo, monospace; font-size: 12px; color: rgb(152, 152, 143);">[ <span class="sc-interp">VF 7</span> chạy trong đô thị — ảnh lớn ]</span></div>
      <div style="display: grid; grid-template-rows: 1fr 1fr; gap: 18px;">
        <div style="border-radius: 16px; overflow: hidden; background: repeating-linear-gradient(45deg, rgb(236, 236, 234) 0px, rgb(236, 236, 234) 14px, rgb(245, 245, 243) 14px, rgb(245, 245, 243) 28px); display: flex; align-items: center; justify-content: center;"><span style="font-family: ui-monospace, Menlo, monospace; font-size: 11px; color: rgb(152, 152, 143);">[ cung đường ven biển ]</span></div>
        <div style="border-radius: 16px; overflow: hidden; background: repeating-linear-gradient(45deg, rgb(236, 236, 234) 0px, rgb(236, 236, 234) 14px, rgb(245, 245, 243) 14px, rgb(245, 245, 243) 28px); display: flex; align-items: center; justify-content: center;"><span style="font-family: ui-monospace, Menlo, monospace; font-size: 11px; color: rgb(152, 152, 143);">[ góc trên cao ]</span></div>
      </div>
    </div>
  </div>

  
  <div style="max-width: 1320px; margin: 0px auto; padding: 0px 64px 120px; display: grid; grid-template-columns: 40fr 60fr; gap: 80px; align-items: center;">
    <div>
      <h2 style="margin: 0px 0px 20px; font-size: 42px; line-height: 1.15; font-weight: 600; letter-spacing: -0.02em; color: rgb(23, 23, 23);">Trải nghiệm thị giác không giới hạn</h2>
      <p style="margin: 0px; font-size: 17px; line-height: 1.7; color: rgb(85, 85, 85); max-width: 430px;">Thân xe tối ưu khí động học, các đường gân bắt sáng chạy dọc thân và dải đèn kéo dài tạo nên nhận diện rõ ràng cả khi đứng yên lẫn khi chuyển động.</p>
    </div>
    <div style="height: 520px; border-radius: 16px; background: repeating-linear-gradient(45deg, rgb(236, 236, 234) 0px, rgb(236, 236, 234) 16px, rgb(245, 245, 243) 16px, rgb(245, 245, 243) 32px); display: flex; align-items: center; justify-content: center;"><span style="font-family: ui-monospace, Menlo, monospace; font-size: 12px; color: rgb(152, 152, 143);">[ <span class="sc-interp">VF 7</span> góc 3/4 đang chuyển động ]</span></div>
  </div>

  
  <div style="max-width: 1320px; margin: 0px auto; padding: 0px 64px 40px;">
    <h2 style="margin: 0px 0px 20px; font-size: 42px; line-height: 1.15; font-weight: 600; letter-spacing: -0.02em; color: rgb(23, 23, 23); max-width: 760px;">Điểm nhấn công nghệ, nâng cấp trải nghiệm</h2>
    <p style="margin: 0px 0px 44px; font-size: 17px; line-height: 1.7; color: rgb(85, 85, 85); max-width: 720px;">Quản lý xe từ xa qua ứng dụng, cập nhật phần mềm không cần tới xưởng và hệ thống âm thanh dành cho những chuyến đi cùng gia đình.</p>
  </div>
  <div style="max-width: 1320px; margin: 0px auto; padding: 0px 64px 120px;">
    <div style="position: relative; height: 600px; border-radius: 16px; overflow: hidden; background: repeating-linear-gradient(45deg, rgb(230, 230, 227) 0px, rgb(230, 230, 227) 16px, rgb(240, 240, 238) 16px, rgb(240, 240, 238) 32px); display: flex; align-items: center; justify-content: center; opacity: 1; transition: opacity 0.35s;">
      <span style="font-family: ui-monospace, Menlo, monospace; font-size: 12px; color: rgb(152, 152, 143);">[ <span class="sc-interp">Khoang lái toàn cảnh — vô lăng và màn hình</span> ]</span>
    </div>
    <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 20px;">
      <div style="font-size: 15px; color: rgb(85, 85, 85);"><span class="sc-interp">Khoang lái toàn cảnh — vô lăng và màn hình</span></div>
      <div style="display: flex; align-items: center; gap: 20px;">
        <span style="font-family: ui-monospace, Menlo, monospace; font-size: 13px; color: rgb(102, 102, 102);"><span class="sc-interp">01 / 03</span></span>
        <span class="scpf" style="width: 44px; height: 44px; border: 1px solid rgb(231, 231, 231); display: flex; align-items: center; justify-content: center; font-size: 18px; color: rgb(23, 23, 23); cursor: pointer;">‹</span>
        <span class="scpf" style="width: 44px; height: 44px; border: 1px solid rgb(231, 231, 231); display: flex; align-items: center; justify-content: center; font-size: 18px; color: rgb(23, 23, 23); cursor: pointer;">›</span>
      </div>
    </div>
  </div>

  
  <div style="max-width: 1320px; margin: 0px auto; padding: 0px 64px 120px; display: grid; grid-template-columns: 1fr 1fr; gap: 96px; align-items: center;">
    <div style="height: 520px; border-radius: 16px; background: repeating-linear-gradient(45deg, rgb(38, 38, 42) 0px, rgb(38, 38, 42) 16px, rgb(30, 30, 34) 16px, rgb(30, 30, 34) 32px); display: flex; align-items: center; justify-content: center;"><span style="font-family: ui-monospace, Menlo, monospace; font-size: 12px; color: rgb(138, 138, 144);">[ vô lăng &amp; màn hình trung tâm ]</span></div>
    <div>
      <h2 style="margin: 0px 0px 20px; font-size: 42px; line-height: 1.15; font-weight: 600; letter-spacing: -0.02em; color: rgb(23, 23, 23);">Nội thất khoáng đạt — nâng tầm tiện nghi</h2>
      <p style="margin: 0px; font-size: 17px; line-height: 1.7; color: rgb(85, 85, 85); max-width: 460px;">Ghế ngồi cải tiến về chất liệu và kiểu dáng, ôm sát cơ thể và nâng đỡ tốt hơn ở những chặng đường dài. Không gian rộng rãi cho cả hàng ghế sau.</p>
    </div>
  </div>

  
  <div style="background: rgb(245, 246, 247); padding: 110px 0px;">
    <div style="max-width: 1320px; margin: 0px auto; padding: 0px 64px;">
      <h2 style="margin: 0px 0px 40px; font-size: 46px; line-height: 1.12; font-weight: 600; letter-spacing: -0.02em; color: rgb(23, 23, 23);">Nâng cấp trải nghiệm<br>thực tế mỗi ngày</h2>
      <div style="display: flex; gap: 40px; border-bottom: 1px solid rgb(224, 225, 226); margin-bottom: 40px;">
        
          <span style="padding: 0px 0px 18px; font-size: 15px; font-weight: 600; color: rgb(20, 100, 244); border-bottom: 2px solid rgb(20, 100, 244); cursor: pointer; transition: color 0.2s;"><span class="sc-interp">01</span> — <span class="sc-interp">Kiến trúc điện – điện tử</span></span>
        
          <span style="padding: 0px 0px 18px; font-size: 15px; font-weight: 600; color: rgb(138, 138, 138); border-bottom: 2px solid transparent; cursor: pointer; transition: color 0.2s;"><span class="sc-interp">02</span> — <span class="sc-interp">Hỗ trợ lái ADAS</span></span>
        
          <span style="padding: 0px 0px 18px; font-size: 15px; font-weight: 600; color: rgb(138, 138, 138); border-bottom: 2px solid transparent; cursor: pointer; transition: color 0.2s;"><span class="sc-interp">03</span> — <span class="sc-interp">Trợ lý ảo</span></span>
        
          <span style="padding: 0px 0px 18px; font-size: 15px; font-weight: 600; color: rgb(138, 138, 138); border-bottom: 2px solid transparent; cursor: pointer; transition: color 0.2s;"><span class="sc-interp">04</span> — <span class="sc-interp">Hệ thống treo thích ứng</span></span>
        
      </div>
      <div style="height: 600px; border-radius: 16px; background: repeating-linear-gradient(45deg, rgb(230, 230, 227) 0px, rgb(230, 230, 227) 16px, rgb(240, 240, 238) 16px, rgb(240, 240, 238) 32px); display: flex; align-items: center; justify-content: center; opacity: 1; transition: opacity 0.35s; margin-bottom: 32px;">
        <span style="font-family: ui-monospace, Menlo, monospace; font-size: 12px; color: rgb(152, 152, 143);">[ <span class="sc-interp">màn hình trung tâm hiển thị trạng thái xe</span> ]</span>
      </div>
      <div style="max-width: 720px; opacity: 1; transition: opacity 0.35s;">
        <h3 style="margin: 0px 0px 12px; font-size: 30px; font-weight: 600; letter-spacing: -0.01em; color: rgb(23, 23, 23);"><span class="sc-interp">Kiến trúc điện – điện tử thế hệ mới</span></h3>
        <p style="margin: 0px; font-size: 17px; line-height: 1.7; color: rgb(85, 85, 85);"><span class="sc-interp">Nền tảng điện – điện tử tập trung cho phép cập nhật phần mềm từ xa, bổ sung tính năng mới cho xe mà không cần tới xưởng dịch vụ.</span></p>
      </div>
    </div>
  </div>

  
  <div style="max-width: 1320px; margin: 0px auto; padding: 110px 64px 0px;">
    <h2 style="margin: 0px 0px 44px; font-size: 42px; font-weight: 600; letter-spacing: -0.02em; color: rgb(23, 23, 23);">Thông số kỹ thuật</h2>
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 0px 48px; border-top: 1px solid rgb(227, 227, 227);">
      
        <div style="padding: 24px 0px; border-bottom: 1px solid rgb(227, 227, 227);">
          <div style="font-size: 13px; color: rgb(102, 102, 102); margin-bottom: 8px;"><span class="sc-interp">Dài × rộng × cao (mm)</span></div>
          <div style="font-size: 19px; font-weight: 600; color: rgb(23, 23, 23);"><span class="sc-interp">4545 × 1890 × 1636</span></div>
        </div>
      
        <div style="padding: 24px 0px; border-bottom: 1px solid rgb(227, 227, 227);">
          <div style="font-size: 13px; color: rgb(102, 102, 102); margin-bottom: 8px;"><span class="sc-interp">Chiều dài cơ sở</span></div>
          <div style="font-size: 19px; font-weight: 600; color: rgb(23, 23, 23);"><span class="sc-interp">2840 mm</span></div>
        </div>
      
        <div style="padding: 24px 0px; border-bottom: 1px solid rgb(227, 227, 227);">
          <div style="font-size: 13px; color: rgb(102, 102, 102); margin-bottom: 8px;"><span class="sc-interp">Khoảng sáng gầm xe</span></div>
          <div style="font-size: 19px; font-weight: 600; color: rgb(23, 23, 23);"><span class="sc-interp">190 mm</span></div>
        </div>
      
        <div style="padding: 24px 0px; border-bottom: 1px solid rgb(227, 227, 227);">
          <div style="font-size: 13px; color: rgb(102, 102, 102); margin-bottom: 8px;"><span class="sc-interp">Số chỗ ngồi</span></div>
          <div style="font-size: 19px; font-weight: 600; color: rgb(23, 23, 23);"><span class="sc-interp">5 chỗ</span></div>
        </div>
      
        <div style="padding: 24px 0px; border-bottom: 1px solid rgb(227, 227, 227);">
          <div style="font-size: 13px; color: rgb(102, 102, 102); margin-bottom: 8px;"><span class="sc-interp">Công suất tối đa</span></div>
          <div style="font-size: 19px; font-weight: 600; color: rgb(23, 23, 23);"><span class="sc-interp">260 kW</span></div>
        </div>
      
        <div style="padding: 24px 0px; border-bottom: 1px solid rgb(227, 227, 227);">
          <div style="font-size: 13px; color: rgb(102, 102, 102); margin-bottom: 8px;"><span class="sc-interp">Mô-men xoắn cực đại</span></div>
          <div style="font-size: 19px; font-weight: 600; color: rgb(23, 23, 23);"><span class="sc-interp">500 Nm</span></div>
        </div>
      
        <div style="padding: 24px 0px; border-bottom: 1px solid rgb(227, 227, 227);">
          <div style="font-size: 13px; color: rgb(102, 102, 102); margin-bottom: 8px;"><span class="sc-interp">Quãng đường/lần sạc</span></div>
          <div style="font-size: 19px; font-weight: 600; color: rgb(23, 23, 23);"><span class="sc-interp">496 km</span></div>
        </div>
      
        <div style="padding: 24px 0px; border-bottom: 1px solid rgb(227, 227, 227);">
          <div style="font-size: 13px; color: rgb(102, 102, 102); margin-bottom: 8px;"><span class="sc-interp">Dung lượng pin khả dụng</span></div>
          <div style="font-size: 19px; font-weight: 600; color: rgb(23, 23, 23);"><span class="sc-interp">75,3 kWh</span></div>
        </div>
      
        <div style="padding: 24px 0px; border-bottom: 1px solid rgb(227, 227, 227);">
          <div style="font-size: 13px; color: rgb(102, 102, 102); margin-bottom: 8px;"><span class="sc-interp">Sạc nhanh tối đa</span></div>
          <div style="font-size: 19px; font-weight: 600; color: rgb(23, 23, 23);"><span class="sc-interp">DC 150 kW</span></div>
        </div>
      
        <div style="padding: 24px 0px; border-bottom: 1px solid rgb(227, 227, 227);">
          <div style="font-size: 13px; color: rgb(102, 102, 102); margin-bottom: 8px;"><span class="sc-interp">Dẫn động</span></div>
          <div style="font-size: 19px; font-weight: 600; color: rgb(23, 23, 23);"><span class="sc-interp">AWD 2 cầu</span></div>
        </div>
      
        <div style="padding: 24px 0px; border-bottom: 1px solid rgb(227, 227, 227);">
          <div style="font-size: 13px; color: rgb(102, 102, 102); margin-bottom: 8px;"><span class="sc-interp">La-zăng</span></div>
          <div style="font-size: 19px; font-weight: 600; color: rgb(23, 23, 23);"><span class="sc-interp">Mâm 19–20"</span></div>
        </div>
      
        <div style="padding: 24px 0px; border-bottom: 1px solid rgb(227, 227, 227);">
          <div style="font-size: 13px; color: rgb(102, 102, 102); margin-bottom: 8px;"><span class="sc-interp">Màn hình trung tâm</span></div>
          <div style="font-size: 19px; font-weight: 600; color: rgb(23, 23, 23);"><span class="sc-interp">12,9"</span></div>
        </div>
      
    </div>
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 64px; margin-top: 64px;">
      <div>
        <h3 style="margin: 0px 0px 16px; font-size: 24px; font-weight: 600; color: rgb(23, 23, 23);">An toàn &amp; an ninh</h3>
        <p style="margin: 0px; font-size: 16px; line-height: 1.9; color: rgb(85, 85, 85);">Tự động khóa cửa khi xe di chuyển · Cảnh báo chống trộm · Giám sát áp suất lốp dTPMS · Camera 360 độ · Khung xe đạt chuẩn an toàn khu vực.</p>
      </div>
      <div>
        <h3 style="margin: 0px 0px 16px; font-size: 24px; font-weight: 600; color: rgb(23, 23, 23);">Hỗ trợ lái nâng cao ADAS</h3>
        <p style="margin: 0px; font-size: 16px; line-height: 1.9; color: rgb(85, 85, 85);">Trợ lái trên cao tốc · Ga tự động thích ứng · Phanh khẩn cấp tự động AEB · Giữ làn và cảnh báo chệch làn · Đèn chiếu xa tự động.</p>
      </div>
    </div>
    <div style="display: flex; gap: 14px; margin: 56px 0px 40px;">
      <span class="scpg" style="display: inline-flex; align-items: center; height: 52px; padding: 0px 34px; border: 1px solid rgb(20, 100, 244); color: rgb(20, 100, 244); font-size: 14px; font-weight: 700; letter-spacing: 0.06em; text-transform: uppercase; white-space: nowrap; cursor: pointer;">Tải brochure</span>
      <span class="scpb" style="display: inline-flex; align-items: center; height: 52px; padding: 0px 40px; background: rgb(20, 100, 244); color: rgb(255, 255, 255); font-size: 14px; font-weight: 700; letter-spacing: 0.06em; text-transform: uppercase; white-space: nowrap; cursor: pointer; transition: background 0.2s, transform 0.2s;">Đặt cọc ngay</span>
    </div>
    <div style="max-width: 1100px; font-size: 12px; line-height: 1.7; color: rgb(102, 102, 102); padding-bottom: 110px;">
      (*) Lưu ý: Một số tính năng có thể được bổ sung qua cập nhật phần mềm. Quãng đường di chuyển tính theo chu trình kiểm định NEDC, thực tế thay đổi tùy tốc độ, nhiệt độ, địa hình, tải trọng và thói quen lái. Hình ảnh và thông số mang tính minh họa, phiên bản thương mại có thể khác biệt và có thể thay đổi mà không cần báo trước.
    </div>
  </div>

  
  <div style="background: rgb(245, 246, 247); padding: 110px 0px;">
    <div style="max-width: 1320px; margin: 0px auto; padding: 0px 64px;">
      <h2 style="margin: 0px 0px 48px; font-size: 38px; line-height: 1.2; font-weight: 600; letter-spacing: -0.02em; color: rgb(23, 23, 23); max-width: 900px;">So sánh giữa <span class="sc-interp">VF 7</span> và xe động cơ đốt trong</h2>
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; align-items: stretch;">
        <div style="background: rgb(255, 255, 255); border: 1px solid rgb(231, 231, 231); border-radius: 14px; padding: 40px;">
          <div style="font-size: 15px; color: rgb(85, 85, 85); margin-bottom: 26px;">Vui lòng nhập thông tin xe động cơ đốt trong cần so sánh:</div>
          <div style="margin-bottom: 24px;">
            <div style="font-size: 13px; font-weight: 600; color: rgb(23, 23, 23); margin-bottom: 10px;">Loại nhiên liệu</div>
            <div style="display: inline-flex; border: 1px solid rgb(217, 217, 217); border-radius: 6px; overflow: hidden;">
              <span style="display: inline-flex; align-items: center; height: 46px; padding: 0px 32px; background: rgb(17, 17, 17); color: rgb(255, 255, 255); font-size: 14px; font-weight: 600; cursor: pointer;">Xăng</span>
              <span style="display: inline-flex; align-items: center; height: 46px; padding: 0px 32px; background: rgb(255, 255, 255); color: rgb(58, 58, 56); font-size: 14px; font-weight: 600; cursor: pointer;">Dầu</span>
            </div>
          </div>
          <div style="margin-bottom: 24px;">
            <div style="font-size: 13px; font-weight: 600; color: rgb(23, 23, 23); margin-bottom: 10px;">Mức tiêu thụ nhiên liệu / 100 km *</div>
            <input class="scph" value="8" style="width: 100%; box-sizing: border-box; height: 54px; border: 1px solid rgb(217, 217, 217); border-radius: 6px; padding: 0px 18px; font-size: 15px; color: rgb(23, 23, 23); font-family: inherit; outline: none;">
          </div>
          <div style="margin-bottom: 28px;">
            <div style="font-size: 13px; font-weight: 600; color: rgb(23, 23, 23); margin-bottom: 10px;">Quãng đường di chuyển / tháng (km) *</div>
            <input class="scph" value="1200" style="width: 100%; box-sizing: border-box; height: 54px; border: 1px solid rgb(217, 217, 217); border-radius: 6px; padding: 0px 18px; font-size: 15px; color: rgb(23, 23, 23); font-family: inherit; outline: none;">
          </div>
          <div style="font-size: 12px; line-height: 1.6; color: rgb(102, 102, 102);">(*) Nhập số, dùng dấu chấm "." cho phần thập phân. Ví dụ: 8.56 lít, 1023.56 km. Giá điện tạm tính 3.900 ₫/kWh khi sạc tại nhà.</div>
        </div>
        <div style="background: rgb(255, 255, 255); border: 1px solid rgb(231, 231, 231); border-radius: 14px; padding: 40px; display: flex; flex-direction: column;">
          <div style="height: 180px; border-radius: 10px; background: rgb(242, 241, 236); display: flex; align-items: center; justify-content: center; margin-bottom: 28px;">
            <span style="font-family: ui-monospace, Menlo, monospace; font-size: 11px; color: rgba(255, 255, 255, 0.6);">[ render <span class="sc-interp">VF 7</span> ]</span>
          </div>
          <div style="font-size: 19px; font-weight: 600; color: rgb(23, 23, 23); margin-bottom: 20px;">Lợi thế chi phí nhiên liệu của <span class="sc-interp">VF 7</span></div>
          <div style="display: flex; justify-content: space-between; align-items: baseline; padding: 14px 0px; border-bottom: 1px solid rgb(238, 238, 238);"><span style="font-size: 15px; color: rgb(85, 85, 85);">Chi phí nhiên liệu xe xăng/dầu · tháng</span><span style="font-size: 19px; font-weight: 700; color: rgb(23, 23, 23);"><span class="sc-interp">2.352.000 ₫</span></span></div>
          <div style="display: flex; justify-content: space-between; align-items: baseline; padding: 14px 0px; border-bottom: 1px solid rgb(238, 238, 238);"><span style="font-size: 15px; color: rgb(85, 85, 85);">Chi phí điện <span class="sc-interp">VF 7</span> · tháng</span><span style="font-size: 19px; font-weight: 700; color: rgb(23, 23, 23);"><span class="sc-interp">710.492 ₫</span></span></div>
          <div style="display: flex; justify-content: space-between; align-items: baseline; padding: 20px 0px 0px;"><span style="font-size: 15px; font-weight: 600; color: rgb(23, 23, 23);">Tiết kiệm mỗi năm</span><span style="font-size: 32px; font-weight: 700; color: rgb(20, 100, 244);"><span class="sc-interp">19.698.097 ₫</span></span></div>
        </div>
      </div>
    </div>
  </div>

  
  <div style="max-width: 1320px; margin: 0px auto; padding: 110px 64px;">
    <div style="max-width: 620px; margin: 0px auto;">
      <h2 style="margin: 0px 0px 12px; font-size: 38px; font-weight: 600; letter-spacing: -0.02em; color: rgb(23, 23, 23); text-align: center;">Đăng ký tư vấn</h2>
      <p style="margin: 0px 0px 40px; font-size: 16px; line-height: 1.6; color: rgb(85, 85, 85); text-align: center;">Vui lòng để lại thông tin, đại lý sẽ cập nhật cho Quý khách thông tin sản phẩm và ưu đãi mới nhất.</p>
      
        <div style="display: flex; flex-direction: column; gap: 18px;">
          <div><div style="font-size: 13px; font-weight: 600; color: rgb(23, 23, 23); margin-bottom: 8px;">Họ và tên *</div><input placeholder="Nguyễn Văn A" class="scph" value="" style="width: 100%; box-sizing: border-box; height: 54px; border: 1px solid rgb(217, 217, 217); border-radius: 6px; padding: 0px 18px; font-size: 15px; color: rgb(23, 23, 23); font-family: inherit; outline: none;"></div>
          <div><div style="font-size: 13px; font-weight: 600; color: rgb(23, 23, 23); margin-bottom: 8px;">Số điện thoại *</div><input placeholder="09xx xxx xxx" class="scph" value="" style="width: 100%; box-sizing: border-box; height: 54px; border: 1px solid rgb(217, 217, 217); border-radius: 6px; padding: 0px 18px; font-size: 15px; color: rgb(23, 23, 23); font-family: inherit; outline: none;"></div>
          <div><div style="font-size: 13px; font-weight: 600; color: rgb(23, 23, 23); margin-bottom: 8px;">Email *</div><input placeholder="ban&#64;email.com" class="scph" value="" style="width: 100%; box-sizing: border-box; height: 54px; border: 1px solid rgb(217, 217, 217); border-radius: 6px; padding: 0px 18px; font-size: 15px; color: rgb(23, 23, 23); font-family: inherit; outline: none;"></div>
          <div style="display: flex; gap: 12px; align-items: flex-start; cursor: pointer; margin-top: 6px;">
            <span style="flex: 0 0 auto; width: 20px; height: 20px; border: 1px solid rgb(217, 217, 217); border-radius: 3px; background: rgb(255, 255, 255); display: inline-flex; align-items: center; justify-content: center; color: rgb(255, 255, 255); font-size: 12px; margin-top: 1px;"><span class="sc-interp"></span></span>
            <span style="font-size: 13.5px; line-height: 1.6; color: rgb(85, 85, 85);">Tôi đồng ý cho đại lý xử lý dữ liệu cá nhân của tôi theo chính sách bảo vệ dữ liệu.</span>
          </div>
          
          <span class="scpb" style="margin-top: 8px; display: inline-flex; align-items: center; justify-content: center; height: 54px; background: rgb(20, 100, 244); color: rgb(255, 255, 255); font-size: 14px; font-weight: 700; letter-spacing: 0.06em; text-transform: uppercase; white-space: nowrap; cursor: pointer; transition: background 0.2s, transform 0.2s;">Đăng ký tư vấn</span>
        </div>
      
      
    </div>
  </div>

  <div style="height: 88px;"></div>

  
  <div style="position: fixed; left: 0px; right: 0px; bottom: 0px; z-index: 60; background: rgb(255, 255, 255); border-top: 1px solid rgb(231, 231, 231); box-shadow: rgba(0, 0, 0, 0.06) 0px -6px 24px; transform: translateY(0px); transition: transform 0.35s cubic-bezier(0.22, 1, 0.36, 1);">
    <div style="max-width: 1320px; margin: 0px auto; padding: 14px 64px; display: flex; align-items: center; justify-content: space-between; gap: 24px;">
      <div style="display: flex; align-items: center; gap: 16px;">
        <div style="width: 56px; height: 40px; border-radius: 4px; background: rgb(242, 241, 236);"></div>
        <div><div style="font-size: 15px; font-weight: 700; color: rgb(23, 23, 23);"><span class="sc-interp">VF 7</span> · <span class="sc-interp">Trắng ngọc trai</span></div><div style="font-size: 12.5px; color: rgb(102, 102, 102);">Từ <span class="sc-interp">799 triệu</span></div></div>
      </div>
      <div style="display: flex; gap: 10px;">
        <span class="scpf" style="display: inline-flex; align-items: center; height: 46px; padding: 0px 24px; border: 1px solid rgb(217, 217, 217); color: rgb(23, 23, 23); font-size: 13.5px; font-weight: 600; cursor: pointer;">Lái thử</span>
        <span class="scpi" style="display: inline-flex; align-items: center; height: 46px; padding: 0px 28px; background: rgb(20, 100, 244); color: rgb(255, 255, 255); font-size: 13.5px; font-weight: 700; letter-spacing: 0.04em; text-transform: uppercase; cursor: pointer;">Đặt cọc ngay</span>
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
