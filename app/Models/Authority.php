<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Authority extends Model
{
    protected $fillable = ['authority_name'];

    /**
     * リレーション: Authority に関連付けられたクラス
     */
    public function classes()
    {
        return $this->hasMany(Classes::class, 'authority_id'); // Authority に関連する複数のクラス
    }
}