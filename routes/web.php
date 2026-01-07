<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    DashboardController,
    OrderController,
    OrderItemController,
    ProductController,
    ProductColorSizeController,
    CustomerController,
    StockController,
    ReportController,
    IncomeController,
    ExpenseController,
    SettingsController,
    CategoryController,
    ColorController,
    SizeController,
    TagController,
    OptionController,
    TypeProductController,
    ProductImageController,
    ProductExcelController
};

Route::get('/', fn () => redirect('/dashboard'));
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// --- Orders ---
Route::prefix('orders')->name('orders.')->group(function () {
    Route::get('customers/search', [OrderController::class, 'searchCustomers'])->name('customers.search');
    Route::get('customers/{customer}/addresses', [OrderController::class, 'getCustomerAddresses'])->name('customers.addresses');
    Route::post('{order}/cancel', [OrderController::class, 'cancel'])->name('cancel');
    Route::post('{order}/ship',   [OrderController::class, 'ship'])->name('ship');
    Route::patch('{order}/pay',   [OrderController::class, 'pay'])->name('pay');
    Route::patch('{order}/tracking', [OrderController::class, 'updateTracking'])->name('tracking');
});
Route::resource('orders', OrderController::class);
Route::resource('order-items', OrderItemController::class)->only(['destroy']);

// --- Customers ---
Route::resource('customers', CustomerController::class)->except('show');

// --- Products & Images ---
Route::controller(ProductController::class)->group(function () {
    // Search & Export/Import
    Route::get('/products/search', 'search')->name('products.search');
    Route::get('/export-products', 'export')->name('export.products');
    Route::post('/import-products', 'import')->name('products.import');
    
    // Toggle Status
    Route::post('/products/{product}/toggle', 'toggleStatus')->name('products.toggle');

    // ✅ Image Management Routes (แก้ไขให้สมบูรณ์)
    Route::get('/products/{product}/images', 'editImages')->name('products.images.edit'); // หน้าแก้ไข
    Route::post('/products/{product}/images', 'addImage')->name('products.images.store'); // เพิ่มรูป (ใช้ POST)
    Route::delete('/products/images/{image}', 'deleteImage')->name('products.images.destroy'); // ลบรูป
    Route::post('/products/{product}/images/{image}/main', 'setMainImage')->name('products.images.setMain'); // ตั้งรูปหลัก (แก้ชื่อให้สอดคล้อง)
    Route::post('/products/{product}/images/{image}/main', 'setMainImage')->name('products.setMain');
});

Route::resource('products', ProductController::class);
// Route::resource('product-images', ProductImageController::class); // ❌ ไม่จำเป็นต้องใช้แล้ว เพราะรวม logic ไว้ใน ProductController แล้ว

// --- Product Color Size ---
Route::prefix('products/{product}/color-size')->name('product.colorSize.')->group(function () {
    Route::get('/create', [ProductColorSizeController::class, 'create'])->name('create');
    Route::post('/', [ProductColorSizeController::class, 'store'])->name('store');
});

// --- Stock ---
Route::get('/stock/adjust/{variant}', [ProductColorSizeController::class, 'adjustForm'])->name('stock.adjust.form');
Route::post('/stock/adjust/{variant}', [ProductColorSizeController::class, 'saveAdjustment'])->name('stock.adjust.save');
Route::get('/stock/history/{variant}', [ProductColorSizeController::class, 'history'])->name('stock.variant.history');
Route::post('/products/print-barcode', [ProductController::class, 'printBarcode'])->name('products.printBarcode');

// --- Others ---
Route::resource('incomes', IncomeController::class);
Route::resource('expenses', ExpenseController::class);
Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
Route::resource('categories', CategoryController::class);
Route::resource('colors', ColorController::class);
Route::resource('sizes', SizeController::class);
Route::resource('tags', TagController::class);
Route::resource('options', OptionController::class);
Route::resource('types', TypeProductController::class);
Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
Route::post('products/import', [ProductExcelController::class, 'import'])->name('products.import');
