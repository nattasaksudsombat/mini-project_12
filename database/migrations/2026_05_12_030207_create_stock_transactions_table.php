<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_transactions', function (Blueprint $table) {
            $table->id();
            
            // ✅ เชื่อม Foreign Key ไปที่ product_color_sizes (ตัวแปรย่อยของสินค้า)
            $table->unsignedBigInteger('product_color_size_id');
            $table->foreign('product_color_size_id')
                  ->references('id')
                  ->on('product_color_sizes')
                  ->onDelete('cascade'); // ลบสินค้าปุ๊บ ประวัติสต๊อกหายตาม

            $table->enum('type', ['in', 'out', 'adjust']); // ประเภท: รับเข้า, จ่ายออก, ปรับปรุง
            $table->integer('quantity'); // จำนวน (+ หรือ -)
            $table->string('reference_number')->nullable(); // เลขอ้างอิง เช่น เลขออเดอร์
            $table->text('notes')->nullable(); // หมายเหตุ
            
            // ✅ เชื่อม Foreign Key ไปที่ users (พนักงานที่ทำรายการ)
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_transactions');
    }
};