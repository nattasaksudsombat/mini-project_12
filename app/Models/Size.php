<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProductSize;
use App\Models\ProductColorSize;
class Size extends Model
{
    use HasFactory;

    protected $table = 'sizes';
    public $timestamps = false;
    protected $fillable = ['size_name'];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_sizes');
    }
    // บอกว่า primary key คือ id (ไม่จำเป็นถ้าใช้ id อยู่แล้ว)
    protected $primaryKey = 'id';


  public function productColorSizes()
    {
        // ถ้าใช้ตาราง product_sizes ให้เปลี่ยนเป็น 'product_sizes'
        // ถ้าใช้ตาราง product_color_size ให้เปลี่ยนเป็น 'product_color_size'
        return $this->hasMany(ProductColorSize::class, 'size_id');
    }
    // Map ชื่อ 'name' ให้ไปที่ 'size_name'
    public function getNameAttribute()
    {
        return $this->attributes['size_name'];
    }
}
