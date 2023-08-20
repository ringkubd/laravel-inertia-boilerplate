<?php
Route::resource('message', \App\Http\Controllers\Teacher\TeacherMessageController::class);
Route::resource('group', \App\Http\Controllers\Teacher\TeacherMessageGroupController::class);
Route::put('store_fcm_token', [\App\Http\Controllers\Teacher\TeacherMessageController::class, 'storeFcmToken'])->name('store_fcm_token');
