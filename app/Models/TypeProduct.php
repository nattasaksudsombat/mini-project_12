<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeProduct extends Model
{
    use HasFactory;

    protected $table = 'type_products';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = ['name', 'weight', 'file'];
}

