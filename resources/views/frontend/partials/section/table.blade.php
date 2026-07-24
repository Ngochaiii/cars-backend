{{--
    Bảng phụ (VD bảng tỷ lệ mua lại). Dòng nhập tay hoặc dán từ HTML —
    cùng parser với `specs`, chỉ khác là phẳng, không chia nhóm.
--}}
<div class="table-scroll">
    <table class="data-table">
        <tbody>
            @foreach ($section['rows'] ?? [] as $row)
                <tr>
                    <th scope="row">{{ $row['label'] ?? '' }}</th>
                    <td>{{ $row['value'] ?? '' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
