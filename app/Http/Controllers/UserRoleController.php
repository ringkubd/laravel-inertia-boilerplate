<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserRoleController extends Controller
{
    /**
     * @param Request $request
     */
    public function __invoke(Request $request){
        $request->validate([
            'user' => 'required|int',
            'roles' => 'required|array'
        ]);
        $user = User::query()
            ->find($request->user)->syncRoles($request->roles);
        return redirect()->route('user_role.index')->withFlash("success", "Successfully updated");
    }

    /**
     * @return \Inertia\Response
     */

    public function index(){
        $users = User::select('name as text', 'id')->get();
        $roles = Role::select('name as text', 'id')->get();
        return Inertia::render('UserPermission/UserRole', [
            'users' => $users,
            'roles' => $roles
        ]);
    }

    /**
     * @param Request $request
     * @return string
     */

    public function getUser(Request $request){
        return User::query()
            ->when($request->name, function ($q, $v){
                $q->where('name', 'like', "%{$v}%");
            })->get()->toJson();
    }

    /**
     * @param Request $request
     * @return string
     */

    public function getPermission(Request $request){
        return Permission::query()
            ->when($request->name, function ($q, $v){
                $q->where('name', 'like', "%{$v}%");
            })->get()->toJson();
    }

    /**
     * @param Request $request
     * @return string
     */
    public function getRoles(Request $request){
        return Role::query()
            ->when($request->name, function ($q, $v){
                $q->where('name', 'like', "%{$v}%");
            })->paginate(50)->toJson();
    }

    /**
     * @param User $user
     * @return mixed
     */
    public function userPermission(User $user){
        return $user->getDirectPermissions()->toJson();
    }

    /**
     * @param User $user
     * @return mixed
     */
    public function userRoles(User $user){
        return $user->roles()->get()->toJson();
    }
}
