<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ManufacturerController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\CustomerController;
use \App\Http\Controllers\AccountController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\EstimateController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::resources([
        'materials' => MaterialController::class,
        'manufacturers' => ManufacturerController::class,
        'customers' => CustomerController::class,
        'accounts' => AccountController::class,
        'estimates' => EstimateController::class,
        'orders' => OrderController::class,
    ]);
});
