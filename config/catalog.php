<?php

/*
|--------------------------------------------------------------------------
| Mảng ô tô — cấu hình riêng của site này
|--------------------------------------------------------------------------
|
| File này ghi đè lên config mặc định của package catalog/core.
| Đổi hãng hoặc đổi mặt hàng thì sửa ở đây, KHÔNG chạy migration.
|
| Site nội thất chỉ cần copy file này, đổi labels + section_presets,
| tắt `dealers`/`fee_calc` là xong — cùng codebase, cùng schema.
|
*/

return [

    'labels' => [
        'product'  => ['single' => 'Xe',        'plural' => 'Dòng xe'],
        'variant'  => ['single' => 'Phiên bản', 'plural' => 'Phiên bản'],
        'option'   => ['single' => 'Màu xe',    'plural' => 'Bảng màu'],
        'sections' => 'Chi tiết xe',
        'specs'    => 'Thông số kỹ thuật',
    ],

    'features' => [
        'variants'   => true,
        'options'    => true,
        'specs'      => true,
        'highlights' => true,
        'posts'      => true,
        'pages'      => true,
        'forms'      => true,
        'dealers'    => true,
        'fee_calc'   => true,
    ],

    'section_presets' => ['Thư viện', 'Ngoại thất', 'Nội thất', 'Mâm xe', 'Vận hành'],

];
