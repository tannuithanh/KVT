<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentsTableSeeder extends Seeder
{
    public function run()
    {
        $departments = [
            ['name' => 'Ban lãnh đạo'],
            ['name' => 'Phân tích cấu hình'],
            ['name' => 'Thiết kế kiểu dáng'],
            ['name' => 'Thiết kế quy trình'],
            ['name' => 'Nội địa hóa'],
            ['name' => 'Quản trị dữ liệu'],
            ['name' => 'Thiết kế nội ngoại thất'],
            ['name' => 'Thiết kế động lực học'],
            ['name' => 'Thiết kế Khung gầm'],
            ['name' => 'Thiết kế Điện tử - HTTM'],
            ['name' => 'Kế toán'],
            ['name' => 'kế hoạch'],
            ['name' => 'Thiết kế xe Du Lịch'],
            ['name' => 'Thiết kế xe Royal'],
            ['name' => 'Thiết kế xe Tải'],
            ['name' => 'Thiết kế xe Bus'],
            ['name' => 'Phân tích mô phỏng'],
            ['name' => 'Thử nghiệm'],
            ['name' => 'Quản lý chất lượng'],
            ['name' => 'Kho vật tư'],
            ['name' => 'Xưởng thân vỏ & khung gầm'],
            ['name' => 'Xưởng lắp ráp'],
            ['name' => 'Thiết kế Hệ thống thủy khí'],
        ];

        DB::table('departments')->insert($departments);
    }
}
