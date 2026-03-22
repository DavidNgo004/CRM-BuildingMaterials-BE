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
?>