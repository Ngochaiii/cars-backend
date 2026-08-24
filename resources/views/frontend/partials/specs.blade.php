{{--
    Bảng thông số theo nhóm. Nhóm đầu mở sẵn, các nhóm sau gấp lại bằng
    <details> — native, không JS.

    Trong mỗi nhóm, các dòng xếp thành lưới nhãn/giá trị (theo thiết kế)
    thay vì bảng — dễ đọc hơn hẳn trên điện thoại.
--}}
<div class="specs">
    @foreach ($specs as $i => $group)
        <details @if ($i === 0) open @endif>
            <summary>{{ $group['group'] ?? 'Thông số' }}</summary>

            <dl class="spec-grid">
                @foreach ($group['rows'] ?? [] as $row)
                    <div>
                        <dt>{{ $row['label'] ?? '' }}</dt>
                        <dd>{{ $row['value'] ?? '' }}</dd>
                    </div>
                @endforeach
            </dl>
        </details>
    @endforeach
</div>
