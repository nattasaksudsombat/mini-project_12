<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $fillable = ['category_id', 'id_stock', 'name', 'description', 'price', 'cost', 'is_active'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function productImages()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function productOptions()
    {
        return $this->hasMany(ProductOption::class);
    }

    public function productColors()
    {
        return $this->hasMany(ProductColor::class);
    }

    public function productTags()
    {
        return $this->hasMany(ProductTag::class);
    }
    public function sizes()
    {
        return $this->belongsToMany(Size::class, 'product_sizes');
    }
    public function colorSizes()
    {
        return $this->hasMany(ProductColorSize::class);
    }
    public function colors()
    {
        return $this->belongsToMany(Color::class, 'product_colors');
    }
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'product_tags');
    }
    public function mainImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_main', 1);
    }
    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }
}
