<?php

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

