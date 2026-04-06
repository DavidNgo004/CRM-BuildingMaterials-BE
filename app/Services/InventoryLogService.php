<?php

namespace App\Services;

use App\Models\InventoryLog;

class InventoryLogService
{
    /**
     * Lấy danh sách lịch sử tồn kho có phân trang.
     *
     * @param int         $perPage    Số bản ghi mỗi trang
     * @param int|null    $productId  Lọc theo sản phẩm cụ thể
     * @param string|null $type       Lọc theo loại: 'import' | 'export'
     */
    public function paginate(int $perPage = 20, ?int $productId = null, ?string $type = null)
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

        return $query->paginate($perPage);
    }
}
