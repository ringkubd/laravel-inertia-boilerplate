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
Route::post('instructor_login', [\App\Http\Controllers\Api\UserController::class, 'instructor_login']);

Route::middleware('auth:sanctum')->group(function (){
    Route::apiResource('result', \App\Http\Controllers\Api\ResultController::class);
    Route::post('token', [\App\Http\Controllers\Api\PushNotificationController::class, 'store']);
    Route::resource('payment_slip', \App\Http\Controllers\Api\PaymentSlipControllerApi::class, ['as' => 'api_payment_slip']);
    Route::get('fee_type', [\App\Http\Controllers\Api\PaymentSlipControllerApi::class, 'getFeeType'])->name('get_fee_type');
    Route::apiResource('madrasah_result', \App\Http\Controllers\Api\MadrasahResultController::class);
    Route::apiResource('notices', \App\Http\Controllers\Api\NotificationApiController::class);

    Route::get('attendance', [\App\Http\Controllers\Api\TeacherAttendanceController::class , 'all'])->name('attendance.all');
    Route::post('attendance', [\App\Http\Controllers\Api\TeacherAttendanceController::class , 'store'])->name('attendance.store');
    Route::get('attendance/today', [\App\Http\Controllers\Api\TeacherAttendanceController::class , 'today'])->name('attendance.today');
    Route::get('madrasah_location', [\App\Http\Controllers\Api\TeacherAttendanceController::class , 'madrasah_location'])->name('attendance.madrasah');
    Route::post('store_public_key', [\App\Http\Controllers\Api\UserController::class , 'store_public_Key'])->name('users.store_public_key');
    Route::post('signature_verification', [\App\Http\Controllers\Api\UserController::class , 'isValid'])->name('signature.validation');
});

