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
    Route::post('logout', [\App\Http\Controllers\Api\UserController::class, 'logout']);
    Route::post('refresh-token', [\App\Http\Controllers\Api\UserController::class, 'refreshToken']);
    Route::apiResource('result', \App\Http\Controllers\Api\ResultController::class);
    Route::post('token', [\App\Http\Controllers\Api\PushNotificationController::class, 'store']);
    Route::resource('payment_slip', \App\Http\Controllers\Api\PaymentSlipControllerApi::class, ['as' => 'api_payment_slip']);
    Route::get('fee_type', [\App\Http\Controllers\Api\PaymentSlipControllerApi::class, 'getFeeType'])->name('get_fee_type');
    Route::apiResource('madrasah_result', \App\Http\Controllers\Api\MadrasahResultController::class);
    Route::apiResource('notices', \App\Http\Controllers\Api\NotificationApiController::class);

    Route::get('attendance', [\App\Http\Controllers\Api\TeacherAttendanceController::class , 'all'])->name('attendance.all');
    Route::post('attendance', [\App\Http\Controllers\Api\TeacherAttendanceController::class , 'store'])->name('attendance.store');
    Route::get('attendance/today', [\App\Http\Controllers\Api\TeacherAttendanceController::class , 'today'])->name('attendance.today');
    Route::get('attendance/today-status', [\App\Http\Controllers\Api\TeacherAttendanceController::class , 'todayStatus'])->name('attendance.today-status');
    Route::get('madrasah_location', [\App\Http\Controllers\Api\TeacherAttendanceController::class , 'madrasah_location'])->name('attendance.madrasah');
    Route::post('store_public_key', [\App\Http\Controllers\Api\UserController::class , 'store_public_Key'])->name('users.store_public_key');
    Route::post('signature_verification', [\App\Http\Controllers\Api\UserController::class , 'isValid'])->name('signature.validation');

    // Face Verification & Enrollment
    Route::get('face/status', [\App\Http\Controllers\Api\Teacher\FaceVerificationController::class, 'status']);
    Route::post('face/enroll', [\App\Http\Controllers\Api\Teacher\FaceVerificationController::class, 'enroll']);
    Route::post('face/verify', [\App\Http\Controllers\Api\Teacher\FaceVerificationController::class, 'verify']);
    Route::delete('face/data', [\App\Http\Controllers\Api\Teacher\FaceVerificationController::class, 'deleteFaceData']);

    // Madrasah Location Management
    Route::get('madrasah/my-location', [\App\Http\Controllers\Api\Teacher\FaceVerificationController::class, 'getMadrasahLocation']);
    Route::put('madrasah/location', [\App\Http\Controllers\Api\Teacher\FaceVerificationController::class, 'updateMadrasahLocation']);

    // Teacher Message
    Route::group(['prefix' => 'teacher'],function (){
        Route::resource('message', \App\Http\Controllers\Teacher\TeacherMessageController::class);
        Route::resource('group', \App\Http\Controllers\Teacher\TeacherMessageGroupController::class);
        Route::post('store_fcm_token', [\App\Http\Controllers\Teacher\TeacherMessageController::class, 'storeFcmToken']);
        Route::get('conversation', [\App\Http\Controllers\Teacher\TeacherMessageController::class, 'conversation']);
        Route::get('conversation_message/{conversation_id}', [\App\Http\Controllers\Teacher\TeacherMessageController::class, 'conversationMessage']);
        Route::post('update_online', [\App\Http\Controllers\Teacher\TeacherMessageGroupController::class, 'updateOnlineStatus']);

        // Teacher App API (Classroom Management, Student Attendance, Stats)
        Route::get('classes', [\App\Http\Controllers\Api\Teacher\ClassController::class, 'index']);
        Route::get('classes/today', [\App\Http\Controllers\Api\Teacher\ClassController::class, 'today']);
        Route::post('classes', [\App\Http\Controllers\Api\Teacher\ClassController::class, 'store']);
        Route::get('classes/{id}', [\App\Http\Controllers\Api\Teacher\ClassController::class, 'show']);
        Route::put('classes/{id}', [\App\Http\Controllers\Api\Teacher\ClassController::class, 'update']);
        Route::delete('classes/{id}', [\App\Http\Controllers\Api\Teacher\ClassController::class, 'destroy']);
        Route::get('classes/{id}/students', [\App\Http\Controllers\Api\Teacher\ClassController::class, 'students']);
        Route::get('classes/{classId}/attendance', [\App\Http\Controllers\Api\Teacher\AttendanceController::class, 'byClass']);
        Route::get('classes/{classId}/attendance/stats', [\App\Http\Controllers\Api\Teacher\AttendanceController::class, 'stats']);
        Route::post('attendance/mark', [\App\Http\Controllers\Api\Teacher\AttendanceController::class, 'mark']);
        Route::get('attendance/history', [\App\Http\Controllers\Api\Teacher\AttendanceController::class, 'history']);
        Route::post('verify-location', [\App\Http\Controllers\Api\Teacher\AttendanceController::class, 'verifyLocation']);
        Route::get('students', [\App\Http\Controllers\Api\Teacher\StudentController::class, 'index']);
        Route::get('students/all', [\App\Http\Controllers\Api\Teacher\StudentController::class, 'teacherStudents']);
        Route::post('students', [\App\Http\Controllers\Api\Teacher\StudentController::class, 'store']);
        Route::get('students/{id}', [\App\Http\Controllers\Api\Teacher\StudentController::class, 'show']);
        Route::put('students/{id}', [\App\Http\Controllers\Api\Teacher\StudentController::class, 'update']);
        Route::delete('students/{id}', [\App\Http\Controllers\Api\Teacher\StudentController::class, 'destroy']);
        Route::get('students/{id}/performance', [\App\Http\Controllers\Api\Teacher\StudentController::class, 'performance']);
        Route::post('students/{id}/enroll', [\App\Http\Controllers\Api\Teacher\StudentController::class, 'enroll']);
        Route::get('stats', [\App\Http\Controllers\Api\Teacher\StatsController::class, 'index']);
        Route::get('report', [\App\Http\Controllers\Api\Teacher\StatsController::class, 'report']);
        Route::get('schedule', [\App\Http\Controllers\Api\Teacher\StatsController::class, 'schedule']);

        // Common Lookups
        Route::get('academic-sessions', [\App\Http\Controllers\Api\Teacher\CommonApiController::class, 'academicSession']);
        Route::get('common-students', [\App\Http\Controllers\Api\Teacher\CommonApiController::class, 'students']);

        // Student Results (Teacher-managed)
        Route::get('results/polytechnic', [\App\Http\Controllers\Api\Teacher\ResultController::class, 'polytechnicIndex']);
        Route::post('results/polytechnic', [\App\Http\Controllers\Api\Teacher\ResultController::class, 'polytechnicStore']);
        Route::put('results/polytechnic/{id}', [\App\Http\Controllers\Api\Teacher\ResultController::class, 'polytechnicUpdate']);
        Route::get('results/madrasah', [\App\Http\Controllers\Api\Teacher\ResultController::class, 'madrasahIndex']);
        Route::post('results/madrasah', [\App\Http\Controllers\Api\Teacher\ResultController::class, 'madrasahStore']);
        Route::put('results/madrasah/{id}', [\App\Http\Controllers\Api\Teacher\ResultController::class, 'madrasahUpdate']);
        Route::delete('results/madrasah/{id}', [\App\Http\Controllers\Api\Teacher\ResultController::class, 'madrasahDestroy']);
    });

    // Teacher Profile (outside teacher prefix for cleaner URL)
    Route::put('user/profile', [\App\Http\Controllers\Api\Teacher\ProfileController::class, 'update']);
    Route::put('user/password', [\App\Http\Controllers\Api\Teacher\ProfileController::class, 'changePassword']);
});

