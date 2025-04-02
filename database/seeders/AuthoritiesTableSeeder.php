<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AuthoritiesTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('authorities')->insert([
            ['id' => 1, 'authority_name' => 'Admin', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'authority_name' => 'User', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}