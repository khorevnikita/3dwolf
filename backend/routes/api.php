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
use App\Http\Controllers\EstimateLineController;
use App\Http\Controllers\PartController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\OrderLineController;
use App\Http\Controllers\MoneyController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PaymentController;

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
        'estimates.estimate-lines' => EstimateLineController::class,
        'parts' => PartController::class,
        'orders' => OrderController::class,
        'orders.order-lines' => OrderLineController::class,
        'contracts' => ContractController::class,
        'users' => UserController::class,
        'payments' => PaymentController::class,
    ]);

    Route::prefix('money')->group(function () {
        Route::get('statistics', [MoneyController::class, 'getTotalStatistics']);
    });
});

Route::prefix('orders')->group(function () {
    Route::get('{order}/export-auth', [OrderController::class, 'exportAuth'])->middleware('auth:sanctum');
    Route::get('{order}/export', [OrderController::class, 'export']);
});
