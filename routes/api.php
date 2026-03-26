<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;

// Authentication routes
Route::post('/login',[AuthController::class,'login']);
// Protected Auth routes
Route::middleware('auth:api')->group(function(){
    Route::post('/create-staff',[AuthController::class,'createStaff']);
    Route::get('/profile',[AuthController::class,'profile']);
    Route::get('/staffs',[AuthController::class,'getStaffs']);
    Route::post('/change-password',[AuthController::class,'changePassword']);
});
// Product routes
Route::middleware('auth:api')->group(function(){
    Route::get('/products',[ProductController::class,'index']);
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

// Import routes
use App\Http\Controllers\ImportController;
Route::middleware('auth:api')->group(function(){
    Route::apiResource('imports', ImportController::class)->except(['update']);
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

// Dashboard routes
use App\Http\Controllers\DashboardController;
Route::middleware('auth:api')->prefix('dashboard')->group(function(){
    Route::get('/kpi-cards',         [DashboardController::class, 'kpiCards']);
    Route::get('/charts',            [DashboardController::class, 'charts']);
    Route::get('/recent-activities', [DashboardController::class, 'recentActivities']);
    Route::get('/alerts',            [DashboardController::class, 'alerts']);
    Route::get('/mini-reports',      [DashboardController::class, 'miniReports']);
});
?>