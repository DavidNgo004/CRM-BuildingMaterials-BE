<?php

namespace App\Services;

use App\Repositories\ImportRepository;
use App\Models\Import;
use App\Models\ImportDetail;
use App\Models\Product;
use App\Mail\OrderSupplierMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Exception;

class ImportService
{
    protected $importRepository;

    public function __construct(ImportRepository $importRepository)
    {
        $this->importRepository = $importRepository;
    }

    public function paginate($perPage = 15, $search = null)
    {
        return $this->importRepository->paginate($perPage, $search);
    }

    public function find($id)
    {
        return $this->importRepository->find($id);
    }

    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            $total_price = 0;

            foreach ($data['details'] as $detail) {
                $total_price += ($detail['unit_price'] * $detail['quantity']);
            }

            $discount = $data['discount_amount'] ?? 0;
            $grand_total = max(0, $total_price - $discount);

            $import = Import::create([
                'user_id' => auth()->id() ?? 1,
                'total_price' => $total_price,
                'discount_amount' => $discount,
                'grand_total' => $grand_total,
                'status' => 'pending',
                'note' => $data['note'] ?? null,
            ]);

            foreach ($data['details'] as $detail) {
                ImportDetail::create([
                    'import_id' => $import->id,
                    'product_id' => $detail['product_id'],
                    'quantity' => $detail['quantity'],
                    'unit_price' => $detail['unit_price'],
                    'total_price' => $detail['unit_price'] * $detail['quantity'],
                ]);
            }

            return $import->load('details.product.supplier');
        });
    }

    public function changeStatus($id, $status)
    {
        return DB::transaction(function () use ($id, $status) {
            $import = Import::with('details.product.supplier')->findOrFail($id);

            if ($import->status === 'completed' || $import->status === 'cancelled') {
                throw new Exception("Phiếu nhập đang ở trạng thái {$import->status} nên không thể thay đổi.");
            }

            $oldStatus = $import->status;
            $import->status = $status;
            $import->save();

            // Tự động gửi Email khi trạng thái chuyển qua 'approved' (Admin duyệt phiếu cho Kho)
            if ($status === 'approved' && $oldStatus === 'pending') {
                // Gom nhóm sản phẩm theo Nhà Nung Cấp
                $supplierGroups = [];
                foreach ($import->details as $detail) {
                    $prod = $detail->product;
                    if ($prod && $prod->supplier) {
                        $supId = $prod->supplier->id;
                        if (!isset($supplierGroups[$supId])) {
                            $supplierGroups[$supId] = [
                                'supplier' => $prod->supplier,
                                'products' => []
                            ];
                        }
                        $supplierGroups[$supId]['products'][] = [
                            'product' => $prod,
                            'quantity' => $detail->quantity
                        ];
                    }
                }

                foreach ($supplierGroups as $supId => $data) {
                    $supplier = $data['supplier'];
                    // Chỉ gửi nếu supplier có email
                    if (!empty($supplier->email)) {
                        try {
                            Mail::to($supplier->email)
                                ->send(new OrderSupplierMail($import, $supplier, $data['products']));
                        }
                        catch (\Exception $e) {
                            \Illuminate\Support\Facades\Log::error('Mail Error: ' . $e->getMessage());
                        // Nuốt lỗi gửi mail để không gãy transaction khi smtp lỗi
                        }
                    }
                }
            }

            // Khi hàng vật lý đã về kho -> Cập nhật Tồn kho (Completed)
            if ($status === 'completed') {
                foreach ($import->details as $detail) {
                    $product = Product::find($detail->product_id);
                    if ($product) {
                        $product->stock += $detail->quantity;

                        // Cập nhật giá nhập mới và tịnh tiến giá bán để giữ nguyên biên lợi nhuận
                        if ($detail->unit_price != $product->import_price) {
                            $margin = $product->sell_price - $product->import_price;
                            $product->import_price = $detail->unit_price; // unit_price của import detail là giá nhập mới
                            $product->sell_price = $product->import_price + $margin;
                        }

                        $product->save();

                        \App\Models\InventoryLog::create([
                            'product_id' => $product->id,
                            'type' => 'import',
                            'quantity' => $detail->quantity,
                            'created_by' => auth()->id() ?? 1,
                        ]);
                    }
                }
            }

            return $import;
        });
    }

    public function delete($id)
    {
        return DB::transaction(function () use ($id) {
            $import = Import::findOrFail($id);
            if ($import->status !== 'pending') {
                throw new Exception('Chỉ có thể xóa phiếu nhập khi đang ở trạng thái pending.');
            }
            return $import->delete();
        });
    }
}
