<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    DashboardController,
    OrderController,
    OrderItemController,
    ProductController,
    ProductColorSizeController,
    ProductColorController,
    ProductTagController,
    ProductOptionController,
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

/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
*/
Route::get('/', fn () => redirect('/dashboard'));
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| Orders
|--------------------------------------------------------------------------
*/
Route::prefix('orders')->name('orders.')->group(function () {

    // 🔍 ค้นหาลูกค้า
    Route::get('customers/search', [OrderController::class, 'searchCustomers'])
        ->name('customers.search');

    // 🏠 ที่อยู่ลูกค้า
    Route::get('customers/{customer}/addresses', [OrderController::class, 'getCustomerAddresses'])
        ->name('customers.addresses');

    Route::post('{order}/cancel', [OrderController::class, 'cancel'])->name('cancel');
    Route::post('{order}/ship',   [OrderController::class, 'ship'])->name('ship');
    Route::patch('{order}/pay',   [OrderController::class, 'pay'])->name('pay');
    Route::patch('{order}/tracking', [OrderController::class, 'updateTracking'])->name('tracking');
});

Route::resource('orders', OrderController::class);
Route::resource('order-items', OrderItemController::class)->only(['destroy']);

/*
|--------------------------------------------------------------------------
| Customers (Master Data)
|--------------------------------------------------------------------------
*/
Route::resource('customers', CustomerController::class)->except('show');

/*
|--------------------------------------------------------------------------
| Products
|--------------------------------------------------------------------------
*/
// --- Product Routes ---
Route::controller(ProductController::class)->group(function () {
    // Search & Export/Import
    Route::get('/products/search', 'search')->name('products.search');
    Route::get('/export-products', 'export')->name('export.products');
    Route::post('/import-products', 'import')->name('products.import');
    
    // Toggle Status (สำหรับปุ่ม เปิด/ปิด การแสดงผล)
    Route::post('/products/{product}/toggle', 'toggleStatus')->name('products.toggle');

    // Image Management Routes (เพิ่มส่วนนี้เพื่อให้ products.images.edit ใช้งานได้)
    Route::get('/products/{product}/images', 'editImages')->name('products.images.edit');
    Route::post('/products/{product}/images', 'addImage')->name('products.images.store');
    Route::delete('/products/images/{image}', 'deleteImage')->name('products.images.destroy');
    Route::post('/products/{product}/images/{image}/main', 'setMainImage')->name('products.setMain');
});

// Resource Routes
Route::resource('products', ProductController::class);
Route::resource('product-images', ProductImageController::class); // อันนี้มีอยู่แล้ว เก็บไว้ได้

// Product Color Size Routes
Route::prefix('products/{product}/color-size')->name('product.colorSize.')->group(function () {
    Route::get('/create', [ProductColorSizeController::class, 'create'])->name('create');
    Route::post('/', [ProductColorSizeController::class, 'store'])->name('store');
});

// Stock Adjustments
Route::get('/stock/adjust/{variant}', [ProductColorSizeController::class, 'adjustForm'])->name('stock.adjust.form');
Route::get('/stock/history/{variant}', [ProductColorSizeController::class, 'history'])->name('stock.variant.history');
Route::post('/products/print-barcode', [ProductController::class, 'printBarcode'])->name('products.printBarcode');

/*
|--------------------------------------------------------------------------
| Others
|--------------------------------------------------------------------------
*/
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
