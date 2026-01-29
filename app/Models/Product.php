<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'products';

    /**
     * The attributes that are mass assignable.
     * เพิ่ม id_stock และ description เพื่อให้ Controller บันทึกข้อมูลได้
     */
    protected $fillable = [
        'id_stock',      // จำเป็นสำหรับ ProductController
        'name',
        'description',   // จำเป็นสำหรับ ProductController
        'price',
        'cost',
        'category_id',
        'is_active',
        // คอลัมน์อื่นๆ เผื่อไว้
        'sku',
        'barcode',
        'type_id',
        'image',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'price' => 'decimal:2',
        'cost' => 'decimal:2',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /* ====================================================
       RELATIONSHIPS (ความสัมพันธ์หลัก)
    ==================================================== */

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * ความสัมพันธ์กับรูปภาพสินค้า
     * Controller ใช้: $product->productImages
     */
    public function productImages()
    {
        return $this->hasMany(ProductImage::class, 'product_id');
    }

    /**
     * ความสัมพันธ์กับตัวเลือกสินค้า (Options)
     * Controller ใช้: $product->productOptions
     */
    public function productOptions()
    {
        return $this->hasMany(ProductOption::class, 'product_id');
    }

    /**
     * ความสัมพันธ์กับสต๊อกแบบ สี-ไซส์ (Variant)
     * Controller ใช้: $product->colorSizes
     */
    public function colorSizes()
    {
        return $this->hasMany(ProductColorSize::class, 'product_id');
    }

    /**
     * ความสัมพันธ์กับ Tags
     * Controller ใช้: $product->tags()->sync(...)
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'product_tags', 'product_id', 'tag_id');
    }

    /**
     * ความสัมพันธ์กับ Colors (ผ่านตาราง pivot product_color_sizes)
     */
    public function colors()
    {
        // แก้จาก 'product_color_sizes' เป็น 'product_color_size' (ตัด s ออก)
        return $this->belongsToMany(Color::class, 'product_color_size', 'product_id', 'color_id')
            ->distinct();
    }

    /**
     * ความสัมพันธ์กับ Sizes (ผ่านตาราง pivot product_color_sizes)
     */
    public function sizes()
    {
        // แก้จาก 'product_color_sizes' เป็น 'product_color_size' (ตัด s ออก)
        return $this->belongsToMany(Size::class, 'product_color_size', 'product_id', 'size_id')
            ->distinct();
    }

    /* ====================================================
       ALIASES FOR CONTROLLER COMPATIBILITY
       (ชื่อเล่นเพื่อให้ตรงกับที่ ProductController เรียกใช้)
    ==================================================== */

    /**
     * Controller เรียกใช้ 'options' ในฟังก์ชัน show()
     * $product->load([... 'options' ...])
     */
    public function options()
    {
        return $this->productOptions();
    }

    /**
     * Controller เรียกใช้ 'productColors' ในฟังก์ชัน index()
     * Product::with([... 'productColors' ...])
     */
    public function productColors()
    {
        return $this->colors();
    }

    /**
     * Controller เรียกใช้ 'productTags' ในฟังก์ชัน index()
     * Product::with([... 'productTags' ...])
     */
    public function productTags()
    {
        return $this->tags();
    }

    /* ====================================================
       ADDITIONAL HELPERS
    ==================================================== */

    public function type()
    {
        return $this->belongsTo(TypeProduct::class, 'type_id');
    }

    public function mainImage()
    {
        return $this->hasOne(ProductImage::class, 'product_id')
            ->where('is_main', true);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'product_id');
    }

    // Helper: นับจำนวนสต๊อกรวม
    public function getTotalStockAttribute()
    {
        return $this->colorSizes()->sum('quantity');
    }

    // Helper: เช็คสถานะ
    public function getStatusLabelAttribute()
    {
        return $this->is_active ? 'ใช้งาน' : 'ปิดการใช้งาน';
    }
    public function productColorSizes()
    {
        return $this->colorSizes();
    }
    /**
     * ✅ Controller เรียกใช้ 'productSizes'
     */
    public function productSizes()
    {
        return $this->sizes();
    }
}
