<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('register', [\App\Http\Controllers\Api\UserController::class, 'register']);
Route::post('login', [\App\Http\Controllers\Api\UserController::class, 'login']);

Route::middleware('auth:sanctum')->group(function (){
    Route::resource('result', \App\Http\Controllers\Api\ResultController::class);
    Route::post('token', [\App\Http\Controllers\Api\PushNotificationController::class, 'store']);
    Route::resource('payment_slip', \App\Http\Controllers\Api\PaymentSlipControllerApi::class, ['as' => 'api_payment_slip']);
    Route::get('fee_type', [\App\Http\Controllers\Api\PaymentSlipControllerApi::class, 'getFeeType'])->name('get_fee_type');
});

