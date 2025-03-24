<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Classes;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $types = ['R', 'J', 'S'];

        foreach ($types as $type) {
            for ($i = 1; $i <= 7; $i++) {
                $index = "{$type}{$i}";
                Classes::create([
                    'class_name' => "{$type}クラス{$i}",
                    'password' => Hash::make("password{$index}"),
                    'shop_name' => "{$type}ショップ{$i}",
                    'class_room' => "Room " . rand(100, 999),
                    'comment' => "{$type}クラス{$i}のテストデータです。",
                    'image_path' => "{$type}_image{$i}.jpg",
                ]);
            }
        }
    }
}