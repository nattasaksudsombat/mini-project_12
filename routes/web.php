<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use App\Http\Controllers\{
    DashboardController,
    IncomeController,
    ExpenseController,
    ProductController,
    OrderController,
    ReportController,
    SettingsController,
    CategoryController,
    ColorController,
    SizeController,
    TagController,
    OptionController,
    TypeProductController,
    ProductImageController,
    ProductOptionController,
    ProductColorController,
    ProductTagController,
    ProductColorSizeController,
    ProductExcelController,
    StockController,
    CustomerController
};

use App\Models\Product;

/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
*/
Route::get('/', fn () => redirect('/dashboard'));
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| Orders (MAIN)
|--------------------------------------------------------------------------
*/
Route::prefix('orders')->name('orders.')->group(function () {

    // ===== Customer Search / Address (ใช้กับหน้า create order) =====
    Route::get('/customers/search', [OrderController::class, 'searchCustomers'])
        ->name('customers.search');

    Route::get('/customers/{customer}/addresses', [OrderController::class, 'getCustomerAddresses'])
        ->name('customers.addresses');

    // ===== Order actions =====
    Route::post('{order}/cancel', [OrderController::class, 'cancel'])->name('cancel');
    Route::post('{order}/ship',   [OrderController::class, 'ship'])->name('ship');
    Route::patch('{order}/pay',   [OrderController::class, 'pay'])->name('pay');
    Route::patch('{order}/tracking', [OrderController::class, 'updateTracking'])->name('updateTracking');
    Route::patch('{order}/mark-paid', [OrderController::class, 'markPaid'])->name('markPaid');
});

// Order CRUD (มีแค่ครั้งเดียว)
Route::resource('orders', OrderController::class);

// ลบ item ใน order
Route::delete('/order-items/{id}', [OrderController::class, 'destroyItem'])
    ->name('order-items.destroy');

/*
|--------------------------------------------------------------------------
| Customers (Master Data)
|--------------------------------------------------------------------------
*/
Route::resource('customers', CustomerController::class)->except(['show']);

/*
|--------------------------------------------------------------------------
| Products
|--------------------------------------------------------------------------
*/
Route::prefix('products')->group(function () {

    Route::get('/search', [ProductController::class, 'search'])
        ->name('products.search');

    Route::get('{product}/variants', [ProductController::class, 'getVariants'])
        ->name('products.variants');

    Route::patch('{product}/variants/{variant}/update-stock',
        [ProductController::class, 'updateVariantStock'])
        ->name('products.variants.update-stock');

    Route::post('/print-barcode', [ProductController::class, 'printBarcode'])
        ->name('products.printBarcode');
});

Route::resource('products', ProductController::class);
Route::post('/products/{product}/toggle', [ProductController::class, 'toggle'])->name('products.toggle');

/*
|--------------------------------------------------------------------------
| Stock
|--------------------------------------------------------------------------
*/
Route::prefix('stock')->group(function () {

    Route::get('/report', [StockController::class, 'report'])->name('stock.report');
    Route::get('/export', [StockController::class, 'export'])->name('stock.export');
    Route::get('/history', [StockController::class, 'history'])->name('stock.history');

    Route::get('/{product}', [StockController::class, 'productStock'])
        ->name('stock.product');

    Route::get('/adjust/{variantId}', [StockController::class, 'adjustForm'])
        ->name('stock.adjust.form');

    Route::post('/adjust/{variantId}', [StockController::class, 'adjustSave'])
        ->name('stock.adjust.save');

    Route::get('/variant/{variantId}/history', [StockController::class, 'variantHistory'])
        ->name('stock.variant.history');
});

/*
|--------------------------------------------------------------------------
| Income / Expense
|--------------------------------------------------------------------------
*/
Route::resource('incomes', IncomeController::class);
Route::resource('expenses', ExpenseController::class);

/*
|--------------------------------------------------------------------------
| Settings / Master
|--------------------------------------------------------------------------
*/
Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');

Route::resource('categories', CategoryController::class);
Route::resource('colors', ColorController::class);
Route::resource('sizes', SizeController::class);
Route::resource('tags', TagController::class);
Route::resource('options', OptionController::class);
Route::resource('types', TypeProductController::class);

/*
|--------------------------------------------------------------------------
| Product Images
|--------------------------------------------------------------------------
*/
Route::get('/products/{product}/images/edit', [ProductImageController::class, 'edit'])
    ->name('products.images.edit');

Route::put('/products/{product}/images', [ProductImageController::class, 'update'])
    ->name('products.images.update');

Route::patch('/products/{product}/images/{image}/main',
    [ProductImageController::class, 'setMain'])
    ->name('products.images.setMain');

Route::delete('/product-images/{image}', [ProductImageController::class, 'destroy'])
    ->name('productImages.destroy');

/*
|--------------------------------------------------------------------------
| Reports
|--------------------------------------------------------------------------
*/
Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

/*
|--------------------------------------------------------------------------
| Import / Export
|--------------------------------------------------------------------------
*/
Route::get('/export-products', [ProductExcelController::class, 'export'])->name('export.products');
Route::post('/import-products', [ProductExcelController::class, 'import'])->name('import.products');
