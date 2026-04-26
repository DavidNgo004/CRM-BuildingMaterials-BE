<?php

namespace App\Services;

use App\Models\InventoryLog;
use Carbon\Carbon;

class InventoryLogService
{
    /**
     * Lấy danh sách lịch sử tồn kho có phân trang.
     *
     * @param int         $perPage    Số bản ghi mỗi trang
     * @param int|null    $productId  Lọc theo sản phẩm cụ thể
     * @param string|null $type       Lọc theo loại: 'import' | 'export'
     * @param string|null $fromDate   Lọc từ ngày (YYYY-MM-DD)
     * @param string|null $toDate     Lọc đến ngày (YYYY-MM-DD)
     */
    public function paginate(int $perPage = 20, ?int $productId = null, ?string $type = null, ?string $fromDate = null, ?string $toDate = null)
    {
        $query = InventoryLog::with([
            'product:id,name,unit',
            'creator:id,name',
        ])->orderByDesc('created_at');

        if ($productId) {
            $query->where('product_id', $productId);
        }

        if ($type && in_array($type, ['import', 'export'])) {
            $query->where('type', $type);
        }

        if ($fromDate) {
            $query->where('created_at', '>=', Carbon::parse($fromDate)->startOfDay());
        }

        if ($toDate) {
            $query->where('created_at', '<=', Carbon::parse($toDate)->endOfDay());
        }

        return $query->paginate($perPage);
    }
}
