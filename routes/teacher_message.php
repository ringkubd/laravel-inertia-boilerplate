<?php
Route::resource('message', \App\Http\Controllers\Teacher\TeacherMessageController::class, ['as' => 'teacher']);
Route::resource('group', \App\Http\Controllers\Teacher\TeacherMessageGroupController::class, ['as' => 'teacher']);
Route::put('store_fcm_token', [\App\Http\Controllers\Teacher\TeacherMessageController::class, 'storeFcmToken'])->name('teacher.store_fcm_token');
Route::get('conversation', [\App\Http\Controllers\Teacher\TeacherMessageController::class, 'conversation'])->name('teacher_conversation');
Route::get('update_online', [\App\Http\Controllers\Teacher\TeacherMessageGroupController::class, 'updateOnlineStatus']);
