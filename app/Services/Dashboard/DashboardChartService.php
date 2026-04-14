<?php

namespace App\Services\Dashboard;

use App\Models\ExportDetail;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * DashboardChartService
 *
 * Xử lý logic tổng hợp dữ liệu cho tất cả các biểu đồ trên Dashboard:
 * - Biểu đồ doanh thu theo ngày/tháng (Line Chart)
 * - Biểu đồ lợi nhuận theo ngày/tháng (Line Chart)
 * - Top 5 sản phẩm bán chạy nhất (Bar Chart)
 * - Tỷ trọng doanh thu theo sản phẩm (Pie Chart)
 * - Phân loại trạng thái tồn kho (Donut Chart)
 */
class DashboardChartService
{
    /**
     * Lấy toàn bộ dữ liệu cho các biểu đồ.
     *
     * @param Carbon $from  Ngày bắt đầu
     * @param Carbon $to    Ngày kết thúc
     * @return array
     */
    public function getCharts(Carbon $from, Carbon $to): array
    {
        return [
            'revenue_chart'       => $this->revenueChart($from, $to),
            'profit_chart'        => $this->profitChart($from, $to),
            'top_products'        => $this->topProducts($from, $to),
            'revenue_by_product'  => $this->revenueByProduct($from, $to),
            'inventory_breakdown' => $this->inventoryBreakdown(),
        ];
    }

    /**
     * Biểu đồ doanh thu theo ngày (≤31 ngày) hoặc theo tháng (>31 ngày). 
     * lấy trạng thái "approved" và "completed" để phản ánh doanh thu thực tế đã ghi nhận và trừ trạng thái "cancelled".
     */
    private function revenueChart(Carbon $from, Carbon $to): array
    {
        [$group, $label] = $this->resolveGrouping($from, $to);

        $revenueData = DB::table('exports')
            ->selectRaw("DATE_FORMAT(updated_at, '{$group}') as {$label}, SUM(grand_total) as revenue")
            ->whereIn('status', ['approved', 'completed'])
            ->whereBetween('updated_at', [$from, $to])
            ->groupBy($label)
            ->pluck('revenue', $label)
            ->toArray();

        $cogsData = DB::table('export_details')
            ->join('exports', 'exports.id', '=', 'export_details.export_id')
            ->selectRaw("DATE_FORMAT(exports.updated_at, '{$group}') as {$label}, SUM(export_details.import_price * export_details.quantity) as cogs")
            ->whereIn('exports.status', ['approved', 'completed'])
            ->whereBetween('exports.updated_at', [$from, $to])
            ->groupBy($label)
            ->pluck('cogs', $label)
            ->toArray();

        $expenseData = DB::table('expenses')
            ->selectRaw("DATE_FORMAT(expense_date, '{$group}') as {$label}, SUM(amount) as expense")
            ->whereBetween('expense_date', [$from, $to])
            ->whereNull('deleted_at')
            ->groupBy($label)
            ->pluck('expense', $label)
            ->toArray();

        $allDates = array_unique(array_merge(
            array_keys($revenueData),
            array_keys($expenseData)
        ));
        sort($allDates);

        $result = [];
        foreach ($allDates as $date) {
            $rev = $revenueData[$date] ?? 0;
            $cogs = $cogsData[$date] ?? 0;
            $exp = $expenseData[$date] ?? 0;
            $net_profit = $rev - $cogs - $exp;

            $result[] = [
                $label       => $date,
                'revenue'    => (float) $rev,
                'expense'    => (float) $exp,
                'net_profit' => (float) $net_profit,
            ];
        }

        return $result;
    }

