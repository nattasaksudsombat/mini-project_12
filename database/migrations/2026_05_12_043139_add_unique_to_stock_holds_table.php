<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
{
    Schema::table('stock_holds', function (Blueprint $table) {
        // เพิ่ม unique constraint เพื่อกันการ insert ข้อมูลชุดเดียวกันซ้ำ
        $table->unique(['product_color_size_id', 'order_id', 'status'], 'unique_stock_hold');
    });
}

public function down(): void
{
    Schema::table('stock_holds', function (Blueprint $table) {
        $table->dropUnique('unique_stock_hold');
    });
}
};
