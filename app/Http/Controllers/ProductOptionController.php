<?php

namespace App\Http\Controllers;

use App\Models\ProductOption;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class ProductOptionController extends Controller
{
    public function show($id)
    {
        $productOptions = DB::table('product_options')
        ->select('option_name', DB::raw('COUNT(DISTINCT product_id) as usage_count'))
        ->groupBy('option_name')
        ->get();
            return view('product_options.index', compact('productOptions'));
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'option_name' => 'required|string|max:255',
            'option_value' => 'required|string|max:255',
        ]);
    
        $option = ProductOption::findOrFail($id);
        $option->update([
            'option_name' => $request->option_name,
            'option_value' => $request->option_value,
        ]);
    
        return back()->with('success', 'อัปเดต Option สำเร็จ');
    }
    


}
