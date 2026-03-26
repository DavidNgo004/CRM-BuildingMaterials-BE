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
        $alerts      = [];
        $suggestions = [];

        // Lấy dữ liệu bán hàng 30 ngày gần nhất và 30 ngày trước đó để so sánh
        $salesLast30  = $this->getSalesQtyByProduct(Carbon::now()->subDays(30), Carbon::now());
        $salesPrior30 = $this->getSalesQtyByProduct(Carbon::now()->subDays(60), Carbon::now()->subDays(30));

        // =============================================
        // A. Quét trạng thái tồn kho từng sản phẩm
        // =============================================
        $products = Product::all();

        foreach ($products as $product) {
            if ($product->stock === 0) {
                // Trường hợp 1: HẾT HÀNG HOÀN TOÀN
                $alerts[] = $this->buildAlert('out_of_stock', 'critical',
                    "🚨 {$product->name} đã HẾT HÀNG (tồn kho: 0 {$product->unit})",
                    $product->name
                );
                // Gợi ý nhập đủ 2× reorder_level
                $suggestions[] = $this->buildSuggestion($product, $product->reorder_level * 2);

            } elseif ($product->stock <= $product->reorder_level) {
                // Trường hợp 2: SẮP HẾT HÀNG
                $alerts[] = $this->buildAlert('low_stock', 'warning',
                    "⚠️ {$product->name} sắp hết hàng (còn: {$product->stock} {$product->unit}, ngưỡng: {$product->reorder_level})",
                    $product->name
                );
                // Gợi ý nhập thêm để đạt 2× reorder_level
                $needed = ($product->reorder_level * 2) - $product->stock;
                $suggestions[] = $this->buildSuggestion($product, $needed);
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
                $product = Product::find($productId);
                if ($product) {
                    $alerts[] = $this->buildAlert('slow_moving', 'info',
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
        $overstockProducts = Product::whereRaw('stock > reorder_level * 3')->get();
        foreach ($overstockProducts as $product) {
            $recentSales = $salesLast30[$product->id] ?? 0;
            if ($recentSales == 0) {
                $alerts[] = $this->buildAlert('overstock', 'info',
                    "📦 {$product->name} tồn kho cao ({$product->stock} {$product->unit}) – không có đơn bán trong 30 ngày qua",
                    $product->name
                );
            }
        }

        return [
            'alerts'      => $alerts,
            'suggestions' => $suggestions,
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
        return ExportDetail::whereHas('export', fn ($q) =>
            $q->where('status', 'completed')->whereBetween('updated_at', [$from, $to])
        )
        ->select('product_id', DB::raw('SUM(quantity) as qty'))
        ->groupBy('product_id')
        ->pluck('qty', 'product_id');
    }

    /**
     * Tạo object cảnh báo chuẩn hóa.
     *
     * @param string $type     Loại cảnh báo (out_of_stock, low_stock, slow_moving, overstock)
     * @param string $level    Mức độ (critical, warning, info)
     * @param string $message  Nội dung hiển thị
     * @param string $product  Tên sản phẩm
     * @param array  $extra    Dữ liệu bổ sung tùy loại cảnh báo
     */
    private function buildAlert(string $type, string $level, string $message, string $product, array $extra = []): array
    {
        return array_merge([
            'type'    => $type,
            'level'   => $level,
            'message' => $message,
            'product' => $product,
        ], $extra);
    }

    /**
     * Tạo object gợi ý nhập hàng từ AI.
     *
     * @param Product $product     Sản phẩm cần nhập
     * @param int     $suggestedQty Số lượng gợi ý cần nhập thêm
     */
    private function buildSuggestion(Product $product, int $suggestedQty): array
    {
        return [
            'product_id'    => $product->id,
            'product_name'  => $product->name,
            'unit'          => $product->unit,
            'current_stock' => $product->stock,
            'reorder_level' => $product->reorder_level,
            'suggested_qty' => max(1, $suggestedQty),
            'message'       => "💡 Gợi ý nhập thêm " . max(1, $suggestedQty) . " {$product->unit} {$product->name}",
        ];
    }
}
