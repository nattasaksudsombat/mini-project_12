<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\SizeController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\OptionController;
use App\Http\Controllers\TypeProductController;
use App\Http\Controllers\ProductImageController;
use App\Http\Controllers\ProductOptionController;
use App\Http\Controllers\ProductColorController;
use App\Http\Controllers\ProductTagController;
use App\Http\Controllers\ProductColorSizeController;
use App\Http\Controllers\ProductExcelController;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\StockController;
use App\Http\Controllers\ColorSizeController;

Route::prefix('products')->group(function () {
    Route::get('/search', [OrderController::class, 'searchProducts'])->name('products.search');
    Route::get('/{product}/variants', [OrderController::class, 'getProductVariants'])->name('products.variants');
});


Route::get('/stock/adjust/{variantId}', [StockController::class, 'adjustForm'])->name('stock.adjust.form');
Route::post('/stock/adjust/{variantId}', [StockController::class, 'adjustSave'])->name('stock.adjust.save');

Route::get('/stock/variant/{variantId}/history', [StockController::class, 'variantHistory'])->name('stock.variant.history');

Route::get('/stock/variant/{variantId}/history', [StockController::class, 'variantHistory'])
    ->name('stock.variant.history');

Route::get('/stock/adjust/{variantId}', [StockController::class, 'adjustForm'])
    ->name('stock.adjust.form');

Route::post('/stock/adjust/{variantId}', [StockController::class, 'adjustSave'])
    ->name('stock.adjust.save');


Route::get('/debug/order-items', function () {
    // ดึง 10 แถวล่าสุด (id มากไปน้อย)
    $rows = DB::table('order_items')
        ->select('id','quantity','unit_price','total_price')
        ->orderByDesc('id')
        ->limit(10)
        ->get();

    return view('debug.order_items', compact('rows'));
})->name('debug.order_items');
   Route::get('/stock/variant/{variantId}/history', [StockController::class, 'variantHistory'])
       ->name('stock.variant.history');
// รายงานสต็อก
Route::get('/stock/report', [StockController::class, 'report'])->name('stock.report');
Route::get('/stock/export', [StockController::class, 'export'])->name('stock.export');

// ประวัติการเปลี่ยนแปลงสต็อก
Route::get('/stock/history', [StockController::class, 'history'])->name('stock.history');
Route::get('/stock/{product}', [StockController::class, 'productStock'])->name('stock.product');
Route::get('/stock/adjust/{variantId}', [StockController::class, 'adjustHistory'])->name('stock.adjust.history');

// เสริม: ยกเลิก/จัดส่ง
Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
Route::post('/orders/{id}/ship',   [OrderController::class, 'ship'])->name('orders.ship');

// ปรับสต็อก
Route::get('/stock/adjust/{variantId}', [StockController::class, 'adjustForm'])->name('stock.adjust.form');
Route::post('/stock/adjust/{variantId}', [StockController::class, 'adjust'])->name('stock.adjust.save');

// ประวัติของ variant นั้นๆ
Route::get('/stock/variant/{variantId}/history', [StockController::class, 'variantHistory'])->name('stock.variant.history');

Route::view('/orders/create', 'orders.create')->name('orders.create');
Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');

// ส่งของ
Route::post('/orders/{order}/ship', [OrderController::class, 'ship'])->name('orders.ship');

Route::get('/products/live-search', function () {
    return view('products.search');
})->name('products.live_search');
// ✅ Route สำหรับอัปเดตสต็อก variant
Route::patch('/products/{product}/variants/{variant}/update-stock', [ProductController::class, 'updateVariantStock'])
    ->name('products.variants.update-stock');

// ✅ Route สำหรับดึง variants ผ่าน AJAX
Route::get('/products/{id}/variants', [ProductController::class, 'getVariants'])
    ->name('products.variants');

// ✅ Route สำหรับค้นหาสินค้า
Route::get('/products/search', [ProductController::class, 'search'])
    ->name('products.search');
// Route AJAX สำหรับการค้นหา
Route::get('/products/search', function (Request $request) {
    Log::info('ค้นหาคำว่า: ' . $request->q);

    $query = $request->q;

    $products = Product::with('mainImage')
        ->where('id_stock', 'LIKE', "%{$query}%")
        ->limit(10)
        ->get()
        ->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'id_stock' => $product->id_stock,
                'image_url' => $product->mainImage && $product->mainImage->image_url
                    ? asset('storage/' . $product->mainImage->image_url)
                    : asset('images/no-image.png'),
            ];
        });

    return response()->json($products);
});

// API สำหรับดึงข้อมูลสี
Route::get('/api/colors/{id}', function ($id) {
    try {
        $color = DB::table('colors')->where('id', $id)->first();
        
        if (!$color) {
            return response()->json(['error' => 'Color not found'], 404);
        }
        
        return response()->json([
            'id' => $color->id,
            'name' => $color->name,
            'hex_code' => $color->hex_code ?? null
        ]);
        
    } catch (Exception $e) {
        return response()->json(['error' => 'Database error'], 500);
    }
});

// API สำหรับดึงข้อมูลไซส์
Route::get('/api/sizes/{id}', function ($id) {
    try {
        $size = DB::table('sizes')->where('id', $id)->first();
        
        if (!$size) {
            return response()->json(['error' => 'Size not found'], 404);
        }
        
        return response()->json([
            'id' => $size->id,
            'name' => $size->size_name,
            'size_name' => $size->size_name
        ]);
        
    } catch (Exception $e) {
        return response()->json(['error' => 'Database error'], 500);
    }
});

