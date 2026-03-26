<?php

namespace App\Services;

use App\Services\Dashboard\DashboardKpiService;
use App\Services\Dashboard\DashboardChartService;
use App\Services\Dashboard\DashboardActivityService;
use App\Services\Dashboard\DashboardReportService;
use App\Services\Dashboard\DashboardAlertService;
use Carbon\Carbon;

/**
 * DashboardService  (Orchestrator / Facade)
 *
 * Lớp này không chứa logic trực tiếp – nó đóng vai trò điều phối (Facade)
 * và uỷ quyền mọi xử lý xuống các sub-service chuyên biệt:
 *
 * ┌─────────────────────────┐
 * │     DashboardService     │  ← Controller gọi vào đây
 * └────────────┬────────────┘
 *              │ delegates to:
 *   ┌──────────┼──────────────────────────────────────┐
 *   │          │                    │                  │
 *   ▼          ▼                    ▼                  ▼
 * KpiService  ChartService  ActivityService  ReportService
 *                                                      ▲
 *                                               AlertService (AI)
 *
 * Lợi ích:
 * - Controller giữ gọn, không biết cách tính toán
 * - Mỗi sub-service dễ test, dễ bảo trì, dễ mở rộng thêm AI/ML
 * - Thêm tính năng mới không phá vỡ code cũ (Open/Closed Principle)
 */
class DashboardService
{
    public function __construct(
        protected DashboardKpiService      $kpiService,
        protected DashboardChartService    $chartService,
        protected DashboardActivityService $activityService,
        protected DashboardReportService   $reportService,
        protected DashboardAlertService    $alertService,
    ) {}

    /**
     * Giải mã tham số filter thành khoảng ngày [Carbon $from, Carbon $to].
     *
     * Nhận diện:
     * - today / this_week / this_month / this_year
     * - custom: start_date + end_date (format YYYY-MM-DD)
     *
     * @return array{Carbon, Carbon}
     */
    public function resolveDateRange(?string $period, ?string $startDate, ?string $endDate): array
    {
        // Ưu tiên custom range nếu cả hai ngày được truyền rõ ràng
        if ($startDate && $endDate) {
            return [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay(),
            ];
        }

        return match ($period) {
            'today'     => [Carbon::today()->startOfDay(),       Carbon::today()->endOfDay()],
            'this_week' => [Carbon::now()->startOfWeek(),        Carbon::now()->endOfWeek()],
            'this_year' => [Carbon::now()->startOfYear(),        Carbon::now()->endOfYear()],
            default     => [Carbon::now()->startOfMonth(),       Carbon::now()->endOfMonth()],  // this_month
        };
    }

    // -----------------------------------------------------------------------
    // Các phương thức public dưới đây là điểm gọi duy nhất từ Controller
    // -----------------------------------------------------------------------

    /** Uỷ quyền cho DashboardKpiService */
    public function getKpiCards(?string $period, ?string $start, ?string $end): array
    {
        [$from, $to] = $this->resolveDateRange($period, $start, $end);
        return $this->kpiService->getKpiCards($from, $to);
    }

    /** Uỷ quyền cho DashboardChartService */
    public function getCharts(?string $period, ?string $start, ?string $end): array
    {
        [$from, $to] = $this->resolveDateRange($period, $start, $end);
        return $this->chartService->getCharts($from, $to);
    }

    /** Uỷ quyền cho DashboardActivityService */
    public function getRecentActivities(int $limit): array
    {
        return $this->activityService->getRecentActivities($limit);
    }

    /** Uỷ quyền cho DashboardAlertService (AI Module) */
    public function getAlerts(): array
    {
        return $this->alertService->getAlerts();
    }

    /** Uỷ quyền cho DashboardReportService */
    public function getMiniReports(?string $period, ?string $start, ?string $end): array
    {
        [$from, $to] = $this->resolveDateRange($period, $start, $end);
        return $this->reportService->getMiniReports($from, $to);
    }
}
