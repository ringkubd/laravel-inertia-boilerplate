<?php

Route::middleware(['web'])->group(function (){
    Route::get('auto-login', [\Anwar\AutoLogin\Controllers\AutoLoginController::class, 'autoLogin'])->name('auto-login');

    Route::post('auto-login/generate_token', [\Anwar\AutoLogin\Controllers\AutoLoginController::class, 'generateToken'])->name('auto-login-token');
});
