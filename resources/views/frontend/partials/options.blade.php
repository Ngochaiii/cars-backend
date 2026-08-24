{{--
    Bảng màu / chất liệu. Có ảnh thì lấy ảnh làm ô tròn, không có thì dùng
    mã màu hex. Không có cả hai thì chỉ còn cái tên — vẫn đọc được.
--}}
<ul class="swatches" style="justify-content:center">
    @foreach ($options as $option)
        @php $img = catalog_image($option->image); @endphp
        <li class="swatch">
            <span class="swatch__chip"
                  @if ($img) style="background-image: url('{{ $img }}')"
                  @elseif ($option->hex) style="background-color: {{ $option->hex }}"
                  @endif
                  aria-hidden="true"></span>
            {{ $option->name }}
        </li>
    @endforeach
</ul>
