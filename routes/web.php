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

// Students Management
Route::resource('student', \App\Http\Controllers\StudentsManagementController::class);

Route::prefix('madrasa')->group(function (){
    Route::resource('student', \App\Http\Controllers\Madrasa\StudentsController::class, ['as' => 'madrasa']);
});
Route::prefix('polytechnic')->group(function (){
    Route::resource('student', \App\Http\Controllers\Ploytechnic\StudentsController::class, ['as' => 'polytechnic']);
    Route::resource('result', \App\Http\Controllers\ResultController::class, ['as' => 'polytechnic']);
});


// Madrasa Management
Route::resource('madrasa', \App\Http\Controllers\MadrasaController::class);

// Polytechnic
Route::resource('polytechnic', \App\Http\Controllers\PolytechnicController::class);

// Trade
Route::resource('trade', \App\Http\Controllers\TradeController::class);

// Trade
Route::resource('teacher', \App\Http\Controllers\TeacherController::class);

// Trade
Route::resource('document', \App\Http\Controllers\DocumentEditorController::class);

// Trade
Route::resource('academic_session', \App\Http\Controllers\AcademicSessionController::class);

// Trade
Route::get('student_list', [\App\Http\Controllers\ResultController::class, "studentList"])->name('student_list');

// Fee Management
Route::resource('fee', \App\Http\Controllers\FeeController::class);

// Invoice Management
Route::resource('invoice', \App\Http\Controllers\InvoiceController::class);
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
Route::get('get_active_conversation/{target}', [ConversationController::class, 'get_active_conversation'])->name('get_active_conversation');


// Blog
Route::resource('post', \App\Http\Controllers\Blog\PostController::class);
Route::resource('page', \App\Http\Controllers\Blog\PageController::class);
Route::resource('category', \App\Http\Controllers\Blog\CategoryController::class);
Route::resource('tag', \App\Http\Controllers\Blog\TagsController::class);
Route::get('category_options', [\App\Http\Controllers\Blog\CategoryController::class, 'getCategory'])->name('category.get');
Route::get('tag_options', [\App\Http\Controllers\Blog\CategoryController::class, 'getTag'])->name('tag.get');
Route::post('tag_options', [\App\Http\Controllers\Blog\CategoryController::class, 'createTag'])->name('tag.insert');
Route::post('fileUpload', [\App\Http\Controllers\Blog\PostController::class, 'fileUpload'])->name('post.fileupload');

Route::get('related_post', [\App\Http\Controllers\Blog\PostController::class, 'postByCategories'])->name('post.related_post');

// CKFinder
Route::any('/ckfinder/connector', '\CKSource\CKFinderBridge\Controller\CKFinderController@requestAction')
    ->name('ckfinder_connector');

Route::any('/ckfinder/browser', '\CKSource\CKFinderBridge\Controller\CKFinderController@browserAction')
    ->name('ckfinder_browser');
Route::any('/ckfinder/examples/{example?}', 'CKSource\CKFinderBridge\Controller\CKFinderController@examplesAction')
    ->name('ckfinder_examples');


// openssl test
Route::get('openssl', [\App\Http\Controllers\OpensslManagementController::class, 'generate']);
Route::get('openssl/encrypt', [\App\Http\Controllers\OpensslManagementController::class, 'publicEncrypt']);
Route::get('openssl/decrypt', [\App\Http\Controllers\OpensslManagementController::class, 'privateDecrypt']);


// Inbox
Route::get('inbox', [\App\Http\Controllers\InboxController::class, 'inbox'])->name('inbox');

// page
Route::get('{slug}', [\App\Http\Controllers\Blog\PageController::class, 'show']);
