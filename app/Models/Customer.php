<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    // ถ้าโปรเจกต์คุณไม่ได้ใช้ HasFactory อยู่แล้ว ก็ไม่ต้องใส่
    // use HasFactory;

    protected $table = 'customers';

    // เปิดให้กรอกตามโครงสร้างจริงในฐานข้อมูลของคุณ (คงของเดิมไว้)
    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',       // ยังเก็บไว้เพื่อ backward-compat
        'district',
        'province',
        'postal_code',
        'payment_method',
        'purchase_channel',
        'notes',
    ];

    /**
     * ความสัมพันธ์: ลูกค้า 1 คน มีได้หลายที่อยู่จัดส่ง
     */
    public function addresses(): HasMany
    {
        return $this->hasMany(CustomerAddress::class, 'customer_id', 'id');
    }
    // App\Models\Customer
    public function orders()
    {
        return $this->hasMany(\App\Models\Order::class, 'customer_id');
    }
}
