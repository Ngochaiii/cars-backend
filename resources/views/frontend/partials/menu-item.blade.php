{{--
    Một mục menu, gọi lại chính nó cho mục con — menu sâu bao nhiêu cấp cũng
    render được. URL do MenuItem::resolvedUrl() suy ra (gõ tay hoặc từ target).
    Mục không suy được URL thì vẫn hiện nhãn, chỉ không bấm được.
--}}
@php
    $url      = $item->resolvedUrl();
    $children = $item->children;
@endphp

<li class="nav__item">
    @if ($url)
        <a class="nav__link" href="{{ $url }}">{{ $item->label }}</a>
    @else
        <span class="nav__link">{{ $item->label }}</span>
    @endif

    @if ($children->isNotEmpty())
        <ul class="nav__sub">
            @foreach ($children as $child)
                @include('frontend.partials.menu-item', ['item' => $child])
            @endforeach
        </ul>
    @endif
</li>
