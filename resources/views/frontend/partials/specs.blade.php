{{--
    Bảng thông số theo nhóm. Nhóm đầu mở sẵn, các nhóm sau gấp lại bằng
    <details> — native, không JS.
--}}
<div class="specs">
    @foreach ($specs as $i => $group)
        <details @if ($i === 0) open @endif>
            <summary>{{ $group['group'] ?? 'Thông số' }}</summary>

            <div class="table-scroll">
                <table class="data-table">
                    <tbody>
                        @foreach ($group['rows'] ?? [] as $row)
                            <tr>
                                <th scope="row">{{ $row['label'] ?? '' }}</th>
                                <td>{{ $row['value'] ?? '' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </details>
    @endforeach
</div>
