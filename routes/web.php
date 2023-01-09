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

use App\Http\Controllers\BkashController;
use Illuminate\Support\Facades\Route;


Route::get('bkash', [BkashController::class, 'index'])->name('index');
// Route::group(['middleware' => ['customAuth']], function () {

// Payment Routes for bKash
// Route::post('bkash', 'BkashController@index')->name('index');
Route::post('bkash/get-token', 'BkashController@getToken')->name('bkash-get-token');
Route::post('bkash/create-payment', 'BkashController@createPayment')->name('bkash-create-payment');
Route::post('bkash/execute-payment', 'BkashController@executePayment')->name('bkash-execute-payment');
Route::get('bkash/query-payment', 'BkashController@queryPayment')->name('bkash-query-payment');
Route::post('bkash/success', 'BkashController@bkashSuccess')->name('bkash-success');

// });
