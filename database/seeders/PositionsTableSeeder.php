<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PositionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $positions = [
            ['name' => 'Giám đốc'],
            ['name' => 'Phó giám đốc'],
            ['name' => 'Phó giám đốc khối thiết kế'],
            ['name' => 'Phó giám đốc xưởng sản xuất mẫu'],
            ['name' => 'Phó giám đốc thử nghiệm & mô phỏng'],
            ['name' => 'Trưởng Phòng'],
            ['name' => 'Phó phòng'],
            ['name' => 'Trưởng bộ phận'],
            ['name' => 'Trưởng nhóm'],
            ['name' => 'Chuyên viên'],
            ['name' => 'Người quản trị'],
        ];

        DB::table('positions')->insert($positions);
    }

}
