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
        Schema::create('export_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('export_id')->constrained('exports')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->integer('quantity')->default(1)->comment('Số lượng xuất');
            $table->decimal('unit_price', 15, 2)->default(0)->comment('Đơn giá xuất (Giá bán)');
            $table->decimal('total_price', 15, 2)->default(0)->comment('Thành tiền (Doanh thu)');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('export_details');
    }
};
