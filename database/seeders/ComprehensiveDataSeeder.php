<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Supplier;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Import;
use App\Models\ImportDetail;
use App\Models\Export;
use App\Models\ExportDetail;
use App\Models\InventoryLog;
use App\Models\Expense;
use Database\Seeders\AdminSeeder;

class ComprehensiveDataSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * Tạo dữ liệu mẫu chuẩn cho hệ thống CRM Vật liệu Xây dựng
     */
    public function run(): void
    {
        $this->command->info('=' . str_repeat('=', 58) . '=');
        $this->command->info('Bắt đầu tạo dữ liệu mẫu chuẩn cho hệ thống CRM VLXD');
        $this->command->info('=' . str_repeat('=', 58) . '=');

        // Bước 1: Tạo Users
        $this->seedUsers();
        $this->call(AdminSeeder::class); // Tạo admin mặc định (nếu chưa có)

        // Bước 2: Tạo Suppliers (Nhà cung cấp)
        $supplierIds = $this->seedSuppliers();

        // Bước 3: Tạo Customers (Khách hàng)
        $customerIds = $this->seedCustomers();

        // Bước 4: Tạo Products (Sản phẩm) với Supplier
        $products = $this->seedProducts($supplierIds);

        // Bước 5: Tạo Imports (Phiếu nhập hàng) từ Suppliers
        $this->seedImports($products);

        // Bước 6: Tạo Exports (Phiếu xuất/bán hàng) cho Customers
        $this->seedExports($products, $customerIds);

        // Bước 7: Tạo Expenses (Chi phí hoạt động)
        $this->seedExpenses();

        $this->command->info('✓ Hoàn thành! Dữ liệu mẫu đã được tạo thành công.');
    }

    /**
     * Tạo dữ liệu User (Người dùng/Nhân viên)
     */
    private function seedUsers(): void
    {
        $this->command->info("\n▶ Tạo Users...");

        $users = [
            
            [
                'name' => 'Nhân viên Bán hàng 1',
                'email' => 'sales1@crm.vn',
                'password' => bcrypt('Sales@123'),
                'role' => 'warehouse_staff'
            ],
            [
                'name' => 'Nhân viên Bán hàng 2',
                'email' => 'sales2@crm.vn',
                'password' => bcrypt('Sales@123'),
                'role' => 'warehouse_staff'
            ],
            [
                'name' => 'Nhân viên Kho',
                'email' => 'warehouse@crm.vn',
                'password' => bcrypt('Warehouse@123'),
                'role' => 'warehouse_staff'
            ],
            [
                'name' => 'Nhân viên Mua hàng',
                'email' => 'purchasing@crm.vn',
                'password' => bcrypt('Purchasing@123'),
                'role' => 'warehouse_staff'
            ]
        ];

        foreach ($users as $user) {
            User::firstOrCreate(
                ['email' => $user['email']],
                $user
            );
        }

        $userCount = User::count();
        $this->command->info("  ✓ Tạo {$userCount} User thành công");
    }

    /**
     * Tạo dữ liệu Supplier (Nhà cung cấp)
     */
    private function seedSuppliers(): array
    {
        $this->command->info("\n▶ Tạo Suppliers (Nhà cung cấp)...");

        $suppliers = [
            [
                'name' => 'Công ty TNHH Xi măng Hà Tiên 1',
                'tax_code' => '0101234567890',
                'phone' => '0901234567',
                'email' => 'contact@hatien1.com.vn',
                'address' => '123 Đường Võ Văn Kiệt, TP.HCM',
                'status' => true,
                'notes' => 'Nhà cung cấp xi măng đen, trắng chất lượng cao'
            ],
            [
                'name' => 'Tập đoàn Thép Hòa Phát',
                'tax_code' => '0102345678901',
                'phone' => '0912345678',
                'email' => 'sales@hoaphat.com.vn',
                'address' => 'Khu công nghiệp Định Mỹ, Hưng Yên',
                'status' => true,
                'notes' => 'Sản xuất thép cuộn, thanh vằn, thép cây'
            ],
            [
                'name' => 'Công ty Thép Pomina',
                'tax_code' => '0103456789012',
                'phone' => '0923456789',
                'email' => 'info@pomina.vn',
                'address' => 'Lô 5-6, Khu CN Amata, Biên Hòa, Đồng Nai',
                'status' => true,
                'notes' => 'Thép Pomina chất lượng, giá cạnh tranh'
            ],
            [
                'name' => 'Nhà máy Gạch Đồng Tâm',
                'tax_code' => '0104567890123',
                'phone' => '0934567890',
                'email' => 'sales@dongtam.vn',
                'address' => 'Xã Tân Hiệp, Huyện Cần Đước, Long An',
                'status' => true,
                'notes' => 'Gạch ống, gạch đinh đặc, các loại gạch xây'
            ],
            [
                'name' => 'Công ty TNHH Khai thác Cát Đá Biên Hòa',
                'tax_code' => '0105678901234',
                'phone' => '0945678901',
                'email' => 'catda@bienhoa.vn',
                'address' => 'Biên Hòa, Đồng Nai',
                'status' => true,
                'notes' => 'Cung cấp cát xây, cát san lấp, đá 1x2, đá mi bụi'
            ],
            [
                'name' => 'Công ty SƠN DULUX Việt Nam',
                'tax_code' => '0106789012345',
                'phone' => '0956789012',
                'email' => 'sales@duluxVN.com',
                'address' => 'Bình Dương',
                'status' => true,
                'notes' => 'Sơn nước, sơn dầu các loại'
            ],
            [
                'name' => 'Công ty INOX VĨ KHÁNH',
                'tax_code' => '0107890123456',
                'phone' => '0967890123',
                'email' => 'contact@vikh.vn',
                'address' => 'TP.HCM',
                'status' => true,
                'notes' => 'Khung sắt, cửa sắt, mắt xích sắt'
            ]
        ];

        $ids = [];
        $codeCounter = 1;
        foreach ($suppliers as $supplier) {
            // Thêm code trước khi insert
            $supplier['code'] = 'NCC' . str_pad($codeCounter, 3, '0', STR_PAD_LEFT);
            $s = Supplier::firstOrCreate(
                ['phone' => $supplier['phone']],
                $supplier
            );
            $ids[] = $s->id;
            $codeCounter++;
        }

        $this->command->info("  ✓ Tạo " . count($ids) . " Supplier thành công");
        return $ids;
    }

    /**
     * Tạo dữ liệu Customer (Khách hàng)
     */
    private function seedCustomers(): array
    {
        $this->command->info("\n▶ Tạo Customers (Khách hàng)...");

        $customers = [
            // Khách hàng sỉ (Wholesale)
            [
                'name' => 'Công ty Xây Dựng Coteccons',
                'phone' => '0909000111',
                'email' => 'coteccons@company.vn',
                'address' => '15 Đường D5, Biên Hòa Newcity, TP.HCM',
                'customer_type' => 'wholesale',
                'status' => true,
                'notes' => 'Khách hàng sỉ - Dự án lớn'
            ],
            [
                'name' => 'Nhà thầu Xây dựng Hòa Bình',
                'phone' => '0909000222',
                'email' => 'hoabinhxd@company.vn',
                'address' => 'Q.Bình Tân, TP.HCM',
                'customer_type' => 'wholesale',
                'status' => true,
                'notes' => 'Nhà thầu xây dựng uy tín'
            ],
            [
                'name' => 'Xây dựng An Phong',
                'phone' => '0909000333',
                'email' => 'anphong@company.vn',
                'address' => 'Q.7, TP.HCM',
                'customer_type' => 'wholesale',
                'status' => true,
                'notes' => 'Dự án cao tầng, chung cư'
            ],
            // Khách hàng lẻ (Retail)
            [
                'name' => 'Cửa hàng VLXD Thạnh Mỹ Lợi',
                'phone' => '0909000444',
                'email' => 'thanhmyloi@shop.vn',
                'address' => 'Q.2, TP.HCM',
                'customer_type' => 'retail',
                'status' => true,
                'notes' => 'Cửa hàng bán lẻ vật liệu xây dựng'
            ],
            [
                'name' => 'Cửa hàng Sắt Thép Bình Tân',
                'phone' => '0909000555',
                'email' => 'satthepbt@shop.vn',
                'address' => 'Q.Bình Tân, TP.HCM',
                'customer_type' => 'retail',
                'status' => true,
                'notes' => 'Chuyên bán sắt thép'
            ],
            [
                'name' => 'Cửa hàng Kim Khí Tân Bình',
                'phone' => '0909000666',
                'email' => 'kimkhi@shop.vn',
                'address' => 'Q.Tân Bình, TP.HCM',
                'customer_type' => 'retail',
                'status' => true,
                'notes' => 'Bán lẻ kim khí, công cụ'
            ],
            [
                'name' => 'CTy Nội thất Kiến Á',
                'phone' => '0909000777',
                'email' => 'kiena@company.vn',
                'address' => 'TP.HCM',
                'customer_type' => 'wholesale',
                'status' => true,
                'notes' => 'Nhà cung cấp nội thất'
            ],
            [
                'name' => 'Xí nghiệp Xây đắp Thủ Đức',
                'phone' => '0909000888',
                'email' => 'xaydap@company.vn',
                'address' => 'TP.Thủ Đức, TP.HCM',
                'customer_type' => 'wholesale',
                'status' => true,
                'notes' => 'Xí nghiệp xây dựng'
            ],
            [
                'name' => 'Cửa hàng Vật Liệu Anh Hải',
                'phone' => '0909000999',
                'email' => 'anhhai@shop.vn',
                'address' => 'Quận 12, TP.HCM',
                'customer_type' => 'retail',
                'status' => true,
                'notes' => 'Bán lẻ vật liệu xây dựng'
            ],
            [
                'name' => 'Dự án Khu đô thị Thủ Thiêm',
                'phone' => '0909001000',
                'email' => 'thuthiem@project.vn',
                'address' => 'TP.Thủ Đức, TP.HCM',
                'customer_type' => 'wholesale',
                'status' => true,
                'notes' => 'Dự án lớn, khách hàng VIP'
            ]
        ];

        $ids = [];
        $codeCounter = 1;
        foreach ($customers as $customer) {
            $customer['code'] = 'KH' . str_pad($codeCounter, 3, '0', STR_PAD_LEFT);
            $c = Customer::firstOrCreate(
                ['phone' => $customer['phone']],
                $customer
            );
            $ids[] = $c->id;
            $codeCounter++;
        }

        $this->command->info("  ✓ Tạo " . count($ids) . " Customer thành công");
        return $ids;
    }

    /**
     * Tạo dữ liệu Product (Sản phẩm)
     */
    private function seedProducts(array $supplierIds): array
    {
        $this->command->info("\n▶ Tạo Products (Sản phẩm)...");

        $products = [
            // Xi măng (Supplier 0: Hà Tiên 1)
            [
                'supplier_id' => $supplierIds[0],
                'name' => 'Xi măng đen Hà Tiên 1 (bao 50kg)',
                'unit' => 'Bao',
                'import_price' => 78000,
                'sell_price' => 88000,
                'reorder_level' => 100,
                'notes' => 'Xi măng PCB 40'
            ],
            [
                'supplier_id' => $supplierIds[0],
                'name' => 'Xi măng trắng (bao 50kg)',
                'unit' => 'Bao',
                'import_price' => 120000,
                'sell_price' => 142000,
                'reorder_level' => 50,
                'notes' => 'Xi măng trắng chất lượng cao'
            ],
            // Thép - Hòa Phát (Supplier 1)
            [
                'supplier_id' => $supplierIds[1],
                'name' => 'Thép cuộn D6 Hòa Phát',
                'unit' => 'Kg',
                'import_price' => 15000,
                'sell_price' => 17500,
                'reorder_level' => 500,
                'notes' => 'Thép cuộn xây dựng'
            ],
            [
                'supplier_id' => $supplierIds[1],
                'name' => 'Thép thanh vằn D10 Hòa Phát',
                'unit' => 'Cây',
                'import_price' => 105000,
                'sell_price' => 120000,
                'reorder_level' => 80,
                'notes' => 'Thép thanh dài 12m'
            ],
            [
                'supplier_id' => $supplierIds[1],
                'name' => 'Thép thanh vằn D13 Hòa Phát',
                'unit' => 'Cây',
                'import_price' => 175000,
                'sell_price' => 198000,
                'reorder_level' => 60,
                'notes' => 'Thép thanh dài 12m'
            ],
            // Thép - Pomina (Supplier 2)
            [
                'supplier_id' => $supplierIds[2],
                'name' => 'Thép cây D16 Pomina',
                'unit' => 'Cây',
                'import_price' => 280000,
                'sell_price' => 315000,
                'reorder_level' => 50,
                'notes' => 'Thép cây chất lượng Pomina'
            ],
            [
                'supplier_id' => $supplierIds[2],
                'name' => 'Thép cây D20 Pomina',
                'unit' => 'Cây',
                'import_price' => 440000,
                'sell_price' => 495000,
                'reorder_level' => 40,
                'notes' => 'Thép cây chất lượng Pomina'
            ],
            // Gạch - Đồng Tâm (Supplier 3)
            [
                'supplier_id' => $supplierIds[3],
                'name' => 'Gạch ống 4 lỗ Đồng Tâm (1 tấn)',
                'unit' => 'Tấn',
                'import_price' => 800000,
                'sell_price' => 950000,
                'reorder_level' => 10,
                'notes' => 'Gạch ống 4 lỗ loại 1'
            ],
            [
                'supplier_id' => $supplierIds[3],
                'name' => 'Gạch đinh đặc (1 tấn)',
                'unit' => 'Tấn',
                'import_price' => 950000,
                'sell_price' => 1100000,
                'reorder_level' => 8,
                'notes' => 'Gạch đinh đặc chất lượng cao'
            ],
            // Cát và Đá - Biên Hòa (Supplier 4)
            [
                'supplier_id' => $supplierIds[4],
                'name' => 'Cát xây tô (1 khối)',
                'unit' => 'Khối',
                'import_price' => 250000,
                'sell_price' => 310000,
                'reorder_level' => 20,
                'notes' => 'Cát sạch chất lượng xây tô'
            ],
            [
                'supplier_id' => $supplierIds[4],
                'name' => 'Cát san lấp (1 khối)',
                'unit' => 'Khối',
                'import_price' => 150000,
                'sell_price' => 210000,
                'reorder_level' => 30,
                'notes' => 'Cát san lấp xây dựng'
            ],
            [
                'supplier_id' => $supplierIds[4],
                'name' => 'Đá 1x2 (1 khối)',
                'unit' => 'Khối',
                'import_price' => 320000,
                'sell_price' => 420000,
                'reorder_level' => 15,
                'notes' => 'Đá 1x2mm xây dựng'
            ],
            [
                'supplier_id' => $supplierIds[4],
                'name' => 'Đá mi bụi (1 khối)',
                'unit' => 'Khối',
                'import_price' => 220000,
                'sell_price' => 290000,
                'reorder_level' => 25,
                'notes' => 'Đá xây dựng mi bụi'
            ],
            // Sơn - Dulux (Supplier 5)
            [
                'supplier_id' => $supplierIds[5],
                'name' => 'Sơn nước Dulux 18L',
                'unit' => 'Thùng',
                'import_price' => 1800000,
                'sell_price' => 2150000,
                'reorder_level' => 20,
                'notes' => 'Sơn nước Dulux chính hãng'
            ],
            [
                'supplier_id' => $supplierIds[5],
                'name' => 'Sơn dầu Dulux 5L',
                'unit' => 'Thùng',
                'import_price' => 950000,
                'sell_price' => 1150000,
                'reorder_level' => 15,
                'notes' => 'Sơn dầu bóng loại 1'
            ],
            // Kim khí - INOX Vĩ Khánh (Supplier 6)
            [
                'supplier_id' => $supplierIds[6],
                'name' => 'Khung cửa sắt 1 cánh',
                'unit' => 'Bộ',
                'import_price' => 2500000,
                'sell_price' => 3100000,
                'reorder_level' => 5,
                'notes' => 'Cửa sắt tiêu chuẩn'
            ],
            [
                'supplier_id' => $supplierIds[6],
                'name' => 'Mắt xích sắt 18mm (1 bộ)',
                'unit' => 'Bộ',
                'import_price' => 450000,
                'sell_price' => 580000,
                'reorder_level' => 10,
                'notes' => 'Xích sắt chất lượng cao'
            ]
        ];

        $productMap = [];
        foreach ($products as $product) {
            // Loại bỏ 'notes' vì field không tồn tại trong migration
            unset($product['notes']);
            $product['stock'] = 0;
            $product['status'] = true;
            $p = Product::create($product);
            $productMap[$p->id] = [
                'id' => $p->id,
                'import_price' => $product['import_price'],
                'sell_price' => $product['sell_price'],
                'stock' => 0
            ];
        }

        $this->command->info("  ✓ Tạo " . count($productMap) . " Product thành công");
        return $productMap;
    }

    /**
     * Tạo dữ liệu Import (Phiếu nhập hàng từ Suppliers)
     */
    private function seedImports(array $products): void
    {
        $this->command->info("\n▶ Tạo Imports (Phiếu nhập hàng)...");

        $adminUser = User::where('role', 'admin')->first();
        $purchasingUser = User::where('email', 'purchasing@crm.vn')->first() ?? $adminUser;

        $importCount = 0;
        $startDate = Carbon::now()->subMonths(3)->startOfDay();
        $endDate = Carbon::now()->endOfDay();
        $productArray = array_keys($products);

        for ($date = clone $startDate; $date->lte($endDate); $date->addDay()) {
            // 30% ngày có nhập hàng
            if (rand(1, 100) > 70) {
                $numImports = rand(1, 2);

                for ($i = 0; $i < $numImports; $i++) {
                    $productId = $productArray[array_rand($productArray)];
                    $quantity = rand(50, 300);
                    $unitPrice = $products[$productId]['import_price'];
                    $totalPrice = $quantity * $unitPrice;

                    $import = Import::create([
                        'code' => 'PN-' . $date->format('YmdHis') . '-' . strtoupper(Str::random(3)),
                        'user_id' => $purchasingUser->id,
                        'total_price' => $totalPrice,
                        'discount_amount' => 0,
                        'grand_total' => $totalPrice,
                        'status' => 'completed',
                        'note' => 'Nhập hàng định kỳ từ nhà cung cấp',
                        'created_at' => $date->copy()->addHours(rand(8, 11)),
                        'updated_at' => $date->copy()->addHours(rand(8, 11)),
                    ]);

                    ImportDetail::create([
                        'import_id' => $import->id,
                        'product_id' => $productId,
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                        'total_price' => $totalPrice,
                    ]);

                    // Cập nhật stock
                    $product = Product::find($productId);
                    $product->increment('stock', $quantity);

                    // Tạo inventory log
                    InventoryLog::create([
                        'product_id' => $productId,
                        'type' => 'import',
                        'quantity' => $quantity,
                        'created_by' => $purchasingUser->id,
                        'created_at' => $date->copy()->addHours(rand(8, 11)),
                        'updated_at' => $date->copy()->addHours(rand(8, 11)),
                    ]);

                    $importCount++;
                }
            }
        }

        $this->command->info("  ✓ Tạo {$importCount} Import thành công");
    }

    /**
     * Tạo dữ liệu Export (Phiếu xuất/bán hàng cho Customers)
     */
    private function seedExports(array $products, array $customerIds): void
    {
        $this->command->info("\n▶ Tạo Exports (Phiếu bán hàng)...");

        $salesUsers = User::where('email', 'like', 'sales%')->get();
        $adminUser = User::where('role', 'admin')->first();
        $allUsers = $salesUsers->count() > 0 ? $salesUsers->toArray() : [$adminUser];

        $exportCount = 0;
        $startDate = Carbon::now()->subMonths(3)->startOfDay();
        $endDate = Carbon::now()->endOfDay();
        $productArray = array_keys($products);

        for ($date = clone $startDate; $date->lte($endDate); $date->addDay()) {
            // 70% ngày có bán hàng
            if (rand(1, 100) > 30) {
                $numExports = rand(1, 4);

                for ($k = 0; $k < $numExports; $k++) {
                    $numProducts = rand(1, 3);
                    $selectedProducts = array_rand(array_flip($productArray), min($numProducts, count($productArray)));
                    if (!is_array($selectedProducts)) {
                        $selectedProducts = [$selectedProducts];
                    }

                    $user = $allUsers[array_rand($allUsers)];
                    $userId = is_array($user) ? $user['id'] : $user->id;
                    $customerId = $customerIds[array_rand($customerIds)];
                    $grandTotal = 0;
                    $exportDetails = [];

                    foreach ($selectedProducts as $productId) {
                        $quantity = rand(5, 50);
                        $unitPrice = $products[$productId]['sell_price'];
                        $total = $quantity * $unitPrice;
                        $grandTotal += $total;

                        $exportDetails[] = [
                            'product_id' => $productId,
                            'quantity' => $quantity,
                            'unit_price' => $unitPrice,
                            'import_price' => $products[$productId]['import_price'],
                            'total_price' => $total,
                        ];

                        // Cập nhật stock (cho phép âm trong demo)
                        $product = Product::find($productId);
                        $newStock = $product->stock - $quantity;
                        $product->update(['stock' => $newStock]);

                        // Tạo inventory log
                        InventoryLog::create([
                            'product_id' => $productId,
                            'type' => 'export',
                            'quantity' => $quantity,
                            'created_by' => $userId,
                            'created_at' => $date->copy()->addHours(rand(13, 17)),
                            'updated_at' => $date->copy()->addHours(rand(13, 17)),
                        ]);
                    }

                    $export = Export::create([
                        'code' => 'PX-' . $date->format('YmdHis') . '-' . strtoupper(Str::random(3)),
                        'user_id' => $userId,
                        'customer_id' => $customerId,
                        'total_price' => $grandTotal,
                        'discount_amount' => 0,
                        'grand_total' => $grandTotal,
                        'status' => 'completed',
                        'note' => 'Bán hàng cho khách hàng',
                        'created_at' => $date->copy()->addHours(rand(13, 17)),
                        'updated_at' => $date->copy()->addHours(rand(13, 17)),
                    ]);

                    foreach ($exportDetails as $detail) {
                        $detail['export_id'] = $export->id;
                        ExportDetail::create($detail);
                    }

                    $exportCount++;
                }
            }
        }

        $this->command->info("  ✓ Tạo {$exportCount} Export thành công");
    }

    /**
     * Tạo dữ liệu Expense (Chi phí hoạt động)
     */
    private function seedExpenses(): void
    {
        $this->command->info("\n▶ Tạo Expenses (Chi phí hoạt động)...");

        $users = User::all();
        $adminUser = $users->where('role', 'admin')->first() ?? $users->first();

        $expenseCategories = [
            ['title' => 'Lương nhân viên tháng', 'amount' => 50000000],
            ['title' => 'Chi phí điện', 'amount' => 3000000],
            ['title' => 'Chi phí nước', 'amount' => 800000],
            ['title' => 'Chi phí vận chuyển', 'amount' => 5000000],
            ['title' => 'Chi phí bảo trì thiết bị', 'amount' => 2000000],
            ['title' => 'Chi phí quảng cáo', 'amount' => 3000000],
            ['title' => 'Chi phí bảo hiểm', 'amount' => 4000000],
            ['title' => 'Chi phí hơi xăng dầu', 'amount' => 6000000],
        ];

        $expenseCount = 0;
        $startDate = Carbon::now()->subMonths(3)->startOfDay();
        $endDate = Carbon::now()->endOfDay();

        for ($date = clone $startDate; $date->lte($endDate); $date->addDay()) {
            // Thống nhất chi phí theo ngày trong tuần
            $dayOfWeek = $date->dayOfWeek;

            // Đầu tháng: chi lương
            if ($date->day >= 1 && $date->day <= 5) {
                Expense::create([
                    'title' => 'Lương nhân viên tháng ' . $date->format('m/Y'),
                    'amount' => 50000000,
                    'expense_date' => $date->format('Y-m-d'),
                    'note' => 'Lương định kỳ',
                    'user_id' => $adminUser->id,
                    'created_at' => $date->copy()->addHours(9),
                    'updated_at' => $date->copy()->addHours(9),
                ]);
                $expenseCount++;
            }

            // Cuối tháng (ngày 25-28): chi phí khác
            if ($date->day >= 25 && $date->day <= 28) {
                foreach (['Chi phí điện', 'Chi phí vận chuyển'] as $title) {
                    $this->command->warn('    Ngày: ' . $date->format('Y-m-d'));
                    Expense::create([
                        'title' => $title,
                        'amount' => rand(2000000, 5000000),
                        'expense_date' => $date->format('Y-m-d'),
                        'note' => 'Chi phí hàng tháng',
                        'user_id' => $adminUser->id,
                        'created_at' => $date->copy()->addHours(14),
                        'updated_at' => $date->copy()->addHours(14),
                    ]);
                    $expenseCount++;
                }
            }
        }

        $this->command->info("  ✓ Tạo {$expenseCount} Expense thành công");
    }
}
