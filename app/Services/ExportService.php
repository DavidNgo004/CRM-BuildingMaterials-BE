<?php

namespace App\Services;

use App\Repositories\ExportRepository;
use App\Models\Export;
use App\Models\ExportDetail;
use App\Models\Product;
use App\Mail\ExportApprovedMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Exception;

class ExportService
{
    protected $exportRepository;

    public function __construct(ExportRepository $exportRepository)
    {
        $this->exportRepository = $exportRepository;
    }

    public function paginate($perPage = 15, $search = null)
    {
        return $this->exportRepository->paginate($perPage, $search);
    }

    public function find($id)
    {
        return $this->exportRepository->find($id);
    }

    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            $total_price = 0;

            $code = 'EX' . date('YmdHis') . rand(10, 99);

            $customerId = $data['customer_id'] ?? null;
            if (!$customerId) {
                $customer = \App\Models\Customer::firstOrCreate(
                    ['phone' => $data['customer_phone']],
                    [
                        'name' => $data['customer_name'],
                        'email' => $data['customer_email'] ?? null,
                        'address' => $data['customer_address'] ?? null,
                    ]
                );
                $customerId = $customer->id;
            }

            foreach ($data['details'] as $detail) {
                $product = Product::findOrFail($detail['product_id']);
                if ($product->stock < $detail['quantity']) {
                    throw new Exception("Sản phẩm {$product->name} không đủ tồn kho (Còn: {$product->stock}).");
                }
                $total_price += ($product->sell_price * $detail['quantity']);
            }

            $discount = $data['discount_amount'] ?? 0;
            $grand_total = max(0, $total_price - $discount);

            $export = Export::create([
                'code' => $code,
                'user_id' => auth()->id() ?? 1,
                'customer_id' => $customerId,
                'total_price' => $total_price,
                'discount_amount' => $discount,
                'grand_total' => $grand_total,
                'status' => 'pending',
                'note' => $data['note'] ?? null,
            ]);

            foreach ($data['details'] as $detail) {
                $product = Product::findOrFail($detail['product_id']);
                ExportDetail::create([
                    'export_id' => $export->id,
                    'product_id' => $detail['product_id'],
                    'quantity' => $detail['quantity'],
                    'unit_price' => $product->sell_price,
                    'import_price' => $product->import_price,
                    'total_price' => $product->sell_price * $detail['quantity'],
                ]);
            }

            return $export->load('details.product', 'customer');
        });
    }

    public function changeStatus($id, $status)
    {
        return DB::transaction(function () use ($id, $status) {
            $export = Export::with('details.product', 'customer')->findOrFail($id);

            if ($export->status === 'completed' || $export->status === 'cancelled') {
                throw new Exception("Phiếu xuất đang ở trạng thái {$export->status} nên không thể thay đổi.");
            }

            $oldStatus = $export->status;
            $export->status = $status;
            $export->save();

            // Tự động gửi Email khi trạng thái chuyển qua 'approved'
            if ($status === 'approved' && $oldStatus === 'pending') {
                $customer = $export->customer;
                if (!empty($customer->email)) {
                    try {
                        Mail::to($customer->email)->send(new ExportApprovedMail($export));
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error('Mail Error: ' . $e->getMessage());
                    }
                }
            }

            // Khi hàng đã giao -> Cập nhật Tồn kho (approved)
            if ($status === 'approved') {
                foreach ($export->details as $detail) {
                    $product = Product::find($detail->product_id);
                    if ($product) {
                        if ($product->stock < $detail->quantity) {
                            throw new Exception("Sản phẩm {$product->name} không đủ tồn kho để xuất.");
                        }
                        $product->stock -= $detail->quantity;
                        $product->save();

                        \App\Models\InventoryLog::create([
                            'product_id' => $product->id,
                            'type' => 'export',
                            'quantity' => -$detail->quantity,
                            'created_by' => auth()->id() ?? 1,
                        ]);
                    }
                }
            }

            return $export;
        });
    }

    public function delete($id)
    {
        return DB::transaction(function () use ($id) {
            $export = Export::findOrFail($id);
            if ($export->status !== 'pending') {
                throw new Exception('Chỉ có thể xóa phiếu xuất khi đang ở trạng thái pending.');
            }
            return $export->delete();
        });
    }
}
