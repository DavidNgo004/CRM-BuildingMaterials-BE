<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Export;
use App\Models\ExportDetail;
use Carbon\Carbon;

class AITestDatasetSeeder extends Seeder
{
    public function run(): void
    {
        $today = Carbon::now();

        /*
        ============================
        PRODUCT 1 → OUT OF STOCK
        ============================
        */

        $cement = Product::create([
            'supplier_id' => 1,
            'name' => 'Xi măng PCB40',
            'unit' => 'bao',
            'import_price' => 50000,
            'sell_price' => 62000,
            'stock' => 0,
            'reorder_level' => 50,
            'status' => 1
        ]);


        /*
        ============================
        PRODUCT 2 → LOW STOCK + AI FORECAST
        ============================
        */

        $steel = Product::create([
            'supplier_id' => 1,
            'name' => 'Thép D10',
            'unit' => 'cây',
            'import_price' => 250000,
            'sell_price' => 315000,
            'stock' => 20,
            'reorder_level' => 40,
            'status' => 1
        ]);


        /*
        ============================
        PRODUCT 3 → SLOW MOVING
        ============================
        */

        $sand = Product::create([
            'supplier_id' => 2,
            'name' => 'Cát xây',
            'unit' => 'm3',
            'import_price' => 180000,
            'sell_price' => 220000,
            'stock' => 120,
            'reorder_level' => 30,
            'status' => 1
        ]);


        /*
        ============================
        PRODUCT 4 → OVERSTOCK
        ============================
        */

        $brick = Product::create([
            'supplier_id' => 2,
            'name' => 'Gạch đỏ',
            'unit' => 'viên',
            'import_price' => 900,
            'sell_price' => 1200,
            'stock' => 500,
            'reorder_level' => 100,
            'status' => 1
        ]);


        /*
        ============================
        EXPORT HISTORY DATASET
        ============================
        */

        // STEEL → demand tăng đều (AI học trend tăng)
        for ($i = 30; $i >= 1; $i--) {

            $export = Export::create([
                'code' => 'EX-AI-' . uniqid(),
                'user_id' => 1,
                'customer_id' => 2,
                'status' => 'completed',
                'created_at' => $today->copy()->subDays($i),
                'updated_at' => $today->copy()->subDays($i),
            ]);

            ExportDetail::create([
                'export_id' => $export->id,
                'product_id' => $steel->id,
                'quantity' => rand(5, 12),
                'unit_price' => 315000,
                'import_price' => 250000,
                'total_price' => 315000 * rand(5, 12),
            ]);
        }


        // SAND → demand giảm mạnh (slow moving detection)
        for ($i = 60; $i >= 31; $i--) {

            $export = Export::create([
                'code' => 'EX-AI-' . uniqid(),
                'user_id' => 1,
                'customer_id' => 2,
                'status' => 'completed',
                'created_at' => $today->copy()->subDays($i),
                'updated_at' => $today->copy()->subDays($i),
            ]);

            ExportDetail::create([
                'export_id' => $export->id,
                'product_id' => $sand->id,
                'quantity' => rand(8, 12),
                'unit_price' => 220000,
                'import_price' => 180000,
                'total_price' => 220000 * rand(8, 12),
            ]);
        }

        for ($i = 30; $i >= 1; $i--) {

            $export = Export::create([
                'code' => 'EX-AI-' . uniqid(),
                'user_id' => 1,
                'customer_id' => 2,
                'status' => 'completed',
                'created_at' => $today->copy()->subDays($i),
                'updated_at' => $today->copy()->subDays($i),
            ]);

            ExportDetail::create([
                'export_id' => $export->id,
                'product_id' => $sand->id,
                'quantity' => rand(1, 3),
                'unit_price' => 220000,
                'import_price' => 180000,
                'total_price' => 220000 * rand(1, 3),
            ]);
        }


        // BRICK → không bán 30 ngày gần nhất → overstock
        for ($i = 60; $i >= 31; $i--) {

            $export = Export::create([
                'code' => 'EX-AI-' . uniqid(),
                'user_id' => 1,
                'customer_id' => 2,
                'status' => 'completed',
                'created_at' => $today->copy()->subDays($i),
                'updated_at' => $today->copy()->subDays($i),
            ]);

            ExportDetail::create([
                'export_id' => $export->id,
                'product_id' => $brick->id,
                'quantity' => rand(10, 20),
                'unit_price' => 1200,
                'import_price' => 900,
                'total_price' => 1200 * rand(10, 20),
            ]);
        }
    }
}