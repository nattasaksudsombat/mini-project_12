<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductTag extends Model
{
    protected $table = 'product_tags';
    protected $fillable = ['product_id', 'tag_id'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function tag()
    {
        return $this->belongsTo(related: Tag::class);
    }
    public function tags()
{
    return $this->belongsToMany(Tag::class, 'product_tags');
}

}
