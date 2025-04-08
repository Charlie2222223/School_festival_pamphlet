<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classes extends Model
{
    protected $fillable = [
        'class_name',
        'password',
        'mail', // メールアドレス
        'is_first_login', // 初回ログイン判定
    ];

    public function uploadedImages()
    {
        return $this->hasMany(UploadedImage::class, 'class_id'); // 1つのクラスに複数の画像
    }

    public function authority()
    {
        return $this->belongsTo(Authority::class, 'authority_id'); // Authority とのリレーション
    }

    public function codeSaves()
    {
        return $this->hasMany(CodeSave::class, 'class_id'); // 1つのクラスに複数のコード保存
    }
}