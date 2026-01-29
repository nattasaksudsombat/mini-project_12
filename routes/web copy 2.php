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

        // ✅ เพิ่มบรรทัดนี้ครับ (เพื่อแก้ Error)
        Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');

        Route::resource('users', UserController::class);
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [ReportController::class, 'index'])->name('index');
            Route::get('/charts', [ReportController::class, 'charts'])->name('charts');
            Route::get('/financial', [ReportController::class, 'financial'])->name('financial');
            Route::get('/export/financial', [ReportController::class, 'exportFinancial'])->name('export.financial');
            Route::get('/api/customers/{customer}/addresses', function (\App\Models\Customer $customer) {
                return response()->json($customer->addresses);
            })->name('api.customer.addresses');
        });
        Route::get('/api/product/{id}/colors', [ProductColorSizeController::class, 'getColors'])->name('api.product.colors');
        Route::get('/api/product/{id}/sizes', [ProductColorSizeController::class, 'getSizes'])->name('api.product.sizes');
        Route::post('/api/check-stock', [ProductColorSizeController::class, 'checkStock'])->name('api.check.stock');

        // 3. Global Search API
        Route::get('/api/global-search', [ProductController::class, 'globalSearch'])->name('api.global.search');
                Route::get('/products/search', [ProductController::class, 'search'])->name('products.search');
        Route::get('/products/{product}/variants', [ProductController::class, 'getVariantsApi'])->name('products.variants.api');
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
 // ✅ เพิ่ม Route สำหรับค้นหาสินค้าและดึง variants (สำหรับสร้าง Order)
 // รายการสินค้า (มุมมอง Sales)
        Route::get('/products', [ProductController::class, 'salesIndex'])->name('products.index');
        
        // รายละเอียดสินค้า & ราคา (มุมมอง Sales)
        Route::get('/products/{product}', [ProductController::class, 'salesShow'])->name('products.show');
        Route::get('/products/search', [ProductController::class, 'search'])->name('products.search');
        Route::get('/products/{product}/variants', [ProductController::class, 'getVariantsApi'])->name('products.variants.api');
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
            Route::prefix('products/{product}/images')->name('product_images.')->group(function () {
        Route::get('/', [ProductImageController::class, 'index'])->name('index');
        Route::post('/', [ProductImageController::class, 'store'])->name('store');
        Route::put('/{image}', [ProductImageController::class, 'update'])->name('update');
        Route::delete('/{image}', [ProductImageController::class, 'destroy'])->name('destroy');
        Route::post('/{image}/set-main', [ProductImageController::class, 'setMain'])->name('setMain');
    });
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

        Route::get('/products', [ProductController::class, 'salesIndex'])->name('products.index');
        Route::get('/products/{product}', [ProductController::class, 'salesShow'])->name('products.show');
   
    // =========================================================
    // 🌍 Public Read-Only (Wildcard Routes ควรอยู่ล่างสุดเสมอ)
    // =========================================================
    Route::get('/products/api/search-order', [ProductController::class, 'searchApi'])->name('products.api.search');
    Route::controller(ProductController::class)->group(function () {
        Route::get('/products', 'index')->name('products.index');
        Route::get('/products/{product}/variants', 'getVariantsApi')->name('products.variants.api');
        Route::get('/products/{product}', 'show')->name('products.show');
    });

    Route::controller(OrderController::class)->group(function () {
        Route::get('/orders', 'index')->name('orders.index');
        Route::get('/orders/{order}', 'show')->name('orders.show');
    });
});
