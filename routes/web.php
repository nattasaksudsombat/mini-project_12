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
    ProductExcelController,
    AuthController,
    UserController,
};

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Protected Routes (ต้อง Login ก่อน)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // ✅ 1. Dashboard (ทุกคน)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // =========================================================
    // 👑 Admin ONLY (เอาไว้บนสุดเพื่อความปลอดภัย)
    // =========================================================
    Route::middleware(['role:admin'])->group(function () {
        Route::resource('incomes', IncomeController::class);
        Route::resource('expenses', ExpenseController::class);
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
        
        Route::resource('users', UserController::class);
          Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/charts', [ReportController::class, 'charts'])->name('charts');
        Route::get('/financial', [ReportController::class, 'financial'])->name('financial');
        Route::get('/export/financial', [ReportController::class, 'exportFinancial'])->name('export.financial');
         Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
    });
    });

    // =========================================================
    // 🛒 Sales Management (Admin + Sales) 
    // ⚠️ ต้องประกาศก่อน Wildcard Route ของ Order
    // =========================================================
    Route::middleware(['role:admin,sales'])->group(function () {
        
        Route::prefix('orders')->name('orders.')->group(function () {
            // Helpers
            Route::get('customers/search', [OrderController::class, 'searchCustomers'])->name('customers.search');
            Route::get('customers/{customer}/addresses', [OrderController::class, 'getCustomerAddresses'])->name('customers.addresses');
            
            // Actions
            Route::post('{order}/cancel', [OrderController::class, 'cancel'])->name('cancel');
            Route::post('{order}/ship',   [OrderController::class, 'ship'])->name('ship');
            Route::patch('{order}/pay',   [OrderController::class, 'pay'])->name('pay');
            Route::patch('{order}/tracking', [OrderController::class, 'updateTracking'])->name('updateTracking');
        });

        // CRUD Order (create ต้องมาก่อน show)
        Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
        Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
        Route::get('/orders/{order}/edit', [OrderController::class, 'edit'])->name('orders.edit');
        Route::put('/orders/{order}', [OrderController::class, 'update'])->name('orders.update');
        Route::delete('/orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');

        Route::resource('order-items', OrderItemController::class)->only(['destroy']);
        Route::resource('customers', CustomerController::class)->except('show');
    });

    // =========================================================
    // 📦 Stock Management (Admin + Stock)
    // =========================================================
    Route::middleware(['role:admin,stock'])->group(function () {
        
        // CRUD สินค้า
        Route::controller(ProductController::class)->group(function () {
            Route::get('/products/create', 'create')->name('products.create');
            Route::post('/products', 'store')->name('products.store');
            Route::get('/products/{product}/edit', 'edit')->name('products.edit');
            Route::put('/products/{product}', 'update')->name('products.update');
            Route::delete('/products/{product}', 'destroy')->name('products.destroy');

            // ฟังก์ชันเสริม
            Route::get('/export-products', 'export')->name('export.products');
            Route::post('/import-products', 'import')->name('products.import');
            Route::post('/products/{product}/toggle', 'toggleStatus')->name('products.toggle');
            Route::post('/products/print-barcode', 'printBarcode')->name('products.printBarcode');

            // รูปภาพ
            Route::get('/products/{product}/images', 'editImages')->name('products.images.edit');
            Route::post('/products/{product}/images', 'addImage')->name('products.images.store');
            Route::delete('/products/images/{image}', 'deleteImage')->name('products.images.destroy');
            Route::post('/products/{product}/images/{image}/main', 'setMainImage')->name('products.setMain');
        });

        // จัดการ Variants & Stock
        Route::prefix('products/{product}/color-size')->name('product.colorSize.')->group(function () {
            Route::get('/create', [ProductColorSizeController::class, 'create'])->name('create');
            Route::post('/', [ProductColorSizeController::class, 'store'])->name('store');
        });

        // ปรับสต๊อก
        Route::get('/stock/adjust/{variant}', [StockController::class, 'adjustForm'])->name('stock.adjust.form');
        Route::post('/stock/adjust/{variant}', [StockController::class, 'adjustSave'])->name('stock.adjust.save');
        Route::get('/stock/history/{variant}', [StockController::class, 'variantHistory'])->name('stock.variant.history');

        // Master Data
        Route::resource('categories', CategoryController::class);
        Route::resource('colors', ColorController::class);
        Route::resource('sizes', SizeController::class);
        Route::resource('tags', TagController::class);
        Route::resource('options', OptionController::class);
        Route::resource('types', TypeProductController::class);
        
        Route::post('products/import-excel', [ProductExcelController::class, 'import'])->name('products.excel.import');
          Route::get('/stock/report', [StockController::class, 'report'])->name('stock.report');
    
    Route::get('/stock/adjust/{variant}', [StockController::class, 'adjustForm'])->name('stock.adjust.form');
    });

    // =========================================================
    // 🌍 Public Read-Only (Wildcard Routes ควรอยู่ล่างสุดเสมอ)
    // =========================================================
    Route::controller(ProductController::class)->group(function () {
        Route::get('/products', 'index')->name('products.index');
        Route::get('/products/search', 'search')->name('products.search');
        Route::get('/products/{product}/variants', 'getVariantsApi')->name('products.variants.api'); 
        Route::get('/products/{product}', 'show')->name('products.show');
    });

    Route::controller(OrderController::class)->group(function () {
        Route::get('/orders', 'index')->name('orders.index');
        Route::get('/orders/{order}', 'show')->name('orders.show');
    });

});