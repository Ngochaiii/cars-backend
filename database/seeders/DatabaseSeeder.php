<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\Brands\VinFastSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Thứ tự có ý nghĩa:
     *   1. CatalogDemoSeeder — khung site: cài đặt, menu, form, trang, tin
     *   2. Brands\*Seeder    — xe của một hãng, gắn vào khung đó
     *
     * Một site chỉ bán một hãng nên bình thường chỉ gọi MỘT brand seeder.
     * Đổi hãng thì đổi đúng dòng cuối — xem database/seeders/Brands/README.md.
     */
    public function run(): void
    {
        User::factory()->create([
            'name'     => 'Admin',
            'email'    => 'admin@cars.test',
            'password' => 'password',
        ]);

        $this->call(CatalogDemoSeeder::class);
        $this->call(VinFastSeeder::class);
    }
}

