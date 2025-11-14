<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'phone', 'email', 'address', 'district', 'province', 'postal_code',
        'payment_method', 'purchase_channel', 'notes'
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
