<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductColorSize extends Model
{
    protected $table = 'product_color_size';
    public $timestamps = false;

    protected $fillable = [
        'product_id', 'color_id', 'size_id', 'quantity',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function color()
    {
        return $this->belongsTo(Color::class);
    }

    public function size()
    {
        return $this->belongsTo(Size::class, 'size_id');
    }
}   