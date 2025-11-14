<?php
// app/Models/StockHold.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockHold extends Model
{
    protected $table = 'stock_holds';
    public $timestamps = false; // ใช้ datetime เอง

    protected $fillable = [
        'product_color_size_id', 'order_id', 'quantity', 'status', 'expires_at', 'created_at', 'updated_at'
    ];
}
