<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class SegmentTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $segments = [
            ['brand_id' => 2, 'name' => 'Tải cơ sở'],
            ['brand_id' => 2, 'name' => 'Tải chuyên dụng'],
            ['brand_id' => 2, 'name' => 'Thùng Tải'],
            ['brand_id' => 1, 'name' => 'Bus ghế'],
            ['brand_id' => 1, 'name' => 'Bus giường'],
            ['brand_id' => 1, 'name' => 'Bus chuyên dùng'],
            ['brand_id' => 3, 'name' => 'KIA'],
            ['brand_id' => 3, 'name' => 'Mazda'],
            ['brand_id' => 3, 'name' => 'Peugeot'],
            ['brand_id' => 4, 'name' => 'Bus Royal'],
            ['brand_id' => 4, 'name' => 'Du lịch Royal'],
            ['brand_id' => 4, 'name' => 'Minibus Royal'],
        ];

        foreach ($segments as $segment) {
            DB::table('segments')->insert($segment);
        }
    }
}
