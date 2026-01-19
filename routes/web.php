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
    UserManagementController,
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

    // ✅ Dashboard (ทุกคน)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // =========================================================
    // 🌍 Shared API Routes (ใช้ร่วมกัน Admin + Sales + Stock)
    // =========================================================
    
    // 1. API ดึงที่อยู่ลูกค้า
    Route::get('/api/customers/{customer}/addresses', function (\App\Models\Customer $customer) {
        return response()->json($customer->addresses);
    })->name('api.customer.addresses');

    // ✅ 2. API ดึงข้อมูลสินค้า สี/ไซส์/สต็อก (แยกออกมาไว้ตรงนี้ Sales ถึงจะกดได้)
    Route::get('/api/product/{id}/colors', [ProductColorSizeController::class, 'getColors'])->name('api.product.colors');
    Route::get('/api/product/{id}/sizes', [ProductColorSizeController::class, 'getSizes'])->name('api.product.sizes');
    Route::post('/api/check-stock', [ProductColorSizeController::class, 'checkStock'])->name('api.check.stock');
    
    // 3. Global Search API
    Route::get('/api/global-search', [ProductController::class, 'globalSearch'])->name('api.global.search');


    // =========================================================
    // 🌍 Public Read-Only (ดูได้ทุกคน)
    // =========================================================
    // Products - Read Only
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/search', [ProductController::class, 'ajaxSearch'])->name('products.search');
    Route::get('/products/{product}/barcode', [ProductController::class, 'barcodePreview'])->name('products.barcode_preview');
    
    // ✅ Orders - Read Only (ทุกคนดูได้รวมทั้ง Stock)
  
// ✅ Orders - Index Only (เฉพาะดูรายการ)
Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');