// API สำหรับดึงข้อมูล variant (product_color_size)
Route::get('/api/variants/{id}', function ($id) {
    try {
        $variant = DB::table('product_color_sizes as pcs')
            ->leftJoin('colors as c', 'pcs.color_id', '=', 'c.id')
            ->leftJoin('sizes as s', 'pcs.size_id', '=', 's.id')
            ->select(
                'pcs.id',
                'pcs.product_id',
                'pcs.color_id',
                'pcs.size_id',
                'pcs.quantity',
                'c.name as color_name',
                's.size_name as size_name'
            )
            ->where('pcs.id', $id)
            ->first();
        
        if (!$variant) {
            return response()->json(['error' => 'Variant not found'], 404);
        }
        
        return response()->json([
            'id' => $variant->id,
            'product_id' => $variant->product_id,
            'color_id' => $variant->color_id,
            'size_id' => $variant->size_id,
            'quantity' => $variant->quantity,
            'color_name' => $variant->color_name,
            'size_name' => $variant->size_name,
            'stock' => $variant->quantity,
            'color' => $variant->color_name ? ['id' => $variant->color_id, 'name' => $variant->color_name] : null,
            'size' => $variant->size_name ? ['id' => $variant->size_id, 'name' => $variant->size_name] : null
        ]);
        
    } catch (Exception $e) {
        return response()->json(['error' => 'Database error'], 500);
    }
});
Route::patch('/orders/{id}/tracking', [OrderController::class, 'updateTracking'])->name('orders.updateTracking');
Route::patch('/orders/{id}/pay', [OrderController::class, 'pay'])->name('orders.pay');

// Route สำหรับดึงข้อมูลสี-ไซส์ของสินค้า
Route::get('/products/{id}/variants', [OrderController::class, 'getProductVariants'])->name('products.variants');
Route::post('/products/print-barcode', [ProductController::class, 'printBarcode'])->name('products.printBarcode');
Route::get('/scan-barcode', [ProductController::class, 'scanBarcode']);
// -------------------------
// หน้าแรก -> Redirect ไป Dashboard
// -------------------------
Route::get('/', fn () => redirect('/dashboard'));

// -------------------------
// Dashboard
// -------------------------
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// -------------------------
// รายรับ
// -------------------------
Route::resource('incomes', IncomeController::class);

// -------------------------
// รายจ่าย
// -------------------------
Route::resource('expenses', ExpenseController::class);

// -------------------------
// การตั้งค่าและผู้ใช้
// -------------------------
Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');

// -------------------------
// หมวดหมู่ / สี / ขนาด / แท็ก / ตัวเลือก / ประเภทสินค้า
// -------------------------
Route::resource('categories', CategoryController::class);
Route::resource('colors', ColorController::class);
Route::resource('sizes', SizeController::class);
Route::resource('tags', TagController::class);
Route::resource('options', OptionController::class);
Route::resource('types', TypeProductController::class);
// -------------------------
// ค้นหาสินค้า (search)
// -------------------------

// หน้าแสดง view ค้นหา

 
// จัดการสินค้า (CRUD)
// -------------------------
Route::resource('products', ProductController::class);

// เปิด/ปิดสถานะการแสดงผลสินค้า
Route::post('/products/{product}/toggle', [ProductController::class, 'toggle'])->name('products.toggle');

// -------------------------
// รูปภาพสินค้า
// -------------------------

// หน้าแก้ไขรูปภาพ
Route::get('/products/{product}/images/edit', [ProductImageController::class, 'edit'])->name('products.images.edit');

// อัปโหลด/อัปเดตรูปภาพ
Route::put('/products/{product}/images', [ProductImageController::class, 'update'])->name('products.images.update');

// ตั้งค่ารูปหลัก
Route::patch('/products/{product}/images/{image}/main', [ProductImageController::class, 'setMain'])->name('products.images.setMain');

// ลบรูปภาพ
Route::delete('/product-images/{image}', [ProductImageController::class, 'destroy'])->name('productImages.destroy');

// -------------------------
// ตัวเลือกสินค้าเพิ่มเติม
// -------------------------

// แสดง Options / Colors / Tags ของสินค้า
Route::get('/products/{id}/options', [ProductOptionController::class, 'show'])->name('products.options.show');
Route::get('/products/{id}/colors', [ProductColorController::class, 'show'])->name('products.colors.show');
Route::get('/products/{id}/tags', [ProductTagController::class, 'show'])->name('products.tags.show');

// จัดการ Color-Size ของสินค้า
Route::get('/product-color-size/create/{product_id}', [ProductColorSizeController::class, 'create'])->name('product.colorSize.create');
Route::post('/product-color-size/store', [ProductColorSizeController::class, 'store'])->name('product.colorSize.store');
Route::get('/product-color-size/{id}/edit', [ProductColorSizeController::class, 'edit'])->name('product.colorSize.edit');
Route::put('/product-color-size/{id}', [ProductColorSizeController::class, 'update'])->name('product.colorSize.update');

// -------------------------
// คำสั่งซื้อ
// -------------------------
Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
Route::patch('/orders/{order}/mark-paid', [OrderController::class, 'markPaid'])->name('orders.markPaid');
Route::resource('orders', OrderController::class);
Route::delete('/order-items/{id}', [OrderController::class, 'destroyItem'])->name('order-items.destroy');
Route::delete('/orders/{id}', [OrderController::class, 'destroy'])->name('orders.destroy');
Route::get('/api/products/{product}/variants', [ProductController::class, 'getVariants']);


// -------------------------
// รายงาน
// -------------------------
Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

Route::get('/export-products', [ProductExcelController::class, 'export'])->name('export.products');
Route::post('/import-products', [ProductExcelController::class, 'import'])->name('import.products');


Route::post('/products/import', [ProductController::class, 'import'])->name('products.import');
Route::get('/products/export', [ProductController::class, 'export'])->name('export.products');
Route::resource('orders', OrderController::class);
Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');