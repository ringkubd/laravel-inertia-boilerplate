<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionController extends Controller
{
    public function __constructor(){
        $this->middleware(['role:super admin']);
    }
    /**
     * @param Request $request
     * @param $role
     * @return \Inertia\Response
     */
    public function get(Request $request, $role){
        $this->authorize('update_role');
        if ($role) {
            $roles = Role::where('id', $role)->get();
        }else{
            $roles = Role::all();
        }
        $permissions = Permission::query()
            ->select('permissions.*',DB::raw('substring_index(name, "_", -1) as permission_group'))
            ->orderBy('name')
            ->get()
            ->groupBy('module');
        return Inertia::render('RolePermission/Index', [
            'roles' => $roles,
            'permissions' => $permissions
        ]);
    }

    /**
     * @param Request $request
     * @param Role $role
     * @return Role
     */

    public function update(Request $request,Role $role){
        $this->authorize('update_role');
        if ($role->hasPermissionTo($request->permissions)){
            $role->revokePermissionTo($request->permissions);
        }else{
            $role->givePermissionTo($request->permissions);
        }
        return $role;
    }

    /**
     * @param Role $role
     * @return string
     */

    public function getRolePermissions(Role $role){
        return $role->permissions()->pluck('id')->toJson();
    }
}
