{{--
    Bảng thông số theo bản thiết kế: lưới 4 cột phẳng, nhãn nhỏ ở trên, giá
    trị đậm ở dưới — không gấp theo nhóm.

    Nhóm đặc biệt `__notes` không phải thông số mà là hai đoạn ghi chú xếp
    cạnh nhau ("An toàn & an ninh", "Hỗ trợ lái nâng cao ADAS" trong bản
    thiết kế): nhãn thành tiêu đề, giá trị thành đoạn văn.

    Biến: $specs
--}}
@php
    $groups = collect($specs);
    $notes  = $groups->firstWhere('group', '__notes')['rows'] ?? [];

    // Mọi nhóm còn lại đổ chung vào một lưới — thiết kế không chia nhóm.
    $rows = $groups
        ->reject(fn ($group) => ($group['group'] ?? null) === '__notes')
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
                <p>{{ $note['value'] ?? '' }}</p>
            </div>
        @endforeach
    </div>
@endif
