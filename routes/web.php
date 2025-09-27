<?php

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
