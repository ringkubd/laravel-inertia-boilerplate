<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class RolesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $this->authorize('view_roles');
        $roles = Role::query()
        ->when($request->search, function ($q, $value) use ($request){
            $q->where('name', 'like', "%".$value . "%");
        })->paginate();
        return Inertia::render('Roles/Index', [
            'roles' => $roles,
            'can' => [
                'create' => auth()->user()->can('create_role'),
                'update' => auth()->user()->can('update_role'),
                'delete' => auth()->user()->can('delete_role'),
                'view' => auth()->user()->can('view_role'),
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
        $this->authorize('create_roles');
        $role = new Role();
        return Inertia::render('Roles/Create',[
            'role' => $role,
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
        $this->authorize('store_roles');
        $request->validate([
            'name' => 'required',
            'guard_name' => 'required'
        ]);
        $role = Role::create($request->all());
        return redirect()->route('roles.index')->withFlash('success', 'Role store successfully');
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
        $this->authorize('view_roles');
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
        $this->authorize('update_roles');
        $role = Role::find($id);
        return Inertia::render('Roles/Edit', [
            'role' => $role
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
        $this->authorize('update_roles');
        $request->validate([
            'name' => 'required',
            'guard_name' => 'required',
        ]);
        $role = Role::find($id)->update($request->all());
        return redirect()->route('roles.index')->withFlash('success', 'Role updated successfully');
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
        $this->authorize('delete_roles');
        $role = Role::find($id)->delete();
        return redirect()->route('roles.index')->withFlash('success', "Role successfully removed");
    }
}
