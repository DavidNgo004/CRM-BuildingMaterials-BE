<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLogService
{
    /**
     * Ghi một bản ghi activity log.
     *
     * @param  string      $action       Hằng số action (VD: ActivityLog::CREATE_IMPORT)
     * @param  string      $entityType   Loại đối tượng: product | import | export | expense | …
     * @param  int|null    $entityId     ID đối tượng
     * @param  array|null  $oldData      Dữ liệu TRƯỚC khi thay đổi
     * @param  array|null  $newData      Dữ liệu SAU khi thay đổi
     * @param  string|null $description  Mô tả ngắn gọn (tự sinh nếu null)
     */
    public static function log(
        string  $action,
        string  $entityType,
        ?int    $entityId   = null,
        ?array  $oldData    = null,
        ?array  $newData    = null,
        ?string $description = null
    ): ActivityLog {
        $user = Auth::user();

        return ActivityLog::create([
            'user_id'     => $user?->id,
            'user_name'   => $user?->name,
            'user_role'   => $user?->role,
            'action'      => $action,
            'entity_type' => $entityType,
            'entity_id'   => $entityId,
            'old_data'    => $oldData,
            'new_data'    => $newData,
            'description' => $description ?? self::buildDescription($action, $entityType, $entityId),
            'ip_address'  => Request::ip(),
            'created_at'  => now(),
        ]);
    }

    /**
     * Tự sinh mô tả ngắn gọn dựa trên action.
     */
    private static function buildDescription(string $action, string $entityType, ?int $entityId): string
    {
        $map = [
            ActivityLog::CREATE_IMPORT   => "Tạo phiếu nhập #{$entityId}",
            ActivityLog::UPDATE_IMPORT   => "Cập nhật phiếu nhập #{$entityId}",
            ActivityLog::APPROVE_IMPORT  => "Duyệt phiếu nhập #{$entityId}",
            ActivityLog::COMPLETE_IMPORT => "Hoàn thành phiếu nhập #{$entityId}",
            ActivityLog::CANCEL_IMPORT   => "Hủy phiếu nhập #{$entityId}",
            ActivityLog::DELETE_IMPORT   => "Xóa phiếu nhập #{$entityId}",

            ActivityLog::CREATE_EXPORT  => "Tạo phiếu xuất #{$entityId}",
            ActivityLog::APPROVE_EXPORT => "Duyệt phiếu xuất #{$entityId}",
            ActivityLog::CANCEL_EXPORT  => "Hủy phiếu xuất #{$entityId}",
            ActivityLog::DELETE_EXPORT  => "Xóa phiếu xuất #{$entityId}",

            ActivityLog::CREATE_PRODUCT => "Thêm sản phẩm #{$entityId}",
            ActivityLog::UPDATE_PRODUCT => "Cập nhật sản phẩm #{$entityId}",
            ActivityLog::DELETE_PRODUCT => "Xóa sản phẩm #{$entityId}",

            ActivityLog::CREATE_EXPENSE => "Thêm chi phí #{$entityId}",
            ActivityLog::UPDATE_EXPENSE => "Cập nhật chi phí #{$entityId}",
            ActivityLog::DELETE_EXPENSE => "Xóa chi phí #{$entityId}",
        ];

        return $map[$action] ?? ucfirst(strtolower(str_replace('_', ' ', $action))) . " {$entityType} #{$entityId}";
    }

    /**
     * Lấy danh sách log có phân trang và lọc.
     */
    public static function paginate(array $filters = [], int $perPage = 20)
    {
        $query = ActivityLog::with('user')
            ->orderBy('created_at', 'desc');

        if (!empty($filters['action'])) {
            $query->where('action', $filters['action']);
        }

        if (!empty($filters['entity_type'])) {
            $query->where('entity_type', $filters['entity_type']);
        }

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['from'])) {
            $query->whereDate('created_at', '>=', $filters['from']);
        }

        if (!empty($filters['to'])) {
            $query->whereDate('created_at', '<=', $filters['to']);
        }

        if (!empty($filters['search'])) {
            $keyword = $filters['search'];
            $query->where(function ($q) use ($keyword) {
                $q->where('description', 'like', "%{$keyword}%")
                  ->orWhere('user_name',  'like', "%{$keyword}%")
                  ->orWhere('action',     'like', "%{$keyword}%");
            });
        }

        return $query->paginate($perPage);
    }
}