// ✅ Customers - Read Only
Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');



    // =========================================================
    // 👑 Admin ONLY
    // =========================================================
    Route::middleware(['role:admin'])->group(function () {
        // รายงาน & การเงิน
        Route::resource('incomes', IncomeController::class);
        Route::resource('expenses', ExpenseController::class);
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        
        // จัดการ Users
        Route::resource('users', UserController::class);
        // Dashboard สรุปภาพรวม
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    
    // กราฟวิเคราะห์
    Route::get('/reports/charts', [ReportController::class, 'charts'])->name('reports.charts');
    
    // รายงานการเงิน
    Route::get('/reports/financial', [ReportController::class, 'financial'])->name('reports.financial');
    
    // Export Excel (CSV)
    Route::get('/reports/export/financial', [ReportController::class, 'exportFinancial'])->name('reports.export.financial');+
     // หน้าตั้งค่า
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    
    // บันทึกการตั้งค่า
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::resource('users', UserManagementController::class)->names([
        'index'   => 'users.index',    // GET  /users
        'create'  => 'users.create',   // GET  /users/create
        'store'   => 'users.store',    // POST /users
        'show'    => 'users.show',     // GET  /users/{id} (ไม่ได้ใช้ แต่ให้ไว้)
        'edit'    => 'users.edit',     // GET  /users/{id}/edit
        'update'  => 'users.update',   // PUT  /users/{id}
        'destroy' => 'users.destroy',  // DELETE /users/{id}
    ]);
    });


    // =========================================================
    // 🕑 Shared Routes (Admin + Stock + Sales)
    // =========================================================
    Route::middleware(['role:admin,stock,sales'])->group(function () {
        // ประวัติสต็อกของ Variant
        Route::get('/stock/history/{variant}', [StockController::class, 'variantHistory'])
            ->name('stock.variant.history');

        Route::get('/stock/api/holds/{variant}', [StockController::class, 'getVariantHolds'])
            ->name('stock.api.holds');
        
        // ⭐ เพิ่มเส้นทางนี้เพื่อให้ Sales ดึง variants ได้ (สำหรับสร้างออเดอร์)
        Route::get('/products/{product}/variants', [ProductController::class, 'getVariants'])
            ->name('products.variants.shared');
    });


    // =========================================================
    // 📦 Stock Management (Admin + Stock)
    // =========================================================
    Route::middleware(['role:admin,stock'])->group(function () {
        
        // Export/Import
        Route::get('/export-products', [ProductController::class, 'export'])->name('export.products');
        Route::post('/import-products', [ProductController::class, 'import'])->name('products.import');
        Route::post('/products/import-excel', [ProductExcelController::class, 'import'])->name('products.excel.import');
        Route::post('/products/print-barcode', [ProductController::class, 'printBarcode'])->name('products.printBarcode');
        
        // Products - CRUD
        Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
        Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
        Route::post('/products/{product}/toggle', [ProductController::class, 'toggleStatus'])->name('products.toggle');
        
        // Product Images
        Route::prefix('products/{product}/images')->name('product_images.')->group(function () {
            Route::get('/', [ProductImageController::class, 'index'])->name('index');
            Route::post('/', [ProductImageController::class, 'store'])->name('store');
        });
        Route::delete('/product-images/{productImage}', [ProductImageController::class, 'destroy'])->name('product_images.destroy');
        Route::post('/product-images/{productImage}/main', [ProductController::class, 'setMainImage'])->name('product_images.set_main');
        
        // Product Color-Size Variants
        Route::get('/products/{product}/color-size/create', [ProductColorSizeController::class, 'create'])->name('product.colorSize.create');
        Route::post('/products/{product}/color-size', [ProductColorSizeController::class, 'store'])->name('product.colorSize.store');
        Route::get('/products/{product}/color-size/{colorSize}/edit', [ProductColorSizeController::class, 'edit'])->name('product.colorSize.edit');
        Route::put('/products/{product}/color-size/{colorSize}', [ProductColorSizeController::class, 'update'])->name('product.colorSize.update');
        Route::delete('/products/{product}/color-size/{colorSize}', [ProductColorSizeController::class, 'destroy'])->name('product.colorSize.destroy');
        
        // Stock Management
        Route::get('/stock/adjust/{variant}', [StockController::class, 'adjustForm'])->name('stock.adjust.form');
        Route::post('/stock/adjust/{variant}', [StockController::class, 'adjustSave'])->name('stock.adjust.save');
        
        // Master Data
        Route::resource('categories', CategoryController::class);
        Route::resource('colors', ColorController::class);
        Route::resource('sizes', SizeController::class);
        Route::resource('tags', TagController::class);
        Route::resource('options', OptionController::class);
        Route::resource('types', TypeProductController::class);
    });


    // =========================================================
    // 🛒 Sales Management (Admin + Sales)
    // =========================================================
   Route::middleware(['role:admin,sales'])->group(function () {
    
    // Orders - Helpers
    Route::get('/orders/customers/search', [OrderController::class, 'searchCustomers'])->name('orders.customers.search');
    Route::get('/orders/customers/{customer}/addresses', [OrderController::class, 'getCustomerAddresses'])->name('orders.customers.addresses');
    
    // ⭐ Orders - CRUD (เรียงลำดับใหม่: create/store ก่อน show/edit/update)
    Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{order}/edit', [OrderController::class, 'edit'])->name('orders.edit');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::put('/orders/{order}', [OrderController::class, 'update'])->name('orders.update');
    Route::delete('/orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');
    
    // Orders - Actions
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::post('/orders/{order}/ship', [OrderController::class, 'ship'])->name('orders.ship');
    Route::patch('/orders/{order}/pay', [OrderController::class, 'pay'])->name('orders.pay');
    Route::patch('/orders/{order}/tracking', [OrderController::class, 'updateTracking'])->name('orders.updateTracking');
    
    // Order Items
    Route::delete('/order-items/{orderItem}', [OrderItemController::class, 'destroy'])->name('order-items.destroy');
    
    // ✅ Customers - CRUD
    Route::get('/customers/create', [CustomerController::class, 'create'])->name('customers.create');
    Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
    Route::get('/customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
    Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
    Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');
    
    // ✅ หน้าดูสินค้าสำหรับ Sales
    Route::get('/sales/products', [ProductController::class, 'salesIndex'])->name('sales.products.index');
    Route::get('/sales/products/{product}', [ProductController::class, 'salesShow'])->name('sales.products.show');
});


   
});