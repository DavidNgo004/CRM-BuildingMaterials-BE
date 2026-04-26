<?php

namespace App\Http\Controllers;

use App\Services\InventoryLogService;
use Illuminate\Http\Request;

class InventoryLogController extends Controller
{
    public function __construct(protected InventoryLogService $inventoryLogService) {}

    /**
     * GET /inventory-logs
     * Lấy danh sách lịch sử biến động tồn kho (có thể lọc theo sản phẩm và loại).
     */
    public function index(Request $request)
    {
        $perPage    = (int) $request->get('per_page', 20);
        $productId  = $request->get('product_id');
        $type       = $request->get('type');   // 'import' | 'export'
        $fromDate   = $request->get('from_date');
        $toDate     = $request->get('to_date');

        $logs = $this->inventoryLogService->paginate($perPage, $productId, $type, $fromDate, $toDate);
        return response()->json($logs);
    }
}
