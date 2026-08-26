{{--
    Bảng thông số theo bản thiết kế: lưới 4 cột phẳng, nhãn nhỏ ở trên, giá
    trị đậm ở dưới — không gấp theo nhóm.

    Hai đoạn ghi chú xếp cạnh nhau bên dưới ("An toàn & an ninh", "Hỗ trợ lái
    nâng cao ADAS" trong bản thiết kế) đến từ cột `spec_notes` riêng, không
    trộn vào `specs` — trộn vào thì chúng hiện lên repeater thông số trong
    admin như một nhóm bình thường, người nhập sửa nhầm là hỏng.

    Biến: $specs · $notes (tuỳ chọn)
--}}
@php
    $notes = $notes ?? [];

    // Thiết kế xếp mọi thông số vào một lưới phẳng, không chia nhóm gấp mở.
    $rows = collect($specs)
        ->flatMap(fn ($group) => $group['rows'] ?? [])
        ->filter(fn ($row) => filled($row['label'] ?? null) || filled($row['value'] ?? null));
@endphp

@if ($rows->isNotEmpty())
    <dl class="spec-flat">
        @foreach ($rows as $row)
            <div>
                <dt>{{ $row['label'] ?? '' }}</dt>
                <dd>{{ $row['value'] ?? '' }}</dd>
            </div>
        @endforeach
    </dl>
@endif

@if (filled($notes))
    <div class="spec-notes">
        @foreach ($notes as $note)
            <div>
                <h3>{{ $note['label'] ?? '' }}</h3>
                <p>{{ $note['body'] ?? '' }}</p>
            </div>
        @endforeach
    </div>
@endif
