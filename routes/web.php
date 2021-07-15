<?php

use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\UserRoleController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/auth.php';

Route::resource('roles', RolesController::class);
Route::resource('permission', PermissionController::class);

Route::put('user_role', UserRoleController::class)->name('put_user_role');
Route::get('user_role', [UserRoleController::class, "index"])->name('user_role_index');
Route::get('get_user', [UserRoleController::class, "getUser"])->name('get_user');
Route::get('get_permission', [UserRoleController::class, "getPermission"])->name('get_permission');
Route::get('get_roles', [UserRoleController::class, "getRoles"])->name('get_roles');
Route::get('user_permission/{user}', [UserRoleController::class, "userPermission"])->name('user_permission');
Route::get('user_roles/{user}', [UserRoleController::class, "userRoles"])->name('user_roles');
Route::resource('users', \App\Http\Controllers\UserController::class);
