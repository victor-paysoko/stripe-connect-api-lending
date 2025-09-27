<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CB\ConfigController;
use App\Http\Controllers\CB\PaymentIntentsController;
use App\Http\Controllers\CB\StripeWebhookController;
use App\Http\Controllers\CB\FiAccountsController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::prefix('v1')->group(function () {
    Route::get('/stripe/config', [ConfigController::class, 'show']);

    // Verify an FI’s connected account is ready (capabilities/charges_enabled)
    Route::get('/fis/{fiId}/stripe-account/health', [FiAccountsController::class, 'health']);

    // Create PaymentIntent for a repayment (Payment Element flow)
    Route::post('/fis/{fiId}/repayments/payment-intent', [PaymentIntentsController::class, 'create']);

    // (Optional) retrieve PI to poll status
    Route::get('/repayments/payment-intent/{piId}', [PaymentIntentsController::class, 'retrieve']);

    Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle']);
});


Route::get('/ping', fn() => response()->json(['pong' => true]));
