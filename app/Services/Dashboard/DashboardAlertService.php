<?php

namespace App\Services\Dashboard;

use App\Models\ExportDetail;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * DashboardAlertService  (AI Module)
 *
 * Module "AI" của Dashboard – phân tích dữ liệu tồn kho và lịch sử bán hàng
 * để đưa ra:
 *
 * 1. CẢNH BÁO (Alerts):
 *    - Hàng hết kho (out_of_stock)
 *    - Hàng sắp hết (low_stock: stock <= reorder_level)
 *    - Hàng bán chậm (slow_moving: sụt giảm ≥40% so với 30 ngày trước)
 *    - Tồn kho quá cao, không có doanh thu (overstock)
 *
 * 2. GỢI Ý NHẬP HÀNG (Suggestions):
 *    - Tính số lượng cần nhập thêm để đưa tồn kho lên 2× reorder_level
 *
 * Đây là lớp có thể mở rộng thêm các thuật toán ML/AI phức tạp hơn
 * (Forecasting, Linear Regression...) mà không ảnh hưởng đến các service khác.
 */
class DashboardAlertService
{
    /**
     * Điểm vào chính – trả về toàn bộ cảnh báo và gợi ý AI.
     *
     * @return array{alerts: array, suggestions: array}
     */
    public function getAlerts(): array
    {
        $alerts = [];
        $suggestions = [];

        // Lấy dữ liệu bán hàng 30 ngày gần nhất và 30 ngày trước đó để so sánh
        $salesLast30 = $this->getSalesQtyByProduct(Carbon::now()->subDays(30), Carbon::now());
        $salesPrior30 = $this->getSalesQtyByProduct(Carbon::now()->subDays(60), Carbon::now()->subDays(30));

        // TỐI ƯU HIỆU NĂNG N+1: Lấy trước toàn bộ dữ liệu lịch sử bán hàng theo ngày của tất cả sản phẩm
        // Sẽ lưu dưới dạng ['product_id' => [10, 5, 20, 0, 15...]] (mảng các quantity tuần tự)
        $bulkDailySales = $this->getBulkDailySales();

        // =============================================
        // A. Quét trạng thái tồn kho từng sản phẩm
        // =============================================
        $products = Product::all();

        foreach ($products as $product) {
            if ($product->stock === 0) {
                // Trường hợp 1: HẾT HÀNG HOÀN TOÀN
                $alerts[] = $this->buildAlert(
                    'out_of_stock',
                    'critical',
                    "🚨 {$product->name} đã HẾT HÀNG (tồn kho: 0 {$product->unit})",
                    $product->name
                );
                // Gợi ý nhập đủ 2× reorder_level
                $suggestions[] = $this->buildSuggestion($product, max(1, $product->reorder_level * 2));

            } elseif ($product->stock <= $product->reorder_level) {
                // Trường hợp 2: SẮP HẾT HÀNG (Dựa theo ngưỡng cứng reorder_level)
                $alerts[] = $this->buildAlert(
                    'low_stock',
                    'warning',
                    "⚠️ {$product->name} sắp hết hàng (còn: {$product->stock} {$product->unit}, ngưỡng: {$product->reorder_level})",
                    $product->name
                );

                // AI 1 dự báo động dựa trên lịch sử
                $lastQty = $salesLast30[$product->id] ?? 0;
                $historyArray = $bulkDailySales->get($product->id, []);
                
                $forecast14 = $this->calculateLinearRegressionForecast($historyArray, 14);

                if ($forecast14 == 0) {
                    // fallback nếu dữ liệu chưa đủ train
                    $avgDaily = $lastQty / 30;
                    $forecast14 = ceil($avgDaily * 14);
                }

                // Nếu tồn kho hiện tại không đủ bán trong 14 ngày tới -> gợi ý nhập thêm
                if ($product->stock < $forecast14) {
                    $needed = max($product->reorder_level * 2, $forecast14) - $product->stock;
                    // Gợi ý nhập thêm theo dự báo hoặc đảm bảo ngưỡng an toàn 2x
                    $suggestions[] = $this->buildSuggestion($product, $needed);

                    $alerts[] = $this->buildAlert(
                        'ai_forecast',
                        'info',
                        "🤖 Dự báo AI: {$product->name} cần {$forecast14} {$product->unit} cho 14 ngày tới. (Tồn: {$product->stock})",
                        $product->name
                    );
                } else {
                    // Nếu đủ bán thì gợi ý nhập thêm mức cơ bản 2x_reorder
                    $needed = ($product->reorder_level * 2) - $product->stock;
                    if ($needed > 0) {
                        $suggestions[] = $this->buildSuggestion($product, $needed);
                    }
                }
            }
        }

        // =============================================
        // B. Phát hiện sản phẩm bán CHẬM (Slow-moving)
        //    Điều kiện: Doanh số 30 ngày gần nhất giảm ≥40% so với 30 ngày trước
        // =============================================
        foreach ($salesPrior30 as $productId => $priorQty) {
            if ($priorQty == 0) continue;

            $lastQty = $salesLast30[$productId] ?? 0;
            $dropPct = round((($priorQty - $lastQty) / $priorQty) * 100, 1);

            if ($dropPct >= 40) {
                // Find in our already fetched collection instead of DB query
                $product = $products->firstWhere('id', $productId);
                if ($product) {
                    $alerts[] = $this->buildAlert(
                        'slow_moving',
                        'info',
                        "📉 {$product->name} bán giảm {$dropPct}% so với 30 ngày trước ({$priorQty} → {$lastQty} {$product->unit})",
                        $product->name,
                        ['drop_pct' => $dropPct, 'prior_qty' => $priorQty, 'last_qty' => $lastQty]
                    );
                }
            }
        }

        // =============================================
        // C. Phát hiện TỒNG KHO CHẾT (Overstock + không bán)
        //    Điều kiện: stock > 3× reorder_level VÀ không có đơn nào trong 30 ngày gần nhất
        // =============================================
        $overstockProducts = $products->filter(function($p) {
            return $p->stock > ($p->reorder_level * 3);
        });

        foreach ($overstockProducts as $product) {
            $recentSales = $salesLast30[$product->id] ?? 0;
            if ($recentSales == 0) {
                $alerts[] = $this->buildAlert(
                    'overstock',
                    'info',
                    "🧊 {$product->name} tồn kho cao ({$product->stock} {$product->unit}) – không có đơn bán trong 30 ngày qua",
                    $product->name
                );
            }
        }

        return [
            'alerts' => array_values($alerts),
            'suggestions' => array_values($suggestions),
        ];
    }

