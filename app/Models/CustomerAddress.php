<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerAddress extends Model
{
    protected $table = 'customer_addresses';

    protected $fillable = [
        'customer_id',
        'name',
        'address',
        'district',
        'province',
        'postal_code',
    ];

    /**
     * ความสัมพันธ์: ที่อยู่แต่ละรายการเป็นของลูกค้าคนหนึ่ง
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }
}
