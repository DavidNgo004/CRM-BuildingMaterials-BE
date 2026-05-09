<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Import;
use App\Models\ImportDetail;
use App\Models\Export;
use App\Models\ExportDetail;
use App\Models\Expense;
use App\Models\ActivityLog;

class RecentDemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Tạo dữ liệu demo mới nhất (TODAY) bao gồm ActivityLog, Cancel Reason...');

        $adminUser = User::where('role', 'admin')->first();
        $warehouseUser = User::where('role', 'warehouse_staff')->first() ?? $adminUser;
        $customer = Customer::first();
        
        $products = Product::inRandomOrder()->limit(2)->get();
        if ($products->count() < 2) {
            $this->command->error('Không đủ dữ liệu sản phẩm để tạo mẫu mới.');
            return;
        }
        
        $p1 = $products[0];
        $p2 = $products[1];

        // Lấy thời điểm hiện tại chính xác (Today)
        $today = Carbon::now();

        // --- 1. Tạo Import Pending (Hôm nay) ---
        $importPending = Import::create([
            'code' => 'PN-TDY-' . strtoupper(Str::random(4)),
            'user_id' => $warehouseUser->id,
            'total_price' => $p1->import_price * 10,
            'discount_amount' => 0,
            'grand_total' => $p1->import_price * 10,
            'status' => 'pending',
            'note' => 'Nhập hàng khẩn cấp để bù tồn kho (Hôm nay)',
            'created_at' => $today->copy()->subHours(2),
            'updated_at' => $today->copy()->subHours(2),
        ]);
        ImportDetail::create([
            'import_id' => $importPending->id,
            'product_id' => $p1->id,
            'quantity' => 10,
            'unit_price' => $p1->import_price,
            'total_price' => $p1->import_price * 10,
        ]);
        $this->logActivity($warehouseUser, ActivityLog::CREATE_IMPORT, 'Import', $importPending->id, "Tạo mới phiếu nhập " . $importPending->code);

        // --- 2. Tạo Import Cancelled (Hôm nay) ---
        $importCancelled = Import::create([
            'code' => 'PN-TDY-' . strtoupper(Str::random(4)),
            'user_id' => $warehouseUser->id,
            'total_price' => $p2->import_price * 5,
            'discount_amount' => 0,
            'grand_total' => $p2->import_price * 5,
            'status' => 'cancelled',
            'note' => 'Nhập hàng nhưng gặp sự cố',
            'cancel_reason' => 'Nhà cung cấp báo hết hàng, không thể giao đúng hẹn nên buộc phải hủy phiếu.',
            'created_at' => $today->copy()->subHours(4),
            'updated_at' => $today->copy()->subHours(1),
        ]);
        ImportDetail::create([
            'import_id' => $importCancelled->id,
            'product_id' => $p2->id,
            'quantity' => 5,
            'unit_price' => $p2->import_price,
            'total_price' => $p2->import_price * 5,
        ]);
        $this->logActivity($warehouseUser, ActivityLog::CREATE_IMPORT, 'Import', $importCancelled->id, "Tạo mới phiếu nhập " . $importCancelled->code);
        $this->logActivity($adminUser, ActivityLog::CANCEL_IMPORT, 'Import', $importCancelled->id, "Hủy phiếu nhập: Nhà cung cấp báo hết hàng.");

        // --- 3. Tạo Export Completed (Hôm nay) ---
        $exportCompleted = Export::create([
            'code' => 'PX-TDY-' . strtoupper(Str::random(4)),
            'user_id' => $warehouseUser->id,
            'customer_id' => $customer->id,
            'total_price' => $p1->sell_price * 2,
            'discount_amount' => 0,
            'grand_total' => $p1->sell_price * 2,
            'status' => 'completed',
            'note' => 'Khách mua lẻ trực tiếp (Hôm nay)',
            'created_at' => $today->copy()->subHours(5),
            'updated_at' => $today->copy()->subHours(3),
        ]);
        ExportDetail::create([
            'export_id' => $exportCompleted->id,
            'product_id' => $p1->id,
            'quantity' => 2,
            'unit_price' => $p1->sell_price,
            'import_price' => $p1->import_price,
            'total_price' => $p1->sell_price * 2,
        ]);
        // Cập nhật tồn kho cho Export completed
        $p1->decrement('stock', 2);
        
        $this->logActivity($warehouseUser, ActivityLog::CREATE_EXPORT, 'Export', $exportCompleted->id, "Tạo phiếu xuất " . $exportCompleted->code);
        $this->logActivity($adminUser, ActivityLog::APPROVE_EXPORT, 'Export', $exportCompleted->id, "Duyệt phiếu xuất " . $exportCompleted->code);

        // --- 4. Tạo Export Cancelled (Hôm nay) ---
        $exportCancelled = Export::create([
            'code' => 'PX-TDY-' . strtoupper(Str::random(4)),
            'user_id' => $warehouseUser->id,
            'customer_id' => $customer->id,
            'total_price' => $p2->sell_price * 3,
            'discount_amount' => 0,
            'grand_total' => $p2->sell_price * 3,
            'status' => 'cancelled',
            'note' => 'Khách chốt đơn nhưng thay đổi ý định',
            'cancel_reason' => 'Khách hàng liên hệ lại đổi ý, không muốn mua mặt hàng này nữa vì không hợp công trình.',
            'created_at' => $today->copy()->subHours(6),
            'updated_at' => $today->copy()->subHours(2),
        ]);
        ExportDetail::create([
            'export_id' => $exportCancelled->id,
            'product_id' => $p2->id,
            'quantity' => 3,
            'unit_price' => $p2->sell_price,
            'import_price' => $p2->import_price,
            'total_price' => $p2->sell_price * 3,
        ]);
        $this->logActivity($warehouseUser, ActivityLog::CREATE_EXPORT, 'Export', $exportCancelled->id, "Tạo phiếu xuất " . $exportCancelled->code);
        $this->logActivity($adminUser, ActivityLog::CANCEL_EXPORT, 'Export', $exportCancelled->id, "Hủy phiếu xuất: Khách đổi ý.");

        // --- 5. Tạo Expense (Hôm nay) ---
        $expense = Expense::create([
            'title' => 'Chi phí tiếp khách quan trọng (Hôm nay)',
            'amount' => 1500000,
            'expense_date' => $today->format('Y-m-d'),
            'note' => 'Tiếp đối tác nhà thầu lớn',
            'user_id' => $adminUser->id,
            'created_at' => $today->copy()->subHours(1),
            'updated_at' => $today->copy()->subHours(1),
        ]);
        $this->logActivity($adminUser, ActivityLog::CREATE_EXPENSE, 'Expense', $expense->id, "Tạo khoản chi: Chi phí tiếp khách quan trọng");

        $this->command->info('✓ Đã tạo thành công dữ liệu mới nhất phản ánh các tính năng vừa code (Cancel, Log) ngay trong ngày hôm nay!');
    }

    private function logActivity($user, $action, $entityType, $entityId, $description)
    {
        ActivityLog::create([
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_role' => $user->role,
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'old_data' => null,
            'new_data' => null,
            'description' => $description,
            'ip_address' => '127.0.0.1',
            'created_at' => Carbon::now(),
        ]);
    }
}
