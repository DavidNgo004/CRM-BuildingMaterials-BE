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
        Schema::create('imports', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique()->comment('Mã phiếu nhập');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->comment('Người lập phiếu');
            $table->decimal('total_price', 15, 2)->default(0)->comment('Tổng tiền hàng');
            $table->decimal('discount_amount', 15, 2)->default(0)->comment('Tiền chiết khấu');
            $table->decimal('grand_total', 15, 2)->default(0)->comment('Tổng thanh toán');
            $table->enum('status', ['pending', 'approved', 'completed', 'cancelled'])->default('pending')->comment('pending: Chờ duyệt, approved: Đã duyệt/Gửi Mail, completed: Đã nhận hàng/Cộng kho');
            $table->text('note')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('imports');
    }
};
