@php
    $floatingFacebook = trim((string) catalog_setting('facebook'));
    $floatingHotline = trim((string) catalog_setting('hotline'));
    $floatingZalo = trim((string) catalog_setting('zalo'));
    $floatingPhoneHref = preg_replace('/\s+/', '', $floatingHotline);
    $floatingZaloHref = str_starts_with($floatingZalo, 'http://') || str_starts_with($floatingZalo, 'https://')
        ? $floatingZalo
        : 'https://zalo.me/'.preg_replace('/\D+/', '', $floatingZalo);
@endphp

@if ($floatingFacebook !== '' || $floatingHotline !== '' || $floatingZalo !== '')
    <nav class="floating-contact" aria-label="Liên hệ nhanh">
        @if ($floatingFacebook !== '')
            <a class="floating-contact__item floating-contact__item--facebook"
               href="{{ $floatingFacebook }}" target="_blank" rel="noopener noreferrer"
               aria-label="Nhắn Facebook" title="Facebook">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M13.5 22v-8h2.7l.4-3h-3.1V9.1c0-.9.3-1.5 1.6-1.5h1.7V4.9c-.3 0-1.3-.1-2.5-.1-2.5 0-4.2 1.5-4.2 4.3V11H7.3v3h2.8v8h3.4Z"/>
                </svg>
                <span class="sr-only">Nhắn Facebook</span>
            </a>
        @endif

        @if ($floatingHotline !== '')
            <a class="floating-contact__item floating-contact__item--phone"
               href="tel:{{ $floatingPhoneHref }}" aria-label="Gọi {{ $floatingHotline }}"
               title="Gọi {{ $floatingHotline }}">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M7.2 3.5 9.7 8l-2 1.7a15.5 15.5 0 0 0 6.6 6.6l1.7-2 4.5 2.5-.8 3.2c-.2.8-.9 1.3-1.7 1.3C9.6 20.7 3.3 14.4 2.7 6c-.1-.8.5-1.5 1.3-1.7l3.2-.8Z"/>
                </svg>
                <span class="sr-only">Gọi {{ $floatingHotline }}</span>
            </a>
        @endif

        @if ($floatingZalo !== '')
            <a class="floating-contact__item floating-contact__item--zalo"
               href="{{ $floatingZaloHref }}" target="_blank" rel="noopener noreferrer"
               aria-label="Nhắn Zalo" title="Zalo">
                <span class="floating-contact__zalo-mark" aria-hidden="true">Zalo</span>
                <span class="sr-only">Nhắn Zalo</span>
            </a>
        @endif
    </nav>
@endif
