<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            BrandsTableSeeder::class,
            DepartmentsTableSeeder::class,
            PositionsTableSeeder::class,
            AppFunctionsTableSeeder::class,
            UsersTableSeeder::class,
            ProviderSeeder::class,
            SegmentTableSeeder::class,
        ]);
    }
}
