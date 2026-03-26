<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('title')->comment('Tên khoản chi (Lương, Điện, Vận chuyển...)');
            $table->unsignedBigInteger('amount')->comment('Số tiền chi (VND)');
            $table->date('expense_date')->comment('Ngày chi');
            $table->text('note')->nullable()->comment('Ghi chú');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
