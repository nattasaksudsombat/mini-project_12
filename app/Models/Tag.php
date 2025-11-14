<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = ['tag_name'];
    public $timestamps = false;

    public function productTags()
    {
        return $this->hasMany(ProductTag::class);
    }

    // ถ้ามี model Product ด้วย
    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_tags');
    }
}
