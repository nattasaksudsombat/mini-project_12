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

    // ✅ Dashboard (ทุกคน)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // =========================================================
    // 🌍 Public Read-Only (ดูได้ทุกคน)
    // =========================================================
    // ... (Route Products เดิม) ...
     Route::get('/products/search', [ProductController::class, 'ajaxSearch'])->name('products.search');
        Route::resource('products', ProductController::class);
        Route::post('/products/{product}/toggle', [ProductController::class, 'toggleStatus'])->name('products.toggle');
        Route::get('/products/{product}/barcode', [ProductController::class, 'barcodePreview'])->name('products.barcode_preview');

        // ✅ [เพิ่มใหม่] จัดการรูปภาพสินค้า (Product Images)
        Route::prefix('products/{product}/images')->name('product_images.')->group(function () {
            Route::get('/', [ProductImageController::class, 'index'])->name('index');   // product_images.index
            Route::post('/', [ProductImageController::class, 'store'])->name('store');  // product_images.store
        });

        // Route สำหรับลบและตั้งรูปหลัก (แยกออกมาเพราะไม่ได้ใช้ {product} ใน URL)
        Route::delete('/product-images/{productImage}', [ProductImageController::class, 'destroy'])->name('product_images.destroy');
        Route::post('/product-images/{productImage}/main', [ProductController::class, 'setMainImage'])->name('product_images.set_main');
    // Products - Read Only
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/search', [ProductController::class, 'search'])->name('products.search');
    
    // Orders - Read Only  
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');


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
        // ✅ Global Search API (สำหรับ Navbar)
    // Route สำหรับค้นหา (Global Search)
       
    });
// =========================================================
    // 🕒 Shared Routes (Admin + Stock + Sales)
    // =========================================================
    Route::middleware(['role:admin,stock,sales'])->group(function () {
        // ประวัติสต๊อกของ Variant (ให้เข้าได้ทั้ง 3 ตำแหน่ง)
        Route::get('/stock/history/{variant}', [StockController::class, 'variantHistory'])
            ->name('stock.variant.history');

            Route::get('/stock/api/holds/{variant}', [StockController::class, 'getVariantHolds'])
        ->name('stock.api.holds');
    });

    // =========================================================
    // 📦 Stock Management (Admin + Stock)
    // =========================================================
    Route::middleware(['role:admin,stock'])->group(function () {
        
        // ⚠️ สำคัญ: Routes เฉพาะเจาะจงต้องอยู่ก่อน {product}
        
        // Export/Import
        Route::get('/export-products', [ProductController::class, 'export'])->name('export.products');
        Route::post('/import-products', [ProductController::class, 'import'])->name('products.import');
        Route::post('/products/import-excel', [ProductExcelController::class, 'import'])->name('products.excel.import');
        Route::post('/products/print-barcode', [ProductController::class, 'printBarcode'])->name('products.printBarcode');
        
        // Products - CRUD (เรียงตามลำดับความเฉพาะเจาะจง)
        Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
        Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
        Route::post('/products/{product}/toggle', [ProductController::class, 'toggleStatus'])->name('products.toggle');
        
        // Product Variants
        Route::get('/products/{product}/variants', [ProductController::class, 'getVariants'])->name('products.variants');
        
        // Product Images
        Route::get('/products/{product}/images', [ProductImageController::class, 'edit'])->name('products.images.edit');
        Route::post('/products/{product}/images', [ProductImageController::class, 'store'])->name('products.images.store');
        Route::post('/products/{product}/images/{image}/main', [ProductImageController::class, 'setMain'])->name('products.images.setMain');
        Route::delete('/products/images/{image}', [ProductImageController::class, 'destroy'])->name('products.images.destroy');
        
        // Product Color-Size Variants
        Route::get('/products/{product}/color-size/create', [ProductColorSizeController::class, 'create'])->name('product.colorSize.create');
        Route::post('/products/{product}/color-size', [ProductColorSizeController::class, 'store'])->name('product.colorSize.store');
        Route::get('/products/{product}/color-size/{colorSize}/edit', [ProductColorSizeController::class, 'edit'])->name('product.colorSize.edit');
        Route::put('/products/{product}/color-size/{colorSize}', [ProductColorSizeController::class, 'update'])->name('product.colorSize.update');
        Route::delete('/products/{product}/color-size/{colorSize}', [ProductColorSizeController::class, 'destroy'])->name('product.colorSize.destroy');
        
        // Stock Management
        Route::get('/stock/adjust/{variant}', [StockController::class, 'adjustForm'])->name('stock.adjust.form');
        
        // บันทึกปรับสต๊อก (ใน StockController ชื่อฟังก์ชันคือ adjustSave ไม่ใช่ saveAdjustment)
        Route::post('/stock/adjust/{variant}', [StockController::class, 'adjustSave'])->name('stock.adjust.save');
        
        // ประวัติสต๊อกของ Variant (ใน StockController ชื่อฟังก์ชันคือ variantHistory ไม่ใช่ history)
        Route::get('/stock/history/{variant}', [StockController::class, 'variantHistory'])->name('stock.variant.history');
        
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
        
        // ⚠️ สำคัญ: Routes เฉพาะเจาะจงต้องอยู่ก่อน {order}
        
    Route::get('/api/global-search', [ProductController::class, 'globalSearch'])->name('api.global.search');
        // Orders - Helpers (ต้องอยู่ก่อน CRUD)
        Route::get('/orders/customers/search', [OrderController::class, 'searchCustomers'])->name('orders.customers.search');
        Route::get('/orders/customers/{customer}/addresses', [OrderController::class, 'getCustomerAddresses'])->name('orders.customers.addresses');
        
        // Orders - CRUD
        Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
        Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::get('/orders/{order}/edit', [OrderController::class, 'edit'])->name('orders.edit');
        Route::put('/orders/{order}', [OrderController::class, 'update'])->name('orders.update');
        Route::delete('/orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');
        
        // Orders - Actions
        Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
        Route::post('/orders/{order}/ship', [OrderController::class, 'ship'])->name('orders.ship');
        Route::patch('/orders/{order}/pay', [OrderController::class, 'pay'])->name('orders.pay');
        Route::patch('/orders/{order}/tracking', [OrderController::class, 'updateTracking'])->name('orders.updateTracking');
        
        // Order Items
        Route::delete('/order-items/{orderItem}', [OrderItemController::class, 'destroy'])->name('order-items.destroy');
        
        // Customers
        Route::resource('customers', CustomerController::class)->except(['show']);
        // ✅ [ใหม่] หน้าดูสินค้าสำหรับ Sales (ห้ามแก้ไข)
        Route::get('/sales/products', [ProductController::class, 'salesIndex'])->name('sales.products.index');
        Route::get('/sales/products/{product}', [ProductController::class, 'salesShow'])->name('sales.products.show');
                // ประวัติสต๊อกของ Variant (ใน StockController ชื่อฟังก์ชันคือ variantHistory ไม่ใช่ history)
        Route::get('/stock/history/{variant}', [StockController::class, 'variantHistory'])->name('stock.variant.history');
    });

});