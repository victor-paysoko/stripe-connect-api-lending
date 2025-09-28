<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CB\ConfigController;
use App\Http\Controllers\CB\PaymentIntentsController;
use App\Http\Controllers\CB\StripeWebhookController;
use App\Http\Controllers\CB\FiAccountsController;
use App\Http\Controllers\Api\FundingController;
use App\Http\Controllers\CB\BorrowersController;
use App\Http\Controllers\CB\CustomersController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// Route::prefix('v1')->group(function () {
//     Route::get('/stripe/config', [ConfigController::class, 'show']);

//     Route::get('/fis/{fiId}/stripe-account/health', [FiAccountsController::class, 'health']);

//     Route::post('/fis/{fiId}/repayments/payment-intent', [PaymentIntentsController::class, 'create']);

//     // retrieve PI to poll status
//     Route::get('/repayments/payment-intent/{piId}', [PaymentIntentsController::class, 'retrieve']);

//     Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle']);

//     // List connected accounts
//     Route::get('/stripe/connected-accounts', [\App\Http\Controllers\CB\FiAccountsController::class, 'listConnected']);

//     // Get one connected account by its acct_ id
//     Route::get('/stripe/connected-accounts/{accountId}', [\App\Http\Controllers\CB\FiAccountsController::class, 'getConnected']);

//     Route::get('/v1/stripe/customers/search', [CustomersController::class, 'search']);

//     Route::post('/v1/fis/{fiId}/borrowers/{borrowerId}/stripe-customer/link', [BorrowersController::class, 'linkStripeCustomer']);
// });

Route::prefix('v1')->group(function () {
    // Health & Config
    Route::get('/stripe/config', [ConfigController::class, 'show']);
    Route::get('/fis/{fiId}/stripe-account/health', [FiAccountsController::class, 'health']);

    // Core Payment Flow (Combined)
    Route::post('/fis/{fiId}/repayments', [PaymentIntentsController::class, 'createOrConfirm']);

    // Payment Recovery & Status
    Route::get('/repayments/{repaymentAttemptId}', [PaymentIntentsController::class, 'getStatus']);
    Route::post('/repayments/{repaymentAttemptId}/actions', [PaymentIntentsController::class, 'handleAction']);

    // Webhook (Critical)
    Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle']);

    // Customer Management
    Route::get('/fis/{fiId}/customers/{borrowerId}/payment-methods', [CustomersController::class, 'getPaymentMethods']);
    Route::post('/fis/{fiId}/customers/{borrowerId}/setup-intent', [CustomersController::class, 'createSetupIntent']);

    // Admin/Internal
    Route::get('/stripe/connected-accounts', [FiAccountsController::class, 'listConnected']);
    Route::get('/stripe/connected-accounts/{accountId}', [FiAccountsController::class, 'getConnected']);
});




Route::get('/ping', fn() => response()->json(['pong' => true]));



// Standalone Funding API
Route::post('funding/fund-account', [FundingController::class, 'fundAccount']);
Route::post('funding/fund-account-test', [FundingController::class, 'fundAccountTest']);
Route::get('funding/balance/{lenderAccountId}', [FundingController::class, 'getBalance']);
Route::get('funding/transactions/{lenderAccountId}', [FundingController::class, 'getRecentTransactions']);
Route::get('funding/history/{lenderAccountId}', [FundingController::class, 'getFundingHistory']);
Route::get('funding/can-fund/{lenderAccountId}', [FundingController::class, 'canFundAccount']);

// Payout API (Dynamic - auto-detects sandbox vs production)
Route::post('payout/initiate', [FundingController::class, 'initiatePayout']);

// Direct charge endpoint
Route::post('funding/direct-charge', [FundingController::class, 'directCharge']);

// Test endpoint
Route::get('/', function () {
    return response()->json([
        'message' => 'Stripe Connect Funding API',
        'version' => '1.0.0',
        'endpoints' => [
            'POST /api/funding/fund-account',
            'POST /api/funding/fund-account-test',
            'GET /api/funding/balance/{lenderAccountId}',
            'GET /api/funding/transactions/{lenderAccountId}',
            'GET /api/funding/history/{lenderAccountId}',
            'GET /api/funding/can-fund/{lenderAccountId}',
            'POST /api/payout/initiate (Dynamic - auto-detects sandbox vs production)',
            'POST /api/funding/direct-charge'
        ],
        'documentation' => 'See FUNDING_API_TESTS.md for complete API documentation'
    ]);
});
