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

        // --- Doanh thu: tổng grand_total các phiếu XUẤT đã hoàn thành trong kỳ ---
        $revenue = Export::where('status', 'completed')
            ->whereBetween('updated_at', [$from, $to])
            ->sum('grand_total');

        // --- Giá vốn: import_price * quantity của các sản phẩm trong phiếu xuất hoàn thành ---
        $cogs = ExportDetail::whereHas('export', fn ($q) =>
            $q->where('status', 'completed')->whereBetween('updated_at', [$from, $to])
        )->selectRaw('SUM(import_price * quantity) as total_cogs')
         ->value('total_cogs') ?? 0;

        // --- Chi phí vận hành: tổng các khoản chi trong kỳ ---
        $expenses = Expense::whereBetween('expense_date', [$from, $to])->sum('amount');

        // --- Lợi nhuận ròng ---
        $profit = $revenue - $cogs - $expenses;

        // --- Doanh thu hôm nay ---
        $revenueToday = Export::where('status', 'completed')
            ->whereBetween('updated_at', [$todayFrom, $todayTo])
            ->sum('grand_total');

        // --- Số đơn xuất / nhập hoàn thành trong kỳ và hôm nay ---
        $exportCount      = Export::where('status', 'completed')->whereBetween('updated_at', [$from, $to])->count();
        $importCount      = Import::where('status', 'completed')->whereBetween('updated_at', [$from, $to])->count();
        $exportCountToday = Export::where('status', 'completed')->whereBetween('updated_at', [$todayFrom, $todayTo])->count();
        $importCountToday = Import::where('status', 'completed')->whereBetween('updated_at', [$todayFrom, $todayTo])->count();

        // --- Số sản phẩm sắp hết hàng (stock <= reorder_level) ---
        $lowStockCount = Product::whereColumn('stock', '<=', 'reorder_level')->count();

        return [
            'revenue'            => (int) $revenue,
            'revenue_today'      => (int) $revenueToday,
            'cogs'               => (int) $cogs,
            'expenses'           => (int) $expenses,
            'profit'             => (int) $profit,
            'export_count'       => $exportCount,
            'export_count_today' => $exportCountToday,
            'import_count'       => $importCount,
            'import_count_today' => $importCountToday,
            'low_stock_count'    => $lowStockCount,
        ];
    }
}
