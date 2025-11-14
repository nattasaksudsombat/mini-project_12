<?php

namespace App\Http\Controllers;

use App\Models\ProductColor;
use Illuminate\Http\Request;

class ProductColorController extends Controller
{
    public function show($id)
    {
        $productColors = ProductColor::where('product_id', $id)->get();
        return view('product_colors.index', compact('productColors'));
    }
}
