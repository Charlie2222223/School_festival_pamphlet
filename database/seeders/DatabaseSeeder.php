<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Classes;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        for ($i = 1; $i <= 20; $i++) {
            Classes::create([
                'class_name' => "クラス{$i}",
                'password' => "password{$i}",
                'shop_name' => "ショップ{$i}",
                'class_room' => "Room " . (100 + $i),
                'comment' => "これはクラス{$i}のテストデータです。",
                'image_path' => "image{$i}.jpg",
                'html_code' => "<div>HTML content {$i}</div>",
                'css_code' => "div { color: hsl(" . ($i * 18) . ", 70%, 50%); }",
                'js_code' => "console.log('Hello JS {$i}');",
            ]);
        }
    }
}
