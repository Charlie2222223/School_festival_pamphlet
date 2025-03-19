<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UploadedImage extends Model
{
    protected $fillable = ['class_id', 'filename', 'path'];

    public function classModel()
    {
        return $this->belongsTo(Classes::class, 'class_id'); // ClassModel が classes テーブル用モデル
    }
}
