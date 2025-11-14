<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ใช้ Bootstrap สำหรับ pagination เป็นค่าเริ่มต้น
        Paginator::useBootstrap();
        
        // กำหนดค่าเริ่มต้นให้แสดง 20 รายการต่อหน้า
        // สามารถใช้ในทุก Model โดยไม่ต้องระบุ paginate(20) ทุกครั้ง
        // Model::$perPage = 20;
    }
}