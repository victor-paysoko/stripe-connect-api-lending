<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FundingController;

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
