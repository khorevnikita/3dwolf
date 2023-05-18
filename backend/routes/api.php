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
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\ProdNumberMaskController;
use App\Http\Controllers\OrderFileController;
use App\Http\Controllers\OrderNotificationTemplateController;
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
    Route::get('me', [AuthController::class, 'me']);
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
        'orders.order-files' => OrderFileController::class,
        'contracts' => ContractController::class,
        'users' => UserController::class,
        'payments' => PaymentController::class,
        'newsletters' => NewsletterController::class,
        'prod-number-masks' => ProdNumberMaskController::class,
        'order-notification-templates' => OrderNotificationTemplateController::class,
    ]);

    Route::prefix('money')->group(function () {
        Route::get('statistics', [MoneyController::class, 'getTotalStatistics']);
        Route::get('dashboard', [MoneyController::class, 'getDashboardData']);
    });

    Route::prefix('newsletters/{newsletter}')->group(function () {
        Route::get('available-receivers', [NewsletterController::class, 'availableReceivers']);
        Route::post('attach-all', [NewsletterController::class, 'attachAll']);
        Route::post('attach/{customer}', [NewsletterController::class, 'attachCustomer']);
        Route::get('attached-receivers', [NewsletterController::class, 'attachedReceivers']);
        Route::post('detach-all', [NewsletterController::class, 'detachAll']);
        Route::post('detach/{customer}', [NewsletterController::class, 'detachCustomer']);
        Route::post('send', [NewsletterController::class, 'send']);
    });

    Route::post('upload', [UploadController::class, 'upload']);
});

Route::prefix('orders')->group(function () {
    Route::get('{order}/export-auth', [OrderController::class, 'exportAuth'])->middleware('auth:sanctum');
    Route::get('{order}/export/pdf', [OrderController::class, 'exportPDF']);
    Route::get('{order}/export/xlsx', [OrderController::class, 'exportXlsx']);
    Route::get('{order}/export/test', [OrderController::class, 'testExport']);
    Route::post('{order}/copy', [OrderController::class, 'copy']);
    Route::post('{order}/order-lines/{orderLine}/copy', [OrderLineController::class, 'copy']);
});
Route::prefix('contracts')->group(function () {
    Route::get('{contract}/export-auth', [ContractController::class, 'exportAuth'])->middleware('auth:sanctum');
    Route::get('{contract}/export/pdf', [ContractController::class, 'exportPDF']);
    Route::get('{contract}/export/test', [ContractController::class, 'testExport']);
});
