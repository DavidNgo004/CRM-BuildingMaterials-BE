<?php

namespace App\Services\Dashboard;

use App\Models\Export;
use App\Models\ExportDetail;
use App\Models\Import;
use App\Models\Product;
use App\Models\Expense;
use Carbon\Carbon;

/**
 * DashboardKpiService
 *
 * Chịu trách nhiệm tính toán các chỉ số KPI tổng quan cho Dashboard:
 * - Doanh thu (Revenue)
 * - Giá vốn hàng bán (COGS)
 * - Chi phí vận hành (Expenses)
 * - Lợi nhuận ròng (Net Profit)
 * - Số lượng đơn xuất / nhập
 * - Số sản phẩm tồn kho thấp
 */
class DashboardKpiService
{
    /**
     * Lấy toàn bộ dữ liệu KPI cards.
     *
     * @param Carbon $from  Ngày bắt đầu của kỳ thống kê
     * @param Carbon $to    Ngày kết thúc của kỳ thống kê
     * @return array
     */
    public function getKpiCards(Carbon $from, Carbon $to): array
    {
        [$todayFrom, $todayTo] = [Carbon::today()->startOfDay(), Carbon::today()->endOfDay()];

        // --- Gộp query XUẤT (Doanh thu, số lượng đơn) ---
        $exportStats = Export::whereIn('status', ['approved', 'completed'])
            ->where(function ($q) use ($from, $to, $todayFrom, $todayTo) {
                $q->whereBetween('updated_at', [$from, $to])
                    ->orWhereBetween('updated_at', [$todayFrom, $todayTo]);
            })
            ->selectRaw("
                SUM(IF(updated_at BETWEEN '{$from}' AND '{$to}', grand_total, 0)) as revenue,
                SUM(IF(updated_at BETWEEN '{$from}' AND '{$to}', 1, 0)) as count,
                SUM(IF(updated_at BETWEEN '{$todayFrom}' AND '{$todayTo}', grand_total, 0)) as revenue_today,
                SUM(IF(updated_at BETWEEN '{$todayFrom}' AND '{$todayTo}', 1, 0)) as count_today
            ")
            ->first();

        $revenue = $exportStats->revenue ?? 0;
        $exportCount = $exportStats->count ?? 0;
        $revenueToday = $exportStats->revenue_today ?? 0;
        $exportCountToday = $exportStats->count_today ?? 0;

        // --- Gộp query NHẬP (số lượng đơn) ---
        $importStats = Import::where('status', 'completed')
            ->where(function ($q) use ($from, $to, $todayFrom, $todayTo) {
                $q->whereBetween('updated_at', [$from, $to])
                    ->orWhereBetween('updated_at', [$todayFrom, $todayTo]);
            })
            ->selectRaw("
                SUM(IF(updated_at BETWEEN '{$from}' AND '{$to}', 1, 0)) as count,
                SUM(IF(updated_at BETWEEN '{$todayFrom}' AND '{$todayTo}', 1, 0)) as count_today
            ")
            ->first();

        $importCount = $importStats->count ?? 0;
        $importCountToday = $importStats->count_today ?? 0;

        // --- Giá vốn: import_price * quantity của các sản phẩm trong phiếu xuất hoàn thành ---
        $cogs = ExportDetail::whereHas(
            'export',
            fn($q) =>
            $q->whereIn('status', ['approved', 'completed'])->whereBetween('updated_at', [$from, $to])
        )->selectRaw('SUM(import_price * quantity) as total_cogs')
            ->value('total_cogs') ?? 0;

        // --- Chi phí vận hành: tổng các khoản chi trong kỳ ---
        $expenses = Expense::whereBetween('expense_date', [$from, $to])->sum('amount');

        // --- Lợi nhuận ròng ---
        $profit = $revenue - $cogs - $expenses;

        // --- Số sản phẩm sắp hết hàng (stock <= reorder_level) ---
        $lowStockCount = Product::whereColumn('stock', '<=', 'reorder_level')->count();

        return [
            'revenue' => (int) $revenue,
            'revenue_today' => (int) $revenueToday,
            'cogs' => (int) $cogs,
            'expenses' => (int) $expenses,
            'profit' => (int) $profit,
            'export_count' => $exportCount,
            'export_count_today' => $exportCountToday,
            'import_count' => $importCount,
            'import_count_today' => $importCountToday,
            'low_stock_count' => $lowStockCount,
        ];
    }
}
