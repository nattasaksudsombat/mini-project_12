<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ColorSizeController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\ProductApiController;


// ⭐ API Routes สำหรับ Color Size Management
Route::post('/find-color-size-id', [ColorSizeController::class, 'findColorSizeId'])
    ->name('api.find-color-size-id');

Route::post('/create-color-size-variant', [ColorSizeController::class, 'createColorSizeVariant'])
    ->name('api.create-color-size-variant');

Route::get('/variants/{id}', [ColorSizeController::class, 'getVariant'])
    ->name('api.get-variant');

Route::middleware('api')->group(function () {
    // ✅ ดึงออเดอร์ที่กำลังจับสต็อก
    Route::get('/products/holding-orders', [ProductController::class, 'getHoldingOrders']);
});
Route::get('/products/search', [ProductApiController::class, 'search']);
Route::get('/products/{product}/variants', [ProductApiController::class, 'variants']);
Route::get('/products/{product}/reserved', [ProductApiController::class, 'reserved']);
Route::get('/products/{product}/holds', [ProductApiController::class, 'holds']);
Route::get('/products/{product}/reserved', [ProductApiController::class, 'reserved']); // ?exclude_order_id=ID
Route::get('/products/{product}/holds',    [ProductApiController::class, 'holds']);    // ?exclude_order_id=ID
