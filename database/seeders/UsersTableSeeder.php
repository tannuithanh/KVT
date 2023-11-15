<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('users')->insert([
            'msnv' => 9999,
            'name' => 'Administrator',
            'email' => 'admin@example.com',
            'password' => bcrypt('123456'),
            'is_admin' => 1,
            'position_id' => 11,
        ]);
    }

}
