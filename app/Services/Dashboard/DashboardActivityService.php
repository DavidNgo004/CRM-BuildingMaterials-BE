<?php

namespace App\Services\Dashboard;

use App\Models\InventoryLog;

/**
 * DashboardActivityService
 *
 * Cung cấp lịch sử hoạt động gần nhất trên hệ thống.
 * Dữ liệu lấy từ bảng `inventory_logs`, bao gồm:
 * - Nhập kho (type = 'import')
 * - Xuất kho (type = 'export')
 *
 * Kết quả được dùng cho widget "Recent Activities" trên Dashboard.
 */
class DashboardActivityService
{
    /**
     * Lấy danh sách hoạt động gần nhất.
     *
     * @param int $limit  Số lượng bản ghi trả về (mặc định 15)
     * @return array
     */
    public function getRecentActivities(int $limit = 15): array
    {
        $logs = InventoryLog::with([
                'product:id,name,unit',   // Chỉ lấy các cột cần thiết của sản phẩm
                'creator:id,name',         // Nhân viên thực hiện hành động
            ])
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();

        return $logs->map(function ($log) {
            // Chuyển type sang động từ tiếng Việt
            $action = $log->type === 'export' ? 'xuất kho' : 'nhập kho';
            $qty    = abs($log->quantity); // quantity xuất kho lưu âm

            return [
                'id'          => $log->id,
                'type'        => $log->type,
                'description' => "{$log->creator?->name} vừa {$action} {$qty} {$log->product?->unit} {$log->product?->name}",
                'product'     => $log->product?->name,
                'unit'        => $log->product?->unit,
                'quantity'    => $qty,
                'user'        => $log->creator?->name,
                'time'        => $log->created_at->diffForHumans(),  // "5 phút trước"
                'created_at'  => $log->created_at,
            ];
        })->toArray();
    }
}
