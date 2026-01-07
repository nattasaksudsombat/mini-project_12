<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'customers';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'purchase_channel',
        'payment_method',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the orders for the customer.
     */
    public function orders()
    {
        return $this->hasMany(Order::class, 'customer_id');
    }

    /**
     * Get the addresses for the customer.
     */
    public function addresses()
    {
        return $this->hasMany(CustomerAddress::class, 'customer_id');
    }

    /**
     * Get the primary address for the customer.
     */
    public function primaryAddress()
    {
        return $this->hasOne(CustomerAddress::class, 'customer_id')
            ->where('is_default', true);
    }

    /**
     * Get purchase channel label in Thai.
     */
    public function getPurchaseChannelLabelAttribute()
    {
        $labels = [
            'facebook' => 'Facebook',
            'line' => 'Line',
            'website' => 'เว็บไซต์',
            'shopee' => 'Shopee',
            'lazada' => 'Lazada',
            'offline' => 'ร้านค้า',
        ];

        return $labels[$this->purchase_channel] ?? $this->purchase_channel;
    }

    /**
     * Get payment method label in Thai.
     */
    public function getPaymentMethodLabelAttribute()
    {
        $labels = [
            'bank_transfer' => 'โอนเงิน',
            'cash_on_delivery' => 'เก็บเงินปลายทาง',
            'credit_card' => 'บัตรเครดิต',
            'e_wallet' => 'กระเป๋าเงินอิเล็กทรอนิกส์',
        ];

        return $labels[$this->payment_method] ?? $this->payment_method;
    }

    /**
     * Get the total orders count.
     */
    public function getTotalOrdersAttribute()
    {
        return $this->orders()->count();
    }

    /**
     * Get the total amount spent.
     */
    public function getTotalSpentAttribute()
    {
        return $this->orders()
            ->where('payment_status', 'paid')
            ->sum('total_price');
    }

    /**
     * Scope a query to search customers.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%");
        });
    }
}