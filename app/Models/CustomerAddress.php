<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerAddress extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'customer_addresses';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'customer_id',
        'name',
        'address',
        'subdistrict',
        'district',
        'province',
        'postal_code',
        'is_default',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_default' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the customer that owns the address.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * Get the orders that use this address.
     */
    public function orders()
    {
        return $this->hasMany(Order::class, 'customer_address_id');
    }

    /**
     * Get the full address string.
     */
    public function getFullAddressAttribute()
    {
        return trim(
            $this->address . ' ' . 
            $this->subdistrict . ' ' . 
            $this->district . ' ' . 
            $this->province . ' ' . 
            $this->postal_code
        );
    }

    /**
     * Get the address label (name or default label).
     */
    public function getLabelAttribute()
    {
        return $this->name ?: 'ที่อยู่ #' . $this->id;
    }

    /**
     * Boot method to handle default address logic.
     */
    protected static function boot()
    {
        parent::boot();

        // เมื่อสร้างที่อยู่ใหม่และเป็น default
        static::creating(function ($address) {
            if ($address->is_default) {
                // ยกเลิก default ของที่อยู่อื่นๆ ของลูกค้านี้
                static::where('customer_id', $address->customer_id)
                    ->update(['is_default' => false]);
            }
        });

        // เมื่ออัปเดตที่อยู่และเปลี่ยนเป็น default
        static::updating(function ($address) {
            if ($address->is_default && $address->isDirty('is_default')) {
                // ยกเลิก default ของที่อยู่อื่นๆ ของลูกค้านี้
                static::where('customer_id', $address->customer_id)
                    ->where('id', '!=', $address->id)
                    ->update(['is_default' => false]);
            }
        });
    }
}