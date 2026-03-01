<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MaterialController;

Route::apiResource('materials', MaterialController::class);
?>