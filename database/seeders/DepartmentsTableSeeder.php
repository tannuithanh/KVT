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
            ['name' => 'kế hoạch'],
            ['name' => 'Quản lý chất lượng'],
            ['name' => 'Kho vật tư'],
        ];

        DB::table('departments')->insert($departments);
    }
}
