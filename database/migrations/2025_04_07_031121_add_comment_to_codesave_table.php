<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCommentToCodesaveTable extends Migration
{
    public function up()
    {
        Schema::table('code_save', function (Blueprint $table) {
            $table->text('comment')->nullable()->after('js_code');
        });
    }

    public function down()
    {
        Schema::table('code_save', function (Blueprint $table) {
            $table->dropColumn('comment'); // カラムを削除
        });
    }
}