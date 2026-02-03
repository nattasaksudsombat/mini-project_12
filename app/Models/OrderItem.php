<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'order_items';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'color_id',
        'size_id',
        'variant_name',
        'quantity',
        'unit_price',
        'total_price',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = true;
public function productColorSize()
    {
        // เชื่อมโยงกับตาราง product_color_sizes ผ่านคอลัมน์ product_color_size_id
        return $this->belongsTo(ProductColorSize::class, 'product_color_size_id');
    }
    /**
     * Get the order that owns the order item.
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    /**
     * Get the product that owns the order item.
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Get the color for the order item.
     */
    public function color()
    {
        return $this->belongsTo(Color::class, 'color_id');
    }

    /**
     * Get the size for the order item.
     */
    public function size()
    {
        return $this->belongsTo(Size::class, 'size_id');
    }

    /**
     * Get the variant (color + size) for the order item.
     */
    public function variant()
    {
        return $this->belongsTo(ProductColorSize::class, 'product_id', 'product_id')
            ->where('color_id', $this->color_id)
            ->where('size_id', $this->size_id);
    }

    /**
     * Get the full variant name with color and size.
     */
    public function getFullVariantNameAttribute()
    {
        if ($this->color && $this->size) {
            return $this->color->name . ' - ' . $this->size->name;
        }
        return $this->variant_name;
    }

    /**
     * Calculate subtotal (alias for total_price).
     */
    public function getSubtotalAttribute()
    {
        return $this->total_price;
    }
}