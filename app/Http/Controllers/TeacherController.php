<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\Designation;
use App\Models\Madrasha;
use App\Models\Teacher;
use App\Models\Trade;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class TeacherController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $this->authorize('view_teacher');
        $teachers = Teacher::query()
            ->when($request->search, function ($q, $v) {
                $q->where('name', 'like', "%$$v%")
                ->orWhere('designation', 'like', "%$v%");
            })
            ->with("user", 'trade', 'madrasa')
            ->paginate();
        return Inertia::render("Teacher/Index", [
            'can' => [
                'create' => auth()->user()->can('create_teacher'),
                'update' => auth()->user()->can('update_teacher'),
                'delete' => auth()->user()->can('delete_teacher'),
                'view' => auth()->user()->can('view_teacher'),
            ],
            'teachers' => $teachers
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create_teacher');
        return Inertia::render("Teacher/Create", [
            'can' => [
                'create' => auth()->user()->can('create_teacher'),
                'update' => auth()->user()->can('update_teacher'),
                'delete' => auth()->user()->can('delete_teacher'),
                'view' => auth()->user()->can('view_teacher'),
            ],
            'teacher' => new Teacher(),
            'madrasa' => Madrasha::all(),
            'user' => new User(),
            'banks' => new Bank(),
            'selected_bank' => '',
            'selected_trade' => '',
            'trades' => Trade::where('is_madrasa', 1)->get(),
            'designation' => Designation::where('is_pc', 0)->get()
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
        $this->authorize('create_teacher');
        $request->validate([
            'name' => 'required',
            'mobile' => 'required',
            'designation' => 'required',
            'photo' => 'required',
            'email' => 'required',
            'password' => 'required',
        ]);
        $data = [];
        DB::beginTransaction();
        if ($request->hasFile('photo')) {
            $image = $request->file('photo');
            $fileName = $request->madrashas_id.'_'.$request->trade_id.'_'. now();
            $path = $image->move(public_path('photos/teachers'), $fileName.'.'.$image->getClientOriginalExtension());
            $data['photo'] ='photos/teachers'.'/'.$fileName.'.'.$image->getClientOriginalExtension();
        }
        $users = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'profile_photo' => $data['photo']
        ]);
        $teacher = Teacher::create([
            'users_id' => $users->id,
            'name' => $request->name,
            'father_name' => $request->father_name,
            'mother_name' => $request->mother_name,
            'mobile' => $request->mobile,
            'joining_date' => $request->joining_date,
            'designation' => $request->designation,
            'trade_id' => $request->trade_id,
            'madrashas_id' => $request->madrashas_id,
            'dob' => $request->dob,
            'photo' => $data['photo'],
            'nid' => $request->nid,
            'bank_account' => $request->bank_account,
            'bank_branch' => $request->bank_branch,
            'bank_name' => $request->bank_name,
            'present_address' => $request->present_address,
            'permanent_address' => $request->permanent_address,
        ]);
        DB::commit();
        return redirect()->route('teacher.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Teacher  $teacher
     * @return \Illuminate\Http\Response
     */
    public function show(Teacher $teacher)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Teacher  $teacher
     * @return \Illuminate\Http\Response
     */
    public function edit(Teacher $teacher)
    {
        $this->authorize('update_teacher');
        return Inertia::render("Teacher/Edit", [
            'can' => [
                'create' => auth()->user()->can('create_teacher'),
                'update' => auth()->user()->can('update_teacher'),
                'delete' => auth()->user()->can('delete_teacher'),
                'view' => auth()->user()->can('view_teacher'),
            ],
            'teacher' => $teacher,
            'madrasa' => Madrasha::all(),
            'user' => new User(),
            'banks' => new Bank(),
            'selected_bank' => '',
            'selected_trade' => '',
            'trades' => Trade::where('is_madrasa', 1)->get(),
            'designation' => Designation::where('is_pc', 0)->get()
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Teacher  $teacher
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Teacher $teacher)
    {
        $this->authorize('update_teacher');
        $request->validate([
            'name' => 'required',
            'mobile' => 'required',
            'designation' => 'required',
            'photo' => 'required',
            'email' => 'required',
            'password' => 'required',
        ]);
        $data = [];
        DB::beginTransaction();
        if ($request->hasFile('photo')) {
            $image = $request->file('photo');
            $fileName = $request->madrashas_id.'_'.$request->trade_id.'_'. now();
            $path = $image->move(public_path('photos/teachers'), $fileName.'.'.$image->getClientOriginalExtension());
            $data['photo'] ='photos/teachers'.'/'.$fileName.'.'.$image->getClientOriginalExtension();
        }else{
            $data['photo'] = $request->photo;
        }
        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'profile_photo' => $data['photo']
        ];
        if ($request->has('password') && $request->password != "") {
            $userData['password'] = Hash::make($request->password);
        }
        // Update Teacher User
        $users = User::find($teacher->users_id)->update($userData);

        // Update Teacher
        $teacher = $teacher->update([
            'name' => $request->name,
            'father_name' => $request->father_name,
            'mother_name' => $request->mother_name,
            'mobile' => $request->mobile,
            'joining_date' => $request->joining_date,
            'designation' => $request->designation,
            'trade_id' => $request->trade_id,
            'madrashas_id' => $request->madrashas_id,
            'dob' => $request->dob,
            'photo' => $data['photo'],
            'nid' => $request->nid,
            'bank_account' => $request->bank_account,
            'bank_branch' => $request->bank_branch,
            'bank_name' => $request->bank_name,
            'present_address' => $request->present_address,
            'permanent_address' => $request->permanent_address,
        ]);
        DB::commit();
        return redirect()->route('teacher.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Teacher  $teacher
     * @return \Illuminate\Http\Response
     */
    public function destroy(Teacher $teacher)
    {
        $this->authorize('delete_teacher');
        $teacher->delete();
        return redirect()->route('teacher.index');
    }
}
