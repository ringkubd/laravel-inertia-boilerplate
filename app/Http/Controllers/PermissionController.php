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
     */
    public function index(Request $request)
    {
        $permission = Permission::query()
        ->when($request->search, function ($q, $value) use ($request){
            $q->where('name', 'like', "%".$value . "%");
        })->paginate();
        return Inertia::render('Permission/Index', [
            'permissions' => $permission
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
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
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $role = Permission::find($id)->delete();
        return redirect()->route('permission.index')->withFlash('success', "Permission successfully removed");
    }

    protected function module(){
        $routes = Route::getRoutes();
        $routeName = [];
        foreach ($routes as $route){
            $module =  explode('.',$route->getName());
            if (count($module) > 1 && !in_array($module[0], $routeName)) {
                $routeName [] = $module[0];
            }

        }
        return $routeName;
    }
}
