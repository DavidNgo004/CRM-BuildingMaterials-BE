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
        Schema::table('export_details', function (Blueprint $table) {
            $table->decimal('import_price', 15, 2)->default(0)->after('unit_price')->comment('Giá vốn (COGS) tại thời điểm xuất');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('export_details', function (Blueprint $table) {
            $table->dropColumn('import_price');
        });
    }
};
