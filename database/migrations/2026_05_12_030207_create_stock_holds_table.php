<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_holds', function (Blueprint $table) {
            $table->id();
            
            // ✅ เชื่อม Foreign Key ไปที่ product_color_sizes
            $table->unsignedBigInteger('product_color_size_id');
            $table->foreign('product_color_size_id')
                  ->references('id')
                  ->on('product_color_sizes')
                  ->onDelete('cascade');

            $table->integer('quantity'); // จำนวนที่ถูกกั๊ก
            $table->string('session_id')->nullable(); // กั๊กจาก Session ของลูกค้า
            
            // ถ้าระบบคุณโยงกับ Order 
            $table->unsignedBigInteger('order_id')->nullable();
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');

            $table->dateTime('expires_at'); // เวลาที่หมดอายุกั๊กสต๊อก
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_holds');
    }
};