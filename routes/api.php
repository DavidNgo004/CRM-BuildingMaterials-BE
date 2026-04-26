<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ImportExcelController;

// Authentication routes
Route::post('/login',[AuthController::class,'login']);
// Protected Auth routes
Route::middleware('auth:api')->group(function(){
    Route::post('/create-staff',[AuthController::class,'createStaff']);
    Route::put('/staffs/{id}',[AuthController::class,'updateStaff']);
    Route::delete('/staffs/{id}',[AuthController::class,'deleteStaff']);
    Route::get('/profile',[AuthController::class,'profile']);
    Route::get('/staffs',[AuthController::class,'getStaffs']);
    Route::post('/change-password',[AuthController::class,'changePassword']);
});
// Product routes
Route::middleware('auth:api')->group(function(){
    Route::get('/products/export/excel', [ProductController::class, 'exportExcel']);
    Route::get('/products/import/template', [ProductController::class, 'downloadTemplateExcel']);
    Route::post('/products/import/excel', [ProductController::class, 'importExcel']);
    Route::get('/products',[ProductController::class,'index']);
    Route::get('/products/{id}',[ProductController::class,'show']);
    Route::post('/products',[ProductController::class,'store']);
    Route::put('/products/{id}',[ProductController::class,'update']);
    Route::delete('/products/{id}',[ProductController::class,'destroy']);
});

// Supplier routes
use App\Http\Controllers\SupplierController;
Route::middleware('auth:api')->group(function(){
    Route::apiResource('suppliers', SupplierController::class);
});

// Customer routes
use App\Http\Controllers\CustomerController;
Route::middleware('auth:api')->group(function(){
    Route::apiResource('customers', CustomerController::class);
});

// Import Excel routes (phải đặt TRƯỚC apiResource để tránh /imports/{id} bắt nhầm)
Route::middleware(['auth:api', 'role:admin,warehouse'])->group(function () {
    Route::post('/imports/excel', [ImportExcelController::class, 'import']);
    Route::get('/imports/excel/template', [ImportExcelController::class, 'downloadTemplate']);
});

// Import routes
use App\Http\Controllers\ImportController;
Route::middleware('auth:api')->group(function(){
    Route::apiResource('imports', ImportController::class)->except(['update']);
    Route::put('imports/{id}', [ImportController::class, 'update']);
    Route::put('imports/{import}/status', [ImportController::class, 'changeStatus']);
});


// Export routes
use App\Http\Controllers\ExportController;
Route::middleware(['auth:api', 'role:admin,warehouse_staff'])->group(function(){
    Route::apiResource('exports', ExportController::class)->except(['update', 'destroy']);
    Route::delete('exports/{export}', [ExportController::class, 'destroy'])->middleware('role:admin');
    Route::put('exports/{export}/status', [ExportController::class, 'changeStatus']);
});

// Expense routes
use App\Http\Controllers\ExpenseController;
Route::middleware(['auth:api', 'role:admin'])->group(function(){
    Route::apiResource('expenses', ExpenseController::class);
});

// Inventory Logs routes
use App\Http\Controllers\InventoryLogController;
Route::middleware('auth:api')->group(function(){
    Route::get('/inventory-logs', [InventoryLogController::class, 'index']);
});

// Dashboard routes
use App\Http\Controllers\DashboardController;
Route::middleware('auth:api')->prefix('dashboard')->group(function(){
    Route::get('/kpi-cards',         [DashboardController::class, 'kpiCards']);
    Route::get('/charts',            [DashboardController::class, 'charts']);
    Route::get('/recent-activities', [DashboardController::class, 'recentActivities']);
    Route::get('/alerts',            [DashboardController::class, 'alerts']);
    Route::get('/mini-reports',      [DashboardController::class, 'miniReports']);
    Route::get('/summary',           [DashboardController::class, 'summary']);
});

Route::prefix('test-dashboard')->group(function(){
    Route::get('/kpi-cards',         [DashboardController::class, 'kpiCards']);
    Route::get('/charts',            [DashboardController::class, 'charts']);
    Route::get('/recent-activities', [DashboardController::class, 'recentActivities']);
    Route::get('/alerts',            [DashboardController::class, 'alerts']);
    Route::get('/mini-reports',      [DashboardController::class, 'miniReports']);
});

// Reports routes
use App\Http\Controllers\ReportController;
Route::middleware('auth:api')->group(function(){
    Route::get('/reports', [ReportController::class, 'index']);
    Route::post('/reports', [ReportController::class, 'store']);
    Route::put('/reports/{id}/seen', [ReportController::class, 'markSeen'])->middleware('role:admin');
    Route::put('/reports/{id}/reply', [ReportController::class, 'reply'])->middleware('role:admin');
});
?>