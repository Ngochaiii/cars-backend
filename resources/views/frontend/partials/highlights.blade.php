{{-- Chỉ số nổi bật: 349 mã lực · 3.4 L — tuỳ mặt hàng, người nhập tự gõ. --}}
<ul class="highlights">
    @foreach ($highlights as $h)
        <li>
            <b>{{ $h['value'] ?? '' }} {{ $h['unit'] ?? '' }}</b>
            <span>{{ $h['label'] ?? '' }}</span>
        </li>
    @endforeach
</ul>
