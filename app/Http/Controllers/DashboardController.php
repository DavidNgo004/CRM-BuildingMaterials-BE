<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

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
        $period = $request->input('period', 'this_month');
        $start  = $request->input('start_date');
        $end    = $request->input('end_date');
        
        $key = "dashboard:kpi_cards:{$period}:{$start}:{$end}";
        
        $data = Cache::remember($key, 300, fn() => $this->dashboardService->getKpiCards($period, $start, $end));
        return response()->json($data);
    }

    /**
     * Charts: Doanh thu, Lợi nhuận, Top sản phẩm, Tỷ trọng, Tồn kho.
     */
    public function charts(Request $request)
    {
        $period = $request->input('period', 'this_month');
        $start  = $request->input('start_date');
        $end    = $request->input('end_date');
        
        $key = "dashboard:charts:{$period}:{$start}:{$end}";
        
        $data = Cache::remember($key, 300, fn() => $this->dashboardService->getCharts($period, $start, $end));
        return response()->json($data);
    }

    /**
     * Recent Activities: Lịch sử hoạt động từ inventory_logs.
     */
    public function recentActivities(Request $request)
    {
        $limit = min((int) $request->input('limit', 15), 50); // tối đa 50
        $key   = "dashboard:recent_activities:{$limit}";
        
        $data  = Cache::remember($key, 300, fn() => $this->dashboardService->getRecentActivities($limit));
        return response()->json($data);
    }

    /**
     * Alerts & AI Suggestions: Cảnh báo tồn kho + Gợi ý nhập hàng.
     */
    public function alerts()
    {
        $key  = "dashboard:alerts";
        $data = Cache::remember($key, 300, fn() => $this->dashboardService->getAlerts());
        return response()->json($data);
    }

    /**
     * Mini Reports: Top Khách hàng & Top Nhà cung cấp.
     */
    public function miniReports(Request $request)
    {
        $period = $request->input('period', 'this_month');
        $start  = $request->input('start_date');
        $end    = $request->input('end_date');
        
        $key = "dashboard:mini_reports:{$period}:{$start}:{$end}";
        
        $data = Cache::remember($key, 300, fn() => $this->dashboardService->getMiniReports($period, $start, $end));
        return response()->json($data);
    }

    /**
     * Get all dashboard data in a single request to avoid concurrent request bottlenecks
     */
    public function summary(Request $request)
    {
        $period = $request->input('period', 'this_month');
        $start  = $request->input('start_date');
        $end    = $request->input('end_date');
        
        $key = "dashboard:summary:{$period}:{$start}:{$end}";
        
        $data = Cache::remember($key, 300, function() use ($period, $start, $end) {
            return [
                'kpi' => $this->dashboardService->getKpiCards($period, $start, $end),
                'charts' => $this->dashboardService->getCharts($period, $start, $end),
                'activities' => $this->dashboardService->getRecentActivities(15),
                'alerts' => $this->dashboardService->getAlerts(),
                'miniReports' => $this->dashboardService->getMiniReports($period, $start, $end),
            ];
        });
        
        return response()->json($data);
    }
}
