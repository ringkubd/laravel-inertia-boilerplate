<?php

use App\Http\Controllers\ConversationController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\UserOfflineController;
use App\Http\Controllers\UserOnlineController;
use App\Http\Controllers\UserRoleController;
use App\Models\User;
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

Route::put('user_role', UserRoleController::class)->name('user_role.put');
Route::get('user_role', [UserRoleController::class, "index"])->name('user_role.index');
Route::get('get_user', [UserRoleController::class, "getUser"])->name('users.get');
Route::get('get_permission', [UserRoleController::class, "getPermission"])->name('permissions.get');
Route::get('get_roles', [UserRoleController::class, "getRoles"])->name('roles.get');
Route::get('user_permission/{user}', [UserRoleController::class, "userPermission"])->name('users.permission');
Route::get('user_roles/{user}', [UserRoleController::class, "userRoles"])->name('users.roles');
Route::resource('users', \App\Http\Controllers\UserController::class);
// Sync Roles Permissions
Route::get('role_permission/{role?}', [\App\Http\Controllers\RolePermissionController::class, 'get'])->name('roles.permissions');
Route::put('role_permission/{role}', [\App\Http\Controllers\RolePermissionController::class, 'update'])->name('roles.permissions_update');
Route::get('role_get_permission/{role}', [\App\Http\Controllers\RolePermissionController::class, 'getRolePermissions'])->name('roles.get_permissions');

// Update User Permissions
Route::get('user_direct_permission/{user?}', [\App\Http\Controllers\UserPermissionController::class, 'index'])->name('users.direct_permission');
Route::put('user_direct_permission_update/{user}', [\App\Http\Controllers\UserPermissionController::class, 'updateUserPermissions'])->name('users.direct_permission_update');
Route::get('user_permission_json/{user}', [\App\Http\Controllers\UserPermissionController::class, "getUserPermissions"])->name('users.permission_json');


// Test

Route::get('conv', function(){
    //dd(conversation(2));
    $user = User::where('online', 0)
        ->where('id','!=', request()->user()->id?? "")
        ->with(['conversation' => function ($q){
            $q->whereHas('conversationUsers', function ($q) {
                $q->where('user_id',  request()->user()->id ?? "");
            });
        }])
        ->get();
    dd($user);
});

//User Online Status
Route::put('online/{user}', UserOnlineController::class)->name('online');
Route::put('offline/{user}', UserOfflineController::class)->name('offline');

// conversation

Route::resource('conversation', ConversationController::class);


// Blog
Route::resource('post', \App\Http\Controllers\Blog\PostController::class);
Route::resource('category', \App\Http\Controllers\Blog\CategoryController::class);
Route::get('category_options', [\App\Http\Controllers\Blog\CategoryController::class, 'getCategory'])->name('category.get');
