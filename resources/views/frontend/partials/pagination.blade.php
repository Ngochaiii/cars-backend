{{--
    View phân trang riêng. View mặc định của Laravel in class Tailwind, mà
    frontend này không có Tailwind — markup ở đây là HTML trần, CSS bắt bằng
    .pagination-wrap trong public/css/frontend.css.
--}}
@if ($paginator->hasPages())
    <nav aria-label="Phân trang">
        <ul>
            @if ($paginator->onFirstPage())
                <li aria-disabled="true"><span>‹ Trước</span></li>
            @else
                <li><a href="{{ $paginator->previousPageUrl() }}" rel="prev">‹ Trước</a></li>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <li aria-disabled="true"><span>{{ $element }}</span></li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li aria-current="page"><span>{{ $page }}</span></li>
                        @else
                            <li><a href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <li><a href="{{ $paginator->nextPageUrl() }}" rel="next">Sau ›</a></li>
            @else
                <li aria-disabled="true"><span>Sau ›</span></li>
            @endif
        </ul>

        <p>{{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} trên {{ $paginator->total() }}</p>
    </nav>
@endif
