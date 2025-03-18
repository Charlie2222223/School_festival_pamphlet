<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classes extends Model
{
    use HasFactory;

    protected $table = 'classes';

    protected $fillable = [
        'class_name',
        'password',
        'shop_name',
        'class_room',
        'comment',
        'image_path',
        'html_code',
        'css_code',
        'js_code',
    ];
}