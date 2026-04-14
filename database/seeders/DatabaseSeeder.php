<?php

namespace Database\Seeders;

use DeepCopy\f002\A;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     * Chạy các seeder để tạo dữ liệu mẫu
     */
    public function run(): void
    {
        // Chạy seeder toàn diện - tạo admin + dữ liệu mẫu đầy đủ
        $this->call(ComprehensiveDataSeeder::class);
    }
}
