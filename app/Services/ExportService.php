<?php

namespace App\Services;

use App\Repositories\ExportRepository;
use App\Models\Export;
use App\Models\ExportDetail;
use App\Models\Product;
use App\Models\ActivityLog;
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
                
                if (!$product->status) {
                    throw new Exception("Sản phẩm '{$product->name}' đã ngừng kinh doanh, không thể xuất kho.");
                }

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

            // ── Activity Log ──────────────────────────────────────────────
            ActivityLogService::log(
                ActivityLog::CREATE_EXPORT,
                'export',
                $export->id,
                null,
                [
                    'code'        => $export->code,
                    'grand_total' => $export->grand_total,
                    'status'      => $export->status,
                    'items'       => count($data['details']),
                ]
            );

            return $export->load('details.product', 'customer');
        });
    }

    public function changeStatus($id, $status, $cancel_reason = null)
    {
        return DB::transaction(function () use ($id, $status, $cancel_reason) {
            $export = Export::with('details.product', 'customer')->findOrFail($id);

            if ($export->status === 'completed' || $export->status === 'cancelled') {
                throw new Exception("Phiếu xuất đang ở trạng thái {$export->status} nên không thể thay đổi.");
            }

            $oldStatus = $export->status;
            $export->status = $status;
            if ($status === 'cancelled' && $cancel_reason) {
                $export->cancel_reason = $cancel_reason;
            }
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

            // Khi duyệt đơn (approved) -> Trừ tồn kho
            if ($status === 'approved') {
                foreach ($export->details as $detail) {
                    $product = Product::find($detail->product_id);
                    if ($product) {
                        if ($product->stock < $detail->quantity) {
                            throw new Exception("Sản phẩm {$product->name} không đủ tồn kho để xuất (Còn: {$product->stock}).");
                        }
                        $product->stock -= $detail->quantity;
                        $product->save();

                        \App\Models\InventoryLog::create([
                            'product_id' => $product->id,
                            'type'       => 'export',
                            'quantity'   => -$detail->quantity,
                            'created_by' => auth()->id() ?? 1,
                        ]);
                    }
                }
            }

            // Khi hủy đơn (cancelled) từ trạng thái approved -> Hoàn lại tồn kho
            if ($status === 'cancelled' && $oldStatus === 'approved') {
                foreach ($export->details as $detail) {
                    $product = Product::find($detail->product_id);
                    if ($product) {
                        $product->stock += $detail->quantity;
                        $product->save();

                        \App\Models\InventoryLog::create([
                            'product_id' => $product->id,
                            'type'       => 'cancel_export',
                            'quantity'   => $detail->quantity,
                            'created_by' => auth()->id() ?? 1,
                        ]);
                    }
                }
            }

            // ── Activity Log theo trạng thái ─────────────────────────────
            $actionMap = [
                'approved'  => ActivityLog::APPROVE_EXPORT,
                'cancelled' => ActivityLog::CANCEL_EXPORT,
            ];
            if (isset($actionMap[$status])) {
                ActivityLogService::log(
                    $actionMap[$status],
                    'export',
                    $export->id,
                    ['status' => $oldStatus],
                    ['status' => $status]
                );
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
            $result = $export->delete();

            // ── Activity Log ──────────────────────────────────────────────
            ActivityLogService::log(
                ActivityLog::DELETE_EXPORT,
                'export',
                $id,
                ['id' => $id],
                null
            );

            return $result;
        });
    }
}
