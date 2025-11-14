<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\OrderItem;
class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'customer_id',
        'status',
        'subtotal',
        'shipping_fee',
        'discount',
        'total_price',
        'payment_status',
        'shipping_address',
        'tracking_number',
        'notes',
        'total_amount'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            $latestOrder = static::latest('id')->first();
            $number = $latestOrder ? ((int) str_replace('ORD', '', $latestOrder->order_number)) + 1 : 1;
            $order->order_number = 'ORD' . str_pad($number, 4, '0', STR_PAD_LEFT);
        });
    }
     public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
