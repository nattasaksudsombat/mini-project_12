<?php
// app/Models/StockHold.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class StockHold extends Model
{
    protected $table = 'stock_holds';
    public $timestamps = false; // ใช้ datetime เอง

    protected $fillable = [
        'product_color_size_id', 'order_id', 'quantity', 'status', 'expires_at', 'created_at', 'updated_at'
    ];
    protected $casts = [
        'quantity' => 'integer',
        'reserved_at' => 'datetime',
        'released_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the product color size that owns the hold.
     */
    public function productColorSize()
    {
        return $this->belongsTo(ProductColorSize::class, 'product_color_size_id');
    }

    /**
     * Get the order that owns the hold.
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    /**
     * Scope to filter by active holds.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to filter by released holds.
     */
    public function scopeReleased($query)
    {
        return $query->where('status', 'released');
    }

    /**
     * Get status label in Thai.
     */
    public function getStatusLabelAttribute()
    {
        $labels = [
            'active' => 'จองอยู่',
            'released' => 'ปล่อยแล้ว',
            'expired' => 'หมดอายุ',
        ];

        return $labels[$this->status] ?? $this->status;
    }

    /**
     * Get status badge color.
     */
    public function getStatusColorAttribute()
    {
        $colors = [
            'active' => 'warning',
            'released' => 'success',
            'expired' => 'danger',
        ];

        return $colors[$this->status] ?? 'secondary';
    }
}
