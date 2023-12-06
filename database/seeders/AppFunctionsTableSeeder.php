<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\AppFunction;
use Illuminate\Database\Seeder;

class AppFunctionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
     public function run()
    {
        $functions = ['Nhập kho', 'Xuất kho', 'Quản lý kế hoạch', 'Quản trị', 'Quản lý kho'];

        foreach ($functions as $function) {
            AppFunction::create(['name' => $function]);
        }
    }
}
