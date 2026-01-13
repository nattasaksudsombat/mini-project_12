<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;
public $timestamps = false;
    protected $fillable = [
        'username',
        'email',
        'password',
        'role', // ✅ เพิ่มบรรทัดนี้
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // ✅ ฟังก์ชันเช็คว่าเป็น Admin หรือไม่ (เอาไว้ใช้ใน Blade หรือ Controller)
    public function isAdmin()
    {
        return $this->role === 'admin';
    }
}