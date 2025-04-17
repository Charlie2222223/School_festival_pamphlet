<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * 保存可能なフィールド
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'microsoft_id',
        'password',
        'class_id', // クラスIDを追加
        'last_login_at',
        'is_online',
    ];

    /**
     * 隠すフィールド（JSON出力時）
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id'); // Classes モデルとのリレーション
    }
}
