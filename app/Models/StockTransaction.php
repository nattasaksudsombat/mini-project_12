<?php
// app/Models/StockTransaction.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTransaction extends Model
{
    protected $table = 'stock_transactions';
    public $timestamps = false;
    
    protected $fillable = [
        'product_color_size_id',
        'order_id',
        'type',
        'quantity',
        'quantity_before',
        'quantity_after',
        'reason',
        'user_id',
        'user_name',
        'reference_number',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    // Relations
    public function variant()
    {
        return $this->belongsTo(ProductColorSize::class, 'product_color_size_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // บันทึก transaction
    public static function record(
        int $variantId,
        string $type,
        int $quantity,
        int $quantityBefore,
        int $quantityAfter,
        string $reason,
        ?int $orderId = null,
        ?string $refNumber = null,
        ?int $userId = null,
        ?string $userName = null
    ) {
        return self::create([
            'product_color_size_id' => $variantId,
            'order_id' => $orderId,
            'type' => $type,
            'quantity' => $quantity,
            'quantity_before' => $quantityBefore,
            'quantity_after' => $quantityAfter,
            'reason' => $reason,
            'user_id' => $userId,
            'user_name' => $userName,
            'reference_number' => $refNumber,
        ]);
    }
}
