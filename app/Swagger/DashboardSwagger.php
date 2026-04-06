<?php

namespace App\Swagger;

use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Dashboard",
 *     description="Dashboard thống kê tổng quan - KPI, Biểu đồ, Cảnh báo, AI Gợi ý"
 * )
 */
class DashboardSwagger
{
    /**
     * @OA\Get(
     *     path="/api/dashboard/kpi-cards",
     *     tags={"Dashboard"},
     *     security={{"bearerAuth":{}}},
     *     summary="Lấy dữ liệu KPI Cards (Doanh thu, Lợi nhuận, Đơn hàng, Tồn kho thấp)",
     *     description="Hỗ trợ filter theo: today | this_week | this_month | this_year | custom (start_date + end_date)",
     *     @OA\Parameter(name="period", in="query", description="Khoảng thời gian (today, this_week, this_month, this_year)", required=false, @OA\Schema(type="string", enum={"today","this_week","this_month","this_year"})),
     *     @OA\Parameter(name="start_date", in="query", description="Ngày bắt đầu (custom, format: YYYY-MM-DD)", required=false, @OA\Schema(type="string", format="date")),
     *     @OA\Parameter(name="end_date", in="query", description="Ngày kết thúc (custom, format: YYYY-MM-DD)", required=false, @OA\Schema(type="string", format="date")),
     *     @OA\Response(
     *         response=200,
     *         description="Thành công",
     *         @OA\JsonContent(
     *             @OA\Property(property="revenue", type="integer", example=42000000, description="Doanh thu trong kỳ"),
     *             @OA\Property(property="revenue_today", type="integer", example=5000000, description="Doanh thu hôm nay"),
     *             @OA\Property(property="cogs", type="integer", example=28000000, description="Giá vốn hàng bán"),
     *             @OA\Property(property="expenses", type="integer", example=3000000, description="Chi phí vận hành"),
     *             @OA\Property(property="profit", type="integer", example=11000000, description="Lợi nhuận ròng"),
     *             @OA\Property(property="export_count", type="integer", example=12),
     *             @OA\Property(property="export_count_today", type="integer", example=3),
     *             @OA\Property(property="import_count", type="integer", example=5),
     *             @OA\Property(property="import_count_today", type="integer", example=1),
     *             @OA\Property(property="low_stock_count", type="integer", example=4, description="Số sản phẩm sắp hết hàng")
     *         )
     *     )
     * )
     */
    public function kpiCards()
    {
    }

    /**
     * @OA\Get(
     *     path="/api/dashboard/charts",
     *     tags={"Dashboard"},
     *     security={{"bearerAuth":{}}},
     *     summary="Biểu đồ: Doanh thu theo ngày/tháng, Lợi nhuận, Top sản phẩm, Phân loại tồn kho",
     *     @OA\Parameter(name="period", in="query", required=false, @OA\Schema(type="string", enum={"today","this_week","this_month","this_year"})),
     *     @OA\Parameter(name="start_date", in="query", required=false, @OA\Schema(type="string", format="date")),
     *     @OA\Parameter(name="end_date", in="query", required=false, @OA\Schema(type="string", format="date")),
     *     @OA\Response(
     *         response=200,
     *         description="Thành công",
     *         @OA\JsonContent(
     *             @OA\Property(property="revenue_chart", type="array", description="Doanh thu theo ngày/tháng", @OA\Items(type="object")),
     *             @OA\Property(property="profit_chart", type="array", description="Lợi nhuận theo ngày/tháng", @OA\Items(type="object")),
     *             @OA\Property(property="top_products", type="array", description="Top 5 sản phẩm bán chạy nhất (SL)", @OA\Items(type="object")),
     *             @OA\Property(property="revenue_by_product", type="array", description="Tỷ trọng doanh thu theo sản phẩm (%)", @OA\Items(type="object")),
     *             @OA\Property(property="inventory_breakdown", type="object", description="Phân loại tồn kho: low, normal, overstock")
     *         )
     *     )
     * )
     */
    public function charts()
    {
    }

    /**
     * @OA\Get(
     *     path="/api/dashboard/recent-activities",
     *     tags={"Dashboard"},
     *     security={{"bearerAuth":{}}},
     *     summary="Lịch sử hoạt động gần nhất (Xuất/Nhập kho)",
     *     @OA\Parameter(name="limit", in="query", description="Số kết quả trả về (mặc định 15)", required=false, @OA\Schema(type="integer", default=15)),
     *     @OA\Response(response=200, description="Thành công")
     * )
     */
    public function recentActivities()
    {
    }

    /**
     * @OA\Get(
     *     path="/api/dashboard/alerts",
     *     tags={"Dashboard"},
     *     security={{"bearerAuth":{}}},
     *     summary="Cảnh báo thông minh & Gợi ý AI (Hàng sắp hết, bán chậm, tồn kho cao, đề xuất nhập hàng)",
     *     @OA\Response(
     *         response=200,
     *         description="Thành công",
     *         @OA\JsonContent(
     *             @OA\Property(property="alerts", type="array", description="Danh sách cảnh báo", @OA\Items(type="object",
     *                 @OA\Property(property="type", type="string", example="low_stock"),
     *                 @OA\Property(property="level", type="string", example="warning"),
     *                 @OA\Property(property="message", type="string", example="⚠️ Xi măng sắp hết hàng (còn: 5 bao)")
     *             )),
     *             @OA\Property(property="suggestions", type="array", description="Gợi ý nhập hàng từ AI", @OA\Items(type="object",
     *                 @OA\Property(property="product_name", type="string", example="Xi măng"),
     *                 @OA\Property(property="suggested_qty", type="integer", example=40),
     *                 @OA\Property(property="message", type="string", example="💡 Gợi ý nhập thêm 40 bao Xi măng")
     *             ))
     *         )
     *     )
     * )
     */
    public function alerts()
    {
    }

    /**
     * @OA\Get(
     *     path="/api/dashboard/mini-reports",
     *     tags={"Dashboard"},
     *     security={{"bearerAuth":{}}},
     *     summary="Mini Reports: Top Khách hàng & Top Nhà cung cấp",
     *     @OA\Parameter(name="period", in="query", required=false, @OA\Schema(type="string", enum={"today","this_week","this_month","this_year"})),
     *     @OA\Parameter(name="start_date", in="query", required=false, @OA\Schema(type="string", format="date")),
     *     @OA\Parameter(name="end_date", in="query", required=false, @OA\Schema(type="string", format="date")),
     *     @OA\Response(
     *         response=200,
     *         description="Thành công",
     *         @OA\JsonContent(
     *             @OA\Property(property="top_customers", type="array", description="Top 5 khách hàng mua nhiều nhất", @OA\Items(type="object")),
     *             @OA\Property(property="top_suppliers", type="array", description="Top 5 nhà cung cấp đặt hàng nhiều nhất", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
    public function miniReports()
    {
    }
}
