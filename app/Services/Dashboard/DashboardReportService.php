<?php

namespace App\Services\Dashboard;

use App\Models\Export;
use App\Models\ExportDetail;
use App\Models\Import;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * DashboardReportService
 *
 * Cung cấp dữ liệu "Mini Reports" – bảng xếp hạng nhanh cho Dashboard:
 * - Top Khách hàng: ai mua nhiều tiền nhất
 * - Top Nhà cung cấp: nhà cung cấp hàng nhiều nhất
 *
 * Dữ liệu được lọc theo kỳ thống kê (from/to).
 */
class DashboardReportService
{
    /**
     * Lấy toàn bộ dữ liệu Mini Reports.
     *
     * @param Carbon $from
     * @param Carbon $to
     * @return array
     */
    public function getMiniReports(Carbon $from, Carbon $to): array
    {
        return [
            'top_customers' => $this->getTopCustomers($from, $to),
            'top_suppliers' => $this->getTopSuppliers($from, $to),
        ];
    }

    /**
     * Top 5 khách hàng theo tổng giá trị mua hàng trong kỳ.
     */
    private function getTopCustomers(Carbon $from, Carbon $to): array
    {
        return Export::select(
                'customer_id',
                DB::raw('SUM(grand_total) as total_purchase'),
                DB::raw('COUNT(*) as order_count')
            )
            ->where('status', 'completed')
            ->whereBetween('updated_at', [$from, $to])
            ->with('customer:id,name,phone,code')
            ->groupBy('customer_id')
            ->orderByDesc('total_purchase')
            ->limit(5)
            ->get()
            ->map(fn ($item) => [
                'customer_id'    => $item->customer_id,
                'name'           => $item->customer?->name,
                'code'           => $item->customer?->code,
                'phone'          => $item->customer?->phone,
                'total_purchase' => (int) $item->total_purchase,
                'order_count'    => $item->order_count,
            ])
            ->toArray();
    }

    /**
     * Top 5 nhà cung cấp theo tổng giá trị hàng đã nhập hoàn thành trong kỳ.
     * Join qua bảng import_details vì mỗi phiếu nhập có thể có nhiều nhà cung cấp.
     */
    private function getTopSuppliers(Carbon $from, Carbon $to): array
    {
        return DB::table('import_details')
            ->join('imports',   'imports.id',   '=', 'import_details.import_id')
            ->join('products',  'products.id',  '=', 'import_details.product_id')
            ->join('suppliers', 'suppliers.id', '=', 'products.supplier_id')
            ->where('imports.status', 'completed')
            ->whereBetween('imports.updated_at', [$from, $to])
            ->select(
                'suppliers.id as supplier_id',
                'suppliers.name',
                'suppliers.code',
                'suppliers.phone',
                DB::raw('SUM(import_details.unit_price * import_details.quantity) as total_value'),
                DB::raw('COUNT(DISTINCT imports.id) as order_count')
            )
            ->groupBy('suppliers.id', 'suppliers.name', 'suppliers.code', 'suppliers.phone')
            ->orderByDesc('total_value')
            ->limit(5)
            ->get()
            ->toArray();
    }
}
