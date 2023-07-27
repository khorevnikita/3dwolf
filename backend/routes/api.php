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
use App\Http\Controllers\MultipartUploadController;
use App\Http\Controllers\OrderNotificationLogController;
use App\Http\Controllers\RegularPaymentController;
use App\Http\Controllers\BranchController;

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
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('me', [AuthController::class, 'me']);
        Route::post('profile', [AuthController::class, 'profile']);
        Route::post('set-password', [AuthController::class, 'setPassword']);
    });
});

Route::middleware('moderator')->group(function () {
    Route::resources([
        'materials' => MaterialController::class,
        'manufacturers' => ManufacturerController::class,
        'customers' => CustomerController::class,
        'branches' => BranchController::class,
        'accounts' => AccountController::class,
        'estimates' => EstimateController::class,
        'estimates.estimate-lines' => EstimateLineController::class,
        'parts' => PartController::class,
        'orders' => OrderController::class,
        'orders.order-lines' => OrderLineController::class,
        'orders.order-files' => OrderFileController::class,
        'orders.notification-logs' => OrderNotificationLogController::class,
        'contracts' => ContractController::class,
        'users' => UserController::class,
        'payments' => PaymentController::class,
        'regular-payments' => RegularPaymentController::class,
        'newsletters' => NewsletterController::class,
        'prod-number-masks' => ProdNumberMaskController::class,
        'order-notification-templates' => OrderNotificationTemplateController::class,
    ]);

    Route::prefix('money')->group(function () {
        Route::get('statistics', [MoneyController::class, 'getTotalStatistics']);
    });

    Route::prefix('customers/{customer}')->group(function () {
        Route::post('user', [CustomerController::class, 'addUser']);
    });

    Route::prefix('users/{user}')->group(function () {
        Route::post('reset', [UserController::class, 'reset']);
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
    Route::prefix('upload/multipart')->group(function () {
        Route::post('/', [MultipartUploadController::class, 'createMultipartUpload']);
        Route::post('{uploadId}', [MultipartUploadController::class, 'uploadPart']);
        Route::get('{uploadId}', [MultipartUploadController::class, 'getUploadedParts']);
        Route::get('{uploadId}/{partNumber}', [MultipartUploadController::class, 'signPartUpload']);
        Route::post('{uploadId}/complete', [MultipartUploadController::class, 'completeMultipartUpload']);
        Route::delete('{uploadId}', [MultipartUploadController::class, 'abortMultipartUpload']);
    });

    Route::get('company-by-inn', [CustomerController::class, 'finDataByINN']);

    Route::prefix('orders')->group(function () {
        Route::post('{order}/notify', [OrderController::class, 'notify']);
        Route::post('{order}/copy', [OrderController::class, 'copy']);
        Route::post('{order}/order-lines/{orderLine}/copy', [OrderLineController::class, 'copy']);
        Route::post('{order}/set-discount', [OrderController::class, 'setDiscount']);
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('orders', [OrderController::class, 'index']);
    Route::get('orders/{order}', [OrderController::class, 'show']);
    Route::get('orders/{order}/order-lines', [OrderLineController::class, 'index']);
    Route::get('orders/{order}/order-files', [OrderFileController::class, 'index']);
    Route::prefix('money')->group(function () {
        Route::get('dashboard', [MoneyController::class, 'getDashboardData']);
    });
});


Route::prefix('orders')->group(function () {
    Route::get('{order}/qr', [OrderController::class, 'qr']);
    Route::get('{order}/export-auth', [OrderController::class, 'exportAuth'])->middleware('auth:sanctum');
    Route::get('{order}/export/pdf', [OrderController::class, 'exportPDF']);
    Route::get('{order}/export/xlsx', [OrderController::class, 'exportXlsx']);
    Route::get('{order}/export/test', [OrderController::class, 'testExport']);
});
Route::prefix('contracts')->group(function () {
    Route::get('{contract}/export-auth', [ContractController::class, 'exportAuth'])->middleware('auth:sanctum');
    Route::get('{contract}/export/pdf', [ContractController::class, 'exportPDF']);
    Route::get('{contract}/export/test', [ContractController::class, 'testExport']);
});
Route::prefix('parts')->group(function () {
    Route::get('{part}/export/auth', [PartController::class, 'exportAuth'])->middleware('auth:sanctum');
    Route::get('{part}/export/sticker', [PartController::class, 'exportSticker']);
});
