<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Authentication routes
Route::post('/login',[AuthController::class,'login']);

Route::middleware('auth:api')->group(function(){

    Route::post('/create-staff',[AuthController::class,'createStaff']);
    Route::get('/profile',[AuthController::class,'profile']);
    Route::get('/staffs',[AuthController::class,'getStaffs']);
    Route::post('/change-password',[AuthController::class,'changePassword']);

});
?>