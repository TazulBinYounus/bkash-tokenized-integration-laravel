<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BkashController;

Auth::routes();

Route::get('/', function () {
    return redirect()->route('home');
});
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


Route::group(['middleware' => ['auth']], function () {
    Route::get('bkash', [BkashController::class, 'index'])->name('bkash');
    Route::post('bkash/get-token', [BkashController::class, 'getToken'])->name('bkash-get-token');
    //agreement
    Route::post('bkash/create-agrement', [BkashController::class, 'createAgrement'])->name('bkash-create-agrement');
    Route::post('bkash/execute-agrement', [BkashController::class, 'executeAgrement'])->name('bkash-execute-agrement');
    Route::post('agreement-callback', [BkashController::class, 'agreementCallback']);
    Route::get('agreement-callback', [BkashController::class, 'agreementCallback']);
    Route::get('remove-agreent/{id}', [BkashController::class, 'removeAgreent'])->name('remove-agreent');


    //payment
    Route::post('bkash/create-payment', [BkashController::class, 'createPayment'])->name('bkash-create-payment');
    Route::post('bkash/execute-payment', [BkashController::class, 'executePayment'])->name('bkash-execute-payment');
    Route::post('payment-callback', [BkashController::class, 'paymentCallback']);
    Route::get('payment-callback', [BkashController::class, 'paymentCallback']);

    // Payment Routes for bKash
    Route::get('bkash/query-payment', 'BkashController@queryPayment')->name('bkash-query-payment');
    Route::post('bkash/success', 'BkashController@bkashSuccess')->name('bkash-success');
});
