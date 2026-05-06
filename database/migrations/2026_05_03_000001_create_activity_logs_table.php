<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_name')->nullable();          // lưu cache tên để không bị null khi user bị xóa
            $table->string('user_role')->nullable();          // admin / warehouse_staff
            $table->string('action', 50);                     // CREATE_IMPORT, APPROVE_EXPORT, …
            $table->string('entity_type', 50);                // product | import | export | expense | …
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->json('old_data')->nullable();             // dữ liệu TRƯỚC khi thay đổi
            $table->json('new_data')->nullable();             // dữ liệu SAU khi thay đổi
            $table->string('description')->nullable();        // mô tả ngắn gọn
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->index(['entity_type', 'entity_id']);
            $table->index(['user_id', 'created_at']);
            $table->index('action');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
