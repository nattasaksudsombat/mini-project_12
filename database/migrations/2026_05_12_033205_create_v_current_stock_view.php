<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ✅ ใช้คำสั่ง Raw SQL เพื่อสร้าง View
        DB::statement("
            CREATE OR REPLACE VIEW v_current_stock AS 
            SELECT 
                product_id, 
                color_id, 
                size_id, 
                quantity AS available_stock 
            FROM product_color_sizes;
        ");
    }

    public function down(): void
    {
        // ลบ View ทิ้งเมื่อกด Rollback
        DB::statement("DROP VIEW IF EXISTS v_current_stock;");
    }
};