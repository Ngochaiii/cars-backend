{{--
    Mã đo lường khai ở Cài đặt → Đo lường. Bỏ trống thì không in gì —
    không nhúng script rỗng, không gọi domain lạ khi chưa cấu hình.
--}}
@php
    $gtm   = catalog_setting('gtm_id');
    $pixel = catalog_setting('facebook_pixel');
    $adsId = catalog_setting('google_ads_id');
    $adsLead = catalog_setting('google_ads_lead');
    $adsContact = catalog_setting('google_ads_contact');
@endphp

{{-- Thẻ Google Ads gắn thẳng, không cần GTM. Gửi form thành công thì
     frontend.js bắn chuyển đổi tới window.leadConversion; bấm link tel:/Zalo
     thì bắn tới window.contactConversion. --}}
@if (filled($adsId))
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $adsId }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', @json($adsId));
        @if (filled($adsLead))
            window.leadConversion = @json($adsLead);
        @endif
        @if (filled($adsContact))
            window.contactConversion = @json($adsContact);
        @endif
    </script>
@endif

@if (filled($gtm))
    <script>
        (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});
        var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';
        j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','{{ $gtm }}');
    </script>
@endif

@if (filled($pixel))
    <script>
        !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
        n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
        document,'script','https://connect.facebook.net/en_US/fbevents.js');
        fbq('init','{{ $pixel }}');fbq('track','PageView');
    </script>
@endif
