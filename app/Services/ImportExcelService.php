<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

use App\Repositories\ProductRepository;
use App\Repositories\ImportRepository;
use App\Repositories\ImportDetailRepository;
use App\Repositories\InventoryLogRepository;

use App\Imports\ImportExcelData;

class ImportExcelService
{
    public function __construct(
        protected ProductRepository $productRepo,
        protected ImportRepository $importRepo,
        protected ImportDetailRepository $detailRepo,
        protected InventoryLogRepository $logRepo
    ) {}

    public function import($file)
    {
        DB::transaction(function () use ($file) {

            $import = $this->importRepo->create([
                'code' => 'PN-' . strtoupper(Str::random(6)),
                'user_id' => Auth::id(),
                'status' => 'pending'
            ]);

            Excel::import(
                new ImportExcelData(function ($rows) use ($import) {

                    unset($rows[0]);

                    foreach ($rows as $row) {

                        $productName = $row[0];
                        $quantity = $row[1];
                        $unitPrice = $row[2];

                        $product = $this->productRepo->findByName($productName);

                        if (!$product) {
                            throw new \Exception(
                                "Không tìm thấy sản phẩm: $productName"
                            );
                        }

                        $this->detailRepo->create([
                            'import_id' => $import->id,
                            'product_id' => $product->id,
                            'quantity' => $quantity,
                            'unit_price' => $unitPrice,
                            'total_price' => $quantity * $unitPrice
                        ]);

                        $this->productRepo
                            ->increaseStock($product, $quantity);

                        $this->logRepo->create([
                            'product_id' => $product->id,
                            'type' => 'import',
                            'quantity' => $quantity,
                            'created_by' => Auth::id()
                        ]);
                    }
                }),
                $file
            );

            $this->importRepo->updateTotals($import);

        });
    }
}