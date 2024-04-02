<?php

namespace App\Http\Controllers;

use App\Models\Madrasha;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $users = User::query()
            ->when($request->search, function ($q, $v){
                $q->where('name', 'like', "%{$v}%");
            })
            ->with(['roles' => function($q){
                $q->select('name');
            }])
            ->with('madrasah', 'student_madrasah', 'roles')
            ->paginate();

        $this->authorize('view_users');
        return Inertia::render('Users/Index', [
            'users' => $users,
            'can' => [
                'create' => auth()->user()->can('create_users'),
                'update' => auth()->user()->can('update_users'),
                'delete' => auth()->user()->can('delete_users'),
                'view' => auth()->user()->can('view_users'),
            ]
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create_users');
        $roles = Role::all();
        $madrasah = Madrasha::all();

        return Inertia::render('Users/Create', [
            'user' => new User(),
            'roles' => $roles,
            'madrasah' => $madrasah,
            'can' => [
                'create' => auth()->user()->can('create_users'),
                'update' => auth()->user()->can('update_users'),
                'delete' => auth()->user()->can('delete_users'),
                'view' => auth()->user()->can('view_users'),
            ]
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
        $this->authorize('create_users');
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'madrasha_id' => $request->madrasha_id
        ]);
        $user->syncRoles($request->roles ?? []);
        return redirect()->route('users.index')->withSuccess("success", "User successfully created.");

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->authorize('view_users');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->authorize('update_users');
        $madrasah = Madrasha::all();
        return Inertia::render('Users/Edit', [
            'user' => User::with(['roles' => function($q){
                $q->select('name', 'id');
            }])->find($id),
            'madrasah' => $madrasah,
            'can' => [
                'create' => auth()->user()->can('create_users'),
                'update' => auth()->user()->can('update_users'),
                'delete' => auth()->user()->can('delete_users'),
                'view' => auth()->user()->can('view_users'),
            ]
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
        $this->authorize('update_users');
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
        ]);
        $userArray = [
            'name' => $request->name,
            'email' => $request->email,
            'madrasha_id' => $request->madrasha_id
        ];
        if ($request->has('password') && $request->password != "") {
            $userArray['password'] = Hash::make($request->password);
        }
        $user = User::findOrFail($id)->syncRoles($request->roles ?? [])->update($userArray);
        return redirect()->route('users.index')->withSuccess("success", "User successfully updated.");

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->authorize('delete_users');
        $user = User::find($id);
        $user->support()->update(['status' => 1]);
        $user->delete();
        return redirect()->route('users.index')->withSuccess("User successfully deleted.");
    }
}
