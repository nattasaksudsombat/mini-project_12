<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    protected $table = 'product_options';

    protected $fillable = ['product_id', 'option_name', 'option_value'];
    public $timestamps = false;


    public function products()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public static function usageCount($name)
    {
        return self::where('option_name', $name)->count();
    }
}
