<?php

use App\Http\Controllers\CB\FiAccountsController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test', function () {
    return '<h1>Test Route</h1>';
});

Route::get('/disbursement', function () {
    return view('disbursement');
})->name('disbursement');

 Route::prefix('payments')->name('payments.')->group(function () {

        Route::get('/create', [PaymentController::class, 'create'])->name('create');

        Route::get('/success', [PaymentController::class, 'success'])->name('success');

        Route::post('/add', [PaymentController::class, 'add'])->name('add');
    });

 Route::prefix('onboarding')->name('onboarding.')->group(function () {
        Route::get('/', [OnboardingController::class, 'index'])->name('index');
        Route::post('/stripe/start', [OnboardingController::class, 'startStripeOnboarding'])->name('stripe.start');
        Route::get('/stripe/return', [OnboardingController::class, 'stripeReturn'])->name('stripe.return');
        Route::get('/stripe/refresh', [OnboardingController::class, 'stripeRefresh'])->name('stripe.refresh');
        Route::get('/stripe/dashboard', [OnboardingController::class, 'stripeDashboard'])->name('stripe.dashboard');
        Route::get('/complete', [OnboardingController::class, 'complete'])->name('complete');
    });



 Route::prefix('fi')->name('fi.')->group(function () {
        Route::get('/', [FiAccountsController::class, 'all'])->name('all');
 });
