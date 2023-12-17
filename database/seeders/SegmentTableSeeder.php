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
            ['brand_id' => 1, 'name' => 'Bus chuyên dùng'],
            ['brand_id' => 1, 'name' => 'minibus'],
            ['brand_id' => 1, 'name' => 'bus ngáo'],
        ];

        foreach ($segments as $segment) {
            DB::table('segments')->insert($segment);
        }
    }
}
