<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SegmentTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $segments = [
            ['brand_id' => 2, 'name' => 'Tải cơ sở'],
            ['brand_id' => 2, 'name' => 'Tải chuyên dụng'],
        ];

        foreach ($segments as $segment) {
            DB::table('segments')->insert($segment);
        }
    }
}
