<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->string('mail')->nullable()->after('class_name'); // メールアドレスカラム
            $table->boolean('is_first_login')->default(true)->after('mail'); // 初回ログイン判定カラム
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->dropColumn('mail');
            $table->dropColumn('is_first_login');
        });
    }
};
