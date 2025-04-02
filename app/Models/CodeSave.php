<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CodeSave extends Model
{
    protected $table = 'code_save'; // テーブル名を指定
    protected $fillable = [
        'class_id',
        'save_number',
        'html_code',
        'css_code',
        'js_code',
        'main_save_date',
    ];

    /**
     * リレーション: CodeSave に関連付けられたクラス
     */
    public function classModel()
    {
        return $this->belongsTo(Classes::class, 'class_id'); // クラスとのリレーション
    }
}