<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockAdjustment extends Model
{
    protected $table = 'stock_adjustments';
    public $timestamps = false;
    
    protected $fillable = [
        'product_color_size_id',
        'adjustment_type',
        'quantity',
        'quantity_before',
        'quantity_after',
        'reason',
        'note',
        'user_id',
        'user_name',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function variant()
    {
        return $this->belongsTo(ProductColorSize::class, 'product_color_size_id');
    }
}