<?php

namespace App\Http\Controllers;

use App\Models\ProductTag;
use Illuminate\Http\Request;

class ProductTagController extends Controller
{
    public function show($id)
    {
        $productTags = ProductTag::where('product_id', $id)->get();
        return view('product_tags.index', compact('productTags'));
    }
}