    /**
     * Biểu đồ lợi nhuận gộp (Revenue - COGS) theo ngày/tháng.
     * (Chi phí vận hành phân bổ đều theo kỳ để giữ đơn giản)
     * lấy trạng thái "approved" và "completed" để phản ánh doanh thu thực tế đã ghi nhận và trừ trạng thái "cancelled".
     */
    private function profitChart(Carbon $from, Carbon $to): array
    {
        [$group, $label] = $this->resolveGrouping($from, $to);

        $revenueData = DB::table('exports')
            ->selectRaw("DATE_FORMAT(updated_at, '{$group}') as {$label}, SUM(grand_total) as revenue")
            ->whereIn('status', ['approved', 'completed'])
            ->whereBetween('updated_at', [$from, $to])
            ->groupBy($label)
            ->pluck('revenue', $label)
            ->toArray();

        $cogsData = DB::table('export_details')
            ->join('exports', 'exports.id', '=', 'export_details.export_id')
            ->selectRaw("DATE_FORMAT(exports.updated_at, '{$group}') as {$label}, SUM(export_details.import_price * export_details.quantity) as cogs")
            ->whereIn('exports.status', ['approved', 'completed'])
            ->whereBetween('exports.updated_at', [$from, $to])
            ->groupBy($label)
            ->pluck('cogs', $label)
            ->toArray();

        $allDates = array_unique(array_merge(
            array_keys($revenueData),
            array_keys($cogsData)
        ));
        sort($allDates);

        $result = [];
        foreach ($allDates as $date) {
            $rev = $revenueData[$date] ?? 0;
            $cogs = $cogsData[$date] ?? 0;
            $gross_profit = $rev - $cogs;

            $result[] = [
                $label         => $date,
                'revenue'      => (float) $rev,
                'cogs'         => (float) $cogs,
                'gross_profit' => (float) $gross_profit,
            ];
        }

        return $result;
    }

    /**
     * Top 5 sản phẩm bán chạy nhất theo số lượng trong kỳ.
     */
    private function topProducts(Carbon $from, Carbon $to): array
    {
        return ExportDetail::select(
                'product_id',
                DB::raw('SUM(quantity) as total_qty'),
                DB::raw('SUM(total_price) as total_revenue')
            )
            ->whereHas('export', fn ($q) =>
                $q->where('status', 'completed')->whereBetween('updated_at', [$from, $to])
            )
            ->with('product:id,name,unit')
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get()
            ->map(fn ($item) => [
                'product_id'    => $item->product_id,
                'name'          => $item->product?->name,
                'unit'          => $item->product?->unit,
                'total_qty'     => (int) $item->total_qty,
                'total_revenue' => (int) $item->total_revenue,
            ])
            ->toArray();
    }

    /**
     * Tỷ trọng doanh thu từng sản phẩm (%) trong Top 5.
     * Dùng cho Pie Chart.
     */
    private function revenueByProduct(Carbon $from, Carbon $to): array
    {
        $topProducts  = $this->topProducts($from, $to);
        $grandTotal   = array_sum(array_column($topProducts, 'total_revenue')) ?: 1;

        return array_map(fn ($p) => [
            ...$p,
            'percentage' => round($p['total_revenue'] / $grandTotal * 100, 1),
        ], $topProducts);
    }

    /**
     * Phân loại trạng thái tồn kho toàn bộ sản phẩm.
     * - out_of_stock: stock = 0
     * - low_stock   : 0 < stock <= reorder_level
     * - normal      : reorder_level < stock <= 3× reorder_level
     * - overstock   : stock > 3× reorder_level
     */
    private function inventoryBreakdown(): array
    {
        return [
            'out_of_stock' => Product::where('stock', 0)->count(),
            'low_stock'    => Product::where('stock', '>', 0)->whereColumn('stock', '<=', 'reorder_level')->count(),
            'normal'       => Product::whereColumn('stock', '>', 'reorder_level')
                                ->whereRaw('stock <= reorder_level * 3')->count(),
            'overstock'    => Product::whereRaw('stock > reorder_level * 3')->count(),
        ];
    }

    /**
     * Xác định format nhóm SQL và tên label dựa trên số ngày trong khoảng.
     * ≤31 ngày → nhóm theo ngày;  >31 ngày → nhóm theo tháng.
     *
     * @return array [$sqlFormat, $labelAlias]
     */
    private function resolveGrouping(Carbon $from, Carbon $to): array
    {
        return $from->diffInDays($to) <= 31
            ? ['%Y-%m-%d', 'date']
            : ['%Y-%m',    'month'];
    }
}
