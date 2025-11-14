<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ProductColorSize;

class OrderItem extends Model
{
       protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'variant_name',
        'quantity',
        'unit_price',
        'total_price',
        'color_id',
        'size_id',
    ];
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    public function color()
    {
        return $this->belongsTo(Color::class, 'color_id');
    }

    public function size()
    {
        return $this->belongsTo(Size::class, 'size_id');
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function productColorSize()
    {
        return $this->belongsTo(ProductColorSize::class, 'color_size_id');
    }

    // สำหรับแสดงข้อมูลสี-ไซส์
    public function getVariantDisplayAttribute()
    {
        if ($this->productColorSize) {
            return $this->productColorSize->color->name . ' - ' . $this->productColorSize->size->name;
        }
        return 'ไม่ระบุ';
    }


    // ความสัมพันธ์กับ OrderItem
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