    // -----------------------------------------------------------------------
    // PRIVATE HELPERS
    // -----------------------------------------------------------------------

    /**
     * Lấy tổng số lượng đã bán của từng sản phẩm trong khoảng thời gian.
     *
     * @return \Illuminate\Support\Collection  [product_id => total_qty]
     */
    private function getSalesQtyByProduct(Carbon $from, Carbon $to)
    {
        return DB::table('export_details')
            ->join('exports', 'exports.id', '=', 'export_details.export_id')
            ->where('exports.status', 'completed')
            ->whereBetween('exports.updated_at', [$from, $to])
            ->select('export_details.product_id', DB::raw('SUM(export_details.quantity) as qty'))
            ->groupBy('export_details.product_id')
            ->pluck('qty', 'product_id');
    }

    /**
     * Kéo toàn bộ lịch sử bán hàng theo ngày trong 90 ngày của tất cả các sản phẩm.
     * Đây là cách tối ưu N+1 query để hệ thống hoạt động với O(1) query.
     */
    private function getBulkDailySales()
    {
        return DB::table('export_details')
            ->join('exports', 'exports.id', '=', 'export_details.export_id')
            ->where('exports.status', 'completed')
            ->where('exports.updated_at', '>=', Carbon::now()->subDays(90))
            ->select(
                'export_details.product_id',
                DB::raw('DATE(exports.updated_at) as date'),
                DB::raw('SUM(export_details.quantity) as qty')
            )
            ->groupBy('export_details.product_id', 'date')
            ->orderBy('date')
            ->get()
            ->groupBy('product_id')
            ->map(function ($items) {
                // Trả ra 1 tập mảng số thực [quantity1, quantity2, ...] đã được xếp theo ngày
                return $items->pluck('qty')->toArray();
            });
    }

    private function buildAlert(string $type, string $level, string $message, string $product, array $extra = []): array
    {
        return array_merge([
            'type' => $type,
            'level' => $level,
            'message' => $message,
            'product' => $product,
        ], $extra);
    }

    private function buildSuggestion(Product $product, int $suggestedQty): array
    {
        return [
            'product_id' => $product->id,
            'product_name' => $product->name,
            'unit' => $product->unit,
            'current_stock' => $product->stock,
            'reorder_level' => $product->reorder_level,
            'suggested_qty' => max(1, $suggestedQty),
            'message' => "💡 Gợi ý nhập thêm " . max(1, $suggestedQty) . " {$product->unit} {$product->name}",
        ];
    }
    
    /**
     * Mô hình huấn luyện AI - Linear Regression
     * Nhận đầu vào là mảng các số lượng bán ra theo ngày.
     */
    private function calculateLinearRegressionForecast(array $history, int $days = 14): int
    {
        $n = count($history);

        if ($n < 5) {
            return 0; // không đủ dữ liệu train
        }

        $x = range(1, $n);

        $sumX = array_sum($x);
        $sumY = array_sum($history);

        $sumXY = 0;
        $sumX2 = 0;

        for ($i = 0; $i < $n; $i++) {
            $sumXY += $x[$i] * $history[$i];
            $sumX2 += $x[$i] * $x[$i];
        }

        $denominator = ($n * $sumX2 - $sumX * $sumX);
        if ($denominator == 0) {
            return 0; // Tránh lỗi chia cho 0 nếu tất cả giá trị đều giống y hệt nhau
        }

        $a = ($n * $sumXY - $sumX * $sumY) / $denominator;
        $b = ($sumY - $a * $sumX) / $n;

        $forecast = 0;

        for ($i = 1; $i <= $days; $i++) {
            $forecast += ($a * ($n + $i) + $b);
        }

        return max(0, (int) ceil($forecast));
    }
}
