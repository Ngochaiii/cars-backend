<ul>
    @foreach ($variants as $v)
        <li>
            {{ $v->name }}
            @if ($v->price) — {{ number_format((float) $v->price, 0, ',', '.') }} đ @endif
        </li>
    @endforeach
</ul>
