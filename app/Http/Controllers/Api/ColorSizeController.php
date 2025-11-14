<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ColorSizeController extends Controller
{
    /**
     * ค้นหา color_size_id จากชื่อสีและไซส์
     */
    public function findColorSizeId(Request $request)
    {
        try {
            $productId = $request->input('product_id');
            $colorName = $request->input('color_name');
            $sizeName = $request->input('size_name');
            
            Log::info('Finding color_size_id', [
                'product_id' => $productId,
                'color_name' => $colorName,
                'size_name' => $sizeName
            ]);
            
            if (!$productId || !$colorName || !$sizeName) {
                return response()->json(['error' => 'Missing required parameters'], 400);
            }
            
            // ค้นหาจากตาราง product_color_sizes โดยใช้ JOIN
            $colorSize = DB::table('product_color_sizes as pcs')
                ->join('colors as c', 'pcs.color_id', '=', 'c.id')
                ->join('sizes as s', 'pcs.size_id', '=', 's.id')
                ->where('pcs.product_id', $productId)
                ->where('c.name', $colorName)
                ->where('s.name', $sizeName)
                ->select(
                    'pcs.id as color_size_id',
                    'pcs.color_id',
                    'pcs.size_id',
                    'c.name as color_name',
                    's.name as size_name',
                    'pcs.quantity'
                )
                ->first();
            
            if (!$colorSize) {
                // ลองค้นหาด้วย size_name field ถ้าไม่พบ
                $colorSize = DB::table('product_color_sizes as pcs')
                    ->join('colors as c', 'pcs.color_id', '=', 'c.id')
                    ->join('sizes as s', 'pcs.size_id', '=', 's.id')
                    ->where('pcs.product_id', $productId)
                    ->where('c.name', $colorName)
                    ->where('s.size_name', $sizeName)
                    ->select(
                        'pcs.id as color_size_id',
                        'pcs.color_id',
                        'pcs.size_id',
                        'c.name as color_name',
                        's.size_name as size_name',
                        'pcs.quantity'
                    )
                    ->first();
            }
            
            if ($colorSize) {
                Log::info('Found color_size_id', ['result' => $colorSize]);
                
                return response()->json([
                    'id' => $colorSize->color_size_id,
                    'color_size_id' => $colorSize->color_size_id,
                    'color_id' => $colorSize->color_id,
                    'size_id' => $colorSize->size_id,
                    'color_name' => $colorSize->color_name,
                    'size_name' => $colorSize->size_name,
                    'quantity' => $colorSize->quantity
                ]);
            }
            
            Log::warning('Color size combination not found', [
                'product_id' => $productId,
                'color_name' => $colorName,
                'size_name' => $sizeName
            ]);
            
            return response()->json(['error' => 'Color size combination not found'], 404);
            
        } catch (\Exception $e) {
            Log::error('Error finding color_size_id', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json(['error' => 'Server error'], 500);
        }
    }
    
    /**
     * สร้าง color_size_id ใหม่
     */
    public function createColorSizeVariant(Request $request)
    {
        try {
            $productId = $request->input('product_id');
            $colorId = $request->input('color_id');
            $sizeId = $request->input('size_id');
            $quantity = $request->input('quantity', 999);
            
            Log::info('Creating color_size_variant', [
                'product_id' => $productId,
                'color_id' => $colorId,
                'size_id' => $sizeId,
                'quantity' => $quantity
            ]);
            
            if (!$productId || !$colorId || !$sizeId) {
                return response()->json(['error' => 'Missing required parameters'], 400);
            }
            
            // ตรวจสอบว่ามีอยู่แล้วหรือไม่
            $existing = DB::table('product_color_sizes')
                ->where('product_id', $productId)
                ->where('color_id', $colorId)
                ->where('size_id', $sizeId)
                ->first();
            
            if ($existing) {
                Log::info('Color size variant already exists', ['id' => $existing->id]);
                
                return response()->json([
                    'id' => $existing->id,
                    'color_size_id' => $existing->id,
                    'color_id' => $existing->color_id,
                    'size_id' => $existing->size_id,
                    'quantity' => $existing->quantity,
                    'message' => 'Variant already exists'
                ]);
            }
            
            // สร้างใหม่
            $id = DB::table('product_color_sizes')->insertGetId([
                'product_id' => $productId,
                'color_id' => $colorId,
                'size_id' => $sizeId,
                'quantity' => $quantity,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            Log::info('Created new color_size_variant', ['id' => $id]);
            
            return response()->json([
                'id' => $id,
                'color_size_id' => $id,
                'color_id' => $colorId,
                'size_id' => $sizeId,
                'quantity' => $quantity,
                'message' => 'Variant created successfully'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error creating color_size_variant', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json(['error' => 'Server error'], 500);
        }
    }
    
    /**
     * ดึงข้อมูล variant by ID
     */
    public function getVariant($id)
    {
        try {
            $variant = DB::table('product_color_sizes as pcs')
                ->join('colors as c', 'pcs.color_id', '=', 'c.id')
                ->join('sizes as s', 'pcs.size_id', '=', 's.id')
                ->where('pcs.id', $id)
                ->select(
                    'pcs.id',
                    'pcs.product_id',
                    'pcs.color_id',
                    'pcs.size_id',
                    'pcs.quantity',
                    'c.name as color_name',
                    's.name as size_name',
                    's.size_name'
                )
                ->first();
            
            if (!$variant) {
                return response()->json(['error' => 'Variant not found'], 404);
            }
            
            return response()->json([
                'id' => $variant->id,
                'product_id' => $variant->product_id,
                'color_id' => $variant->color_id,
                'size_id' => $variant->size_id,
                'color_name' => $variant->color_name,
                'size_name' => $variant->size_name ?: $variant->size_name,
                'quantity' => $variant->quantity
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting variant', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json(['error' => 'Server error'], 500);
        }
    }
}