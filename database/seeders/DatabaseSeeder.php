<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Classes;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {

        $this->call(AuthoritiesTableSeeder::class);

        // 権限を持ったユーザーを3つ作成
        $adminUsers = [
            [
                'class_name' => '管理者クラス1',
                'password' => Hash::make('adminpassword1'),
                'shop_name' => '管理者ショップ1',
                'class_room' => 'Admin Room 101',
                'comment' => '管理者クラス1のテストデータです。',
                'authority_id' => 1, // 権限ID
            ],
        ];

        foreach ($adminUsers as $adminUser) {
            Classes::create($adminUser);
        }

        // 通常のクラスデータを作成
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
                    'authority_id' => 2, // 通常ユーザーの権限ID
                ]);
            }
        }
    }
}