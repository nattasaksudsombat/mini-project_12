<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

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
use App\Http\Controllers\StockController;

/**
 * หน้าแรก -> Dashboard
 */
Route::get('/', fn () => redirect('/dashboard'));
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

/**
 * รายรับ/รายจ่าย
 */
Route::resource('incomes', IncomeController::class);
Route::resource('expenses', ExpenseController::class);

/**
 * การตั้งค่า
 */
Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');

/**
 * หมวดหมู่ / สี / ไซส์ / แท็ก / ตัวเลือก / ประเภทสินค้า
 */
Route::resource('categories', CategoryController::class);
Route::resource('colors', ColorController::class);
Route::resource('sizes', SizeController::class);
Route::resource('tags', TagController::class);
Route::resource('options', OptionController::class);
Route::resource('types', TypeProductController::class);

/**
 * สินค้า + รูป + ตัวเลือกเสริม
 */
Route::resource('products', ProductController::class);
Route::post('/products/{product}/toggle', [ProductController::class, 'toggle'])->name('products.toggle');

Route::get('/products/{product}/images/edit', [ProductImageController::class, 'edit'])->name('products.images.edit');
Route::put('/products/{product}/images', [ProductImageController::class, 'update'])->name('products.images.update');
Route::patch('/products/{product}/images/{image}/main', [ProductImageController::class, 'setMain'])->name('products.images.setMain');
Route::delete('/product-images/{image}', [ProductImageController::class, 'destroy'])->name('productImages.destroy');

Route::get('/products/{id}/options', [ProductOptionController::class, 'show'])->name('products.options.show');
Route::get('/products/{id}/colors', [ProductColorController::class, 'show'])->name('products.colors.show');
Route::get('/products/{id}/tags', [ProductTagController::class, 'show'])->name('products.tags.show');

/**
 * จัดการ Color-Size ของสินค้า
 */
Route::get('/product-color-size/create/{product_id}', [ProductColorSizeController::class, 'create'])->name('product.colorSize.create');
Route::post('/product-color-size/store', [ProductColorSizeController::class, 'store'])->name('product.colorSize.store');
Route::get('/product-color-size/{id}/edit', [ProductColorSizeController::class, 'edit'])->name('product.colorSize.edit');
Route::put('/product-color-size/{id}', [ProductColorSizeController::class, 'update'])->name('product.colorSize.update');

/**
 * ค้นหาสินค้า + Live Search + Variants
 */
Route::get('/products/search', [ProductController::class, 'search'])->name('products.search');

Route::get('/products/live-search', function () {
    return view('products.search');
})->name('products.live_search');

Route::get('/products/{product}/variants', [ProductController::class, 'getVariants'])->name('products.variants');

/**
 * บาร์โค้ด
 */
Route::post('/products/print-barcode', [ProductController::class, 'printBarcode'])->name('products.printBarcode');
Route::get('/scan-barcode', [ProductController::class, 'scanBarcode']);

/**
 * คำสั่งซื้อ (ประกาศครั้งเดียวพอ)
 */
Route::resource('orders', OrderController::class);
Route::patch('/orders/{order}/mark-paid', [OrderController::class, 'markPaid'])->name('orders.markPaid');
Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
Route::post('/orders/{id}/ship',   [OrderController::class, 'ship'])->name('orders.ship');
Route::patch('/orders/{id}/tracking', [OrderController::class, 'updateTracking'])->name('orders.updateTracking');
Route::patch('/orders/{id}/pay',      [OrderController::class, 'pay'])->name('orders.pay');
Route::delete('/order-items/{id}', [OrderController::class, 'destroyItem'])->name('order-items.destroy');

/**
 * สต๊อก (Golden Rule)
 * - GET /stock/adjust/{variantId}  => ฟอร์มปรับ
 * - POST /stock/adjust/{variantId} => บันทึก (ใช้ adjustSave)
 * - GET /stock/variant/{id}/history => ไทม์ไลน์ของ variant นั้น
 * - GET /stock/adjust/{id}/history  => ประวัติปรับเข้า/ออก (สั้นๆ 10 รายการ)
 * - รายงานรวม/Export
 */
Route::get('/stock/adjust/{variantId}', [StockController::class, 'adjustForm'])->name('stock.adjust.form');
Route::post('/stock/adjust/{variantId}', [StockController::class, 'adjustSave'])->name('stock.adjust.save');

Route::get('/stock/variant/{variantId}/history', [StockController::class, 'variantHistory'])->name('stock.variant.history');
Route::get('/stock/adjust/{variantId}/history',  [StockController::class, 'adjustHistory'])->name('stock.adjust.history');

Route::get('/stock/report', [StockController::class, 'report'])->name('stock.report');
Route::get('/stock/export', [StockController::class, 'export'])->name('stock.export');
Route::get('/stock/history', [StockController::class, 'history'])->name('stock.history');
Route::get('/stock/{product}', [StockController::class, 'productStock'])->name('stock.product');

/**
 * Debug page: ดู order_items ล่าสุด 10 แถว
 */
Route::get('/debug/order-items', function () {
    $rows = DB::table('order_items')
        ->select('id','quantity','unit_price','total_price')
        ->orderByDesc('id')
        ->limit(10)
        ->get();
    return view('debug.order_items', compact('rows'));
})->name('debug.order_items');

/**
 * Simple APIs
 */
Route::get('/api/colors/{id}', function ($id) {
    try {
        $color = DB::table('colors')->where('id', $id)->first();
        if (!$color) return response()->json(['error' => 'Color not found'], 404);
        return response()->json([
            'id' => $color->id,
            'name' => $color->name,
            'hex_code' => $color->hex_code ?? null
        ]);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Database error'], 500);
    }
});

Route::get('/api/sizes/{id}', function ($id) {
    try {
        $size = DB::table('sizes')->where('id', $id)->first();
        if (!$size) return response()->json(['error' => 'Size not found'], 404);
        return response()->json([
            'id' => $size->id,
            'name' => $size->size_name,
            'size_name' => $size->size_name
        ]);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Database error'], 500);
    }
});

Route::get('/api/variants/{id}', function ($id) {
    try {
        // ใช้ singular table ตามโปรเจกต์: product_color_size
        $variant = DB::table('product_color_size as pcs')
            ->leftJoin('colors as c', 'pcs.color_id', '=', 'c.id')
            ->leftJoin('sizes  as s', 'pcs.size_id', '=', 's.id')
            ->select(
                'pcs.id','pcs.product_id','pcs.color_id','pcs.size_id','pcs.quantity',
                'c.name as color_name',
                's.size_name as size_name'
            )
            ->where('pcs.id', $id)
            ->first();

        if (!$variant) {
            return response()->json(['error' => 'Variant not found'], 404);
        }

        return response()->json([
            'id'         => $variant->id,
            'product_id' => $variant->product_id,
            'color_id'   => $variant->color_id,
            'size_id'    => $variant->size_id,
            'quantity'   => $variant->quantity,
            'color_name' => $variant->color_name,
            'size_name'  => $variant->size_name,
            'stock'      => $variant->quantity,
            'color'      => $variant->color_name ? ['id'=>$variant->color_id,'name'=>$variant->color_name] : null,
            'size'       => $variant->size_name ? ['id'=>$variant->size_id,'name'=>$variant->size_name] : null,
        ]);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Database error'], 500);
    }
});

/**
 * รายงานรวม
 */
Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

/**
 * Import/Export สินค้า
 */
Route::get('/export-products', [ProductExcelController::class, 'export'])->name('export.products');
Route::post('/import-products', [ProductExcelController::class, 'import'])->name('import.products');
Route::post('/products/import', [ProductController::class, 'import'])->name('products.import');
Route::get('/products/export', [ProductController::class, 'export'])->name('export.products');
