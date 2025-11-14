<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Product;


class ProductOption extends Model
{
    protected $table = 'product_options';
    protected $fillable = ['product_id', 'option_name', 'option_value'];

    public $timestamps = false;

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
