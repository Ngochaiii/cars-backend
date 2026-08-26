<?php

namespace Database\Seeders;

use App\Support\Catalog;
use Illuminate\Database\Seeder;

/**
 * Thông tin PHÁP NHÂN THẬT của đại lý đang vận hành site này.
 *
 * Tách khỏi CatalogDemoSeeder vì hai thứ khác hẳn nhau: seeder demo đặt chữ
 * mẫu ("Website ô tô demo") để soi thử admin, còn file này đặt dữ liệu thật.
 * Chạy demo seeder sau file này là ghi đè mất — chạy CompanySeeder sau cùng.
 *
 * Nguồn: hoá đơn GTGT do chi nhánh phát hành. CỐ Ý chỉ lấy phần bên BÁN.
 * Thông tin bên mua (họ tên, số CCCD, địa chỉ nhà, số khung/số máy xe) là dữ
 * liệu cá nhân của khách — không có chỗ nào trên website công khai được.
 *
 * Chưa có số liệu thật thì để trống, KHÔNG bịa: hotline và email sai còn tệ
 * hơn không có, khách gọi vào số không tồn tại là mất khách thật.
 */
class CompanySeeder extends Seeder
{
    public function run(): void
    {
        $setting = Catalog::model('setting');

        // Tên thương mại hiển thị khắp site.
        $setting::put('site_name', 'VinFast Bắc Giang');
        $setting::put('brand_sub', 'Bắc Giang');

        // Tên pháp nhân + MST: chỉ dùng ở dòng bản quyền cuối trang.
        $setting::put('company_name', 'Công ty Cổ phần Tập đoàn Tương Lai Việt — Chi nhánh Bắc Giang');
        $setting::put('tax_code', '0111166593-007');

        $setting::put('address', 'Tổ dân phố Giáp Sau, Phường Bắc Giang, Tỉnh Bắc Ninh');

        $setting::put('site_description',
            'Đại lý ủy quyền VinFast tại Bắc Giang — báo giá, lái thử và đặt cọc các dòng xe điện VinFast.');
    }
}
