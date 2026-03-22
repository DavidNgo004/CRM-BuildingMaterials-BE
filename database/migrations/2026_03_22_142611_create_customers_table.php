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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique()->comment('Mã khách hàng tự tạo');
            $table->string('name')->comment('Tên khách hàng');
            $table->string('phone', 20)->unique()->comment('SĐT suy nhất 10 số');
            $table->text('address')->nullable()->comment('Địa chỉ khách hàng');
            $table->string('customer_type', 50)->default('retail')->comment('Loại KH (wholesale/retail, sỉ/lẻ...) để áp dụng ưu đãi');
            $table->boolean('status')->default(true)->comment('Trạng thái hoạt động');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
