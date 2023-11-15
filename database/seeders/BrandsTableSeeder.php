<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class BrandsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(){
        $brands = ['Xe Bus', 'Tải', 'Du lịch', 'Royal', '2 bánh', 'Nông nghiệp'];

        foreach ($brands as $brand) {
            Brand::create(['name' => $brand]);
        }
    }

}
