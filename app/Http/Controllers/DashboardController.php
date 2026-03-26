<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\Request;

/**
 * DashboardController
 *
 * Điều hướng các HTTP Request xuống DashboardService (Orchestrator).
 * Controller không chứa logic nghiệp vụ hay SQL trực tiếp.
 *
 * Tất cả endpoints đều nhận các query params filter:
 *   ?period=today|this_week|this_month|this_year
 *   ?start_date=YYYY-MM-DD&end_date=YYYY-MM-DD
 */
class DashboardController extends Controller
{
    public function __construct(protected DashboardService $dashboardService) {}

    /**
     * KPI Cards: Doanh thu, Giá vốn, Lợi nhuận, Số đơn, Tồn kho thấp.
     */
    public function kpiCards(Request $request)
    {
        $data = $this->dashboardService->getKpiCards(
            $request->input('period', 'this_month'),
            $request->input('start_date'),
            $request->input('end_date')
        );
        return response()->json($data);
    }

    /**
     * Charts: Doanh thu, Lợi nhuận, Top sản phẩm, Tỷ trọng, Tồn kho.
     */
    public function charts(Request $request)
    {
        $data = $this->dashboardService->getCharts(
            $request->input('period', 'this_month'),
            $request->input('start_date'),
            $request->input('end_date')
        );
        return response()->json($data);
    }

    /**
     * Recent Activities: Lịch sử hoạt động từ inventory_logs.
     */
    public function recentActivities(Request $request)
    {
        $limit = min((int) $request->input('limit', 15), 50); // tối đa 50
        $data  = $this->dashboardService->getRecentActivities($limit);
        return response()->json($data);
    }

    /**
     * Alerts & AI Suggestions: Cảnh báo tồn kho + Gợi ý nhập hàng.
     */
    public function alerts()
    {
        $data = $this->dashboardService->getAlerts();
        return response()->json($data);
    }

    /**
     * Mini Reports: Top Khách hàng & Top Nhà cung cấp.
     */
    public function miniReports(Request $request)
    {
        $data = $this->dashboardService->getMiniReports(
            $request->input('period', 'this_month'),
            $request->input('start_date'),
            $request->input('end_date')
        );
        return response()->json($data);
    }
}
