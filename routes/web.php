<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BkashController;


Route::get('bkash', [BkashController::class, 'index'])->name('index');
Route::post('bkash/get-token', [BkashController::class, 'getToken'])->name('bkash-get-token');
Route::post('bkash/create-payment', [BkashController::class, 'createPayment'])->name('bkash-create-payment');
Route::post('bkash/create-agrement', [BkashController::class, 'createAgrement'])->name('bkash-create-agrement');
Route::post('bkash/execute-agrement', [BkashController::class, 'executeAgrement'])->name('bkash-execute-agrement');

// Payment Routes for bKash
Route::post('bkash/execute-payment', 'BkashController@executePayment')->name('bkash-execute-payment');
Route::get('bkash/query-payment', 'BkashController@queryPayment')->name('bkash-query-payment');
Route::post('bkash/success', 'BkashController@bkashSuccess')->name('bkash-success');
// Route::group(['middleware' => ['customAuth']], function () {


// });
