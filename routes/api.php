<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FundingController;

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
