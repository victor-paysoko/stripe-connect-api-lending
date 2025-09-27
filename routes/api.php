<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CB\ConfigController;
use App\Http\Controllers\CB\PaymentIntentsController;
use App\Http\Controllers\CB\StripeWebhookController;
use App\Http\Controllers\CB\FiAccountsController;
use App\Http\Controllers\Api\FundingController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::prefix('v1')->group(function () {
    Route::get('/stripe/config', [ConfigController::class, 'show']);

    Route::get('/fis/{fiId}/stripe-account/health', [FiAccountsController::class, 'health']);

    Route::post('/fis/{fiId}/repayments/payment-intent', [PaymentIntentsController::class, 'create']);

    Route::get('/repayments/payment-intent/{piId}', [PaymentIntentsController::class, 'retrieve']);

    // Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle']);
});


Route::get('/ping', fn() => response()->json(['pong' => true]));



// Standalone Funding API
Route::post('funding/fund-account', [FundingController::class, 'fundAccount']);
Route::post('funding/fund-account-test', [FundingController::class, 'fundAccountTest']);
Route::get('funding/balance/{lenderAccountId}', [FundingController::class, 'getBalance']);
Route::get('funding/history/{lenderAccountId}', [FundingController::class, 'getFundingHistory']);
Route::get('funding/can-fund/{lenderAccountId}', [FundingController::class, 'canFundAccount']);

// Test endpoint
Route::get('/', function () {
    return response()->json([
        'message' => 'Stripe Connect Funding API',
        'version' => '1.0.0',
        'endpoints' => [
            'POST /api/funding/fund-account',
            'GET /api/funding/balance/{lenderAccountId}',
            'GET /api/funding/history/{lenderAccountId}',
            'GET /api/funding/can-fund/{lenderAccountId}'
        ],
        'documentation' => 'See FUNDING_API_TESTS.md for complete API documentation'
    ]);
});

