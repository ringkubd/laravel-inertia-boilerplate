<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $this->authorize('view_permission');

        $permission = Permission::query()
            ->when($request->search, function ($q, $value) use ($request){
                $q->where('name', 'like', "%".$value . "%");
            })->paginate();
        $this->createAutoPermissions($this->module());
        return Inertia::render('Permission/Index', [
            'permissions' => $permission,
            'can' => [
                'create' => auth()->user()->can('create_permission'),
                'update' => auth()->user()->can('update_permission'),
                'delete' => auth()->user()->can('delete_permission'),
                'view' => auth()->user()->can('view_permission'),
            ]
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create()
    {
        $this->authorize('create_permission');

        $permission = new Permission();
        $modules = $this->module();
        return Inertia::render('Permission/Create',[
            'permission' => $permission,
            'modules' => $modules
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(Request $request)
    {
        $this->authorize('create_permission');

        $request->validate([
            'name' => 'required',
            'guard_name' => 'required',
            'module' => 'required'
        ]);
        $data = $request->all();
        if ($request->has('module') && count(explode('_', $request->name)) < 2) {
            $data['name'] = $request->name ."_". $request->module;
        }
        $permission = Permission::create($data);
        return redirect()->route('permission.index')->withFlash('success', 'Permission store successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show($id)
    {
        $this->authorize('view_permission');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($id)
    {
        $this->authorize('update_permission');
        $permission = Permission::find($id);
        $modules = $this->module();
        return Inertia::render('Permission/Edit', [
            'permission' => $permission,
            'modules' => $modules
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(Request $request, $id)
    {
        $this->authorize('update_permission');
        $request->validate([
            'name' => 'required',
            'guard_name' => 'required',
            'module' => 'required'
        ]);
        $data = $request->all();
        if ($request->has('module') && count(explode('_', $request->name)) < 2) {
            $data['name'] = $request->name ."_". $request->module;
        }
        $permission = Permission::find($id)->update($data);
        return redirect()->route('permission.index')->withFlash('success', 'Permission updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($id)
    {
        $this->authorize('delete_permission');
        $role = Permission::find($id)->delete();
        return redirect()->route('permission.index')->withFlash('success', "Permission successfully removed");
    }

    /**
     * @return array
     */
    protected function module(){
        $routes = Route::getRoutes();
        $routeName = [];
        $numberOfDevide = [];
        foreach ($routes as $route){
            $module =  explode('.',$route->getName());
            $numberOfDevide[$route->getName()] = count($module);
            if (count($module) > 1 && !in_array($module[0], $routeName)) {
                if (count($module) == 2) {
                    $routeName [] = $module[0];
                }else{
                    $routeName [] = $module[0].'_'.$module[1];
                }
            }

        }
        return $routeName;
    }

    /**
     * @param array $modules
     * @return mixed
     */
    protected function createAutoPermissions(Array $modules){
        $permissions = Permission::whereIn('module', $modules)->get()->groupBy('module')->keys()->toArray();
        $exclude = config('custom.excluded_from_create_permission');
        $newModule = array_diff($modules, $exclude, $permissions);
        $defaultPermissions = config('custom.default_permissions');
        $newPermissions = [];
        $i = 0;
        foreach ($newModule as $module) {
            if (count($defaultPermissions) > 0) {
                $newP = array_map(function ($v) use($module, $i){
                    return [
                        'name' => $v.'_'.$module,
                        'guard_name' => 'web',
                        'module' => $module,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }, $defaultPermissions);
                $permissions = Permission::insert($newP);
            }
            $i++;
        }
        return $permissions;
    }
}
