<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('name'); // ユーザー名
                $table->string('email')->unique(); // メールアドレス
                $table->string('microsoft_id')->unique(); // Microsoft ID
                $table->string('password')->nullable(); // パスワード（必要に応じて）
                $table->timestamps(); // 作成日時と更新日時
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
