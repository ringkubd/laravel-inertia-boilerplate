<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;

class UserPermissionController extends Controller
{
    public function __constructor(){
        $this->middleware(['role:super admin']);
    }
    public function index($user = ""){
        if ($user != "") {
            $users = User::where('id', $user)->get();
        }else{
            $users = User::all();
        }
        $permissions = Permission::select('permissions.*',DB::raw('substring_index(name, "_", -1) as permission_group'))->get()->groupBy('module');
        return Inertia::render('UserPermission/Index', [
            'users' => $users,
            'permissions' => $permissions
        ]);
    }

    public function updateUserPermissions(Request $request, User $user){
        if ($user->hasPermissionTo($request->permissions)){
            $user->revokePermissionTo($request->permissions);
            if ($user->hasPermissionTo($request->permissions)) {
                return false;
            }
        }else{
            $permissions = Permission::find($request->permissions);
            $user->givePermissionTo($permissions);
        }
        return $user;
    }

    /**
     * @param User $user
     * @return string
     */

    public function getUserPermissions(User $user){
        return $user->getAllPermissions()->pluck('id')->toJson();
    }
}
