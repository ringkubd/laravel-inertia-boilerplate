<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use App\Models\Bank;
use App\Models\Madrasha;
use App\Models\Polytechnic;
use App\Models\Student;
use App\Models\Trade;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class StudentsManagementController extends Controller
{
    protected $root_component = "Student/";
    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $this->authorize('view_student');

        $students = Student::with('users')->when($request->search, function ($q, $v){
            $q->where('name', 'like', "%$v%")->orWhere('mobile', 'like', "%$v%");
        })->when($request->academic_session, function ($q, $v){
            $q->where('current_session', 'like', "%$v%");
        })
            ->when($request->trade, function ($q, $v){
                $q->where('trade', 'like', "%$v%");
            })
            ->with('madrasha')
            ->with('polytechnic')
            ->paginate();

        $session = AcademicSession::all();
        $trade = Trade::all();

        return Inertia::render($this->root_component.'Index', [
            'students' => $students,
            'can' => [
                'create' => auth()->user()->can('create_student'),
                'view' => auth()->user()->can('view_student'),
                'update' => auth()->user()->can('update_student'),
                'delete' => auth()->user()->can('delete_student'),
            ],
            'trades' => $trade,
            'academic_session' => $session
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create_student');

        $student = new Student();
        $academic_session = AcademicSession::all();
        $polytechnic = Polytechnic::all();
        $madrasa = Madrasha::all();

        $trade_polytechnic = Trade::whereIsMadrasa(0)->get();
        $trade_madrasa = Trade::whereIsMadrasa(1)->get();

        $selected_trade = null;
        $selected_session = null;
        $banks = Bank::all();
        $selected_bank = null;
        $user = new User();

        return Inertia::render($this->root_component.'Create', [
            'student' => $student,
            'selected_trade' => $selected_trade,
            'selected_session' => $selected_session,
            'academic_session' => $academic_session,
            'trade_polytechnic' => $trade_polytechnic,
            'trade_madrasa' => $trade_madrasa,
            'banks' => $banks,
            'selected_bank' => $selected_bank,
            'user' => $user,
            'polytechnic' => $polytechnic,
            'madrasa' => $madrasa,
            'can' => [
                'create' => auth()->user()->hasPermissionTo('create_student'),
                'view' => auth()->user()->hasPermissionTo('view_student'),
                'update' => auth()->user()->hasPermissionTo('update_student'),
                'delete' => auth()->user()->hasPermissionTo('delete_student'),
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
        $request->validate([
            'name' => 'required',
            'mobile' => 'required',
            'dob' => 'required',
            'father_name' => 'required',
            'mother_name' => 'required',
            'present_address' => 'required',
            'permanent_address' => 'required',
            'madrasa_trade_id' => 'required',
            'class_roll' => 'required',
            'madrasha_id' => 'required',
        ]);
        $data = $request->all();
        $student_id = random_int(0, 9);
        if ($request->hasFile('photo')) {
            $image = $request->file('photo');
            $fileName = $request->madrashas_id.'_'.$request->class_roll.'_'.$request->trade.'_'.$student_id;
            $path = $image->move(public_path('photos/students'), $fileName.'.'.$image->getClientOriginalExtension());
            $data['photo'] ='photos/students'.'/'.$fileName.'.'.$image->getClientOriginalExtension();
        }
        if ($request->hasFile('id_card')) {
            $image = $request->file('id_card');
            $fileName = $request->madrashas_id.'_'.$request->class_roll.'_'.$request->trade.'_'.$student_id.'_idcard';
            $path = $image->move(public_path('photos/students'), $fileName.'.'.$image->getClientOriginalExtension());
            $data['id_card'] ='photos/students'.'/'.$fileName.'.'.$image->getClientOriginalExtension();
        }
        $user = null;
        $data['student_id'] = $student_id;
        if ($request->has('email') && $request->has('password')) {
            $request->validate([
                'email' => 'unique:users'
            ]);
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);
            $user = $user->id;
        }
        $data['users_id'] = $user;
        unset($data['email']);
        unset($data['password']);
        $students = Student::create($data);
        return redirect()->route('student.index')->withMessage('Student addedd.');
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
        $student = Student::findOrFail($id);
        if (!$student) {
            return back();
        }

        $polytechnic = Polytechnic::all();
        $madrasa = Madrasha::all();

        $academic_session = AcademicSession::all();
        $trade_polytechnic = Trade::whereIsMadrasa(0)->get();
        $trade_madrasa = Trade::whereIsMadrasa(1)->get();
        $selected_trade = $student->trade;
        $selected_session = $student->current_session;
        $banks = Bank::all();
        $selected_bank = $student->bank_name;


        if ($student->users_id != null) {
            $user = User::find($student->users_id);
            if (!$user) {
                $user = new User();
            }
        }else{
            $user = new User();
        }

        return Inertia::render($this->root_component.'Edit', [
            'student' => $student,
            'selected_trade' => $selected_trade,
            'selected_session' => $selected_session,
            'academic_session' => $academic_session,
            'trade_polytechnic' => $trade_polytechnic,
            'trade_madrasa' => $trade_madrasa,
            'banks' => $banks,
            'selected_bank' => $selected_bank,
            'user' => $user,
            'polytechnic' => $polytechnic,
            'madrasa' => $madrasa
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
            'mobile' => 'required',
            'dob' => 'required',
            'father_name' => 'required',
            'mother_name' => 'required',
            'present_address' => 'required',
            'permanent_address' => 'required',
            'madrasa_trade_id' => 'required',
            'class_roll' => 'required',
            'madrasha_id' => 'required',
        ]);
        $studentsInformation = Student::find($id);
        $data = $request->all();
        $student_id = random_int(0, 9);
        if ($request->hasFile('photo')) {
            if ($studentsInformation->photo != null && file_exists(public_path($studentsInformation->photo))) {
                unlink(public_path($studentsInformation->photo));
            }
            $image = $request->file('photo');
            $fileName = $request->madrashas_id.'_'.$request->class_roll.'_'.$request->trade.'_'.$student_id;
            $path = $image->move(public_path('photos/students'), $fileName.'.'.$image->getClientOriginalExtension());
            unset($data['photo']);
            $data['photo'] ='photos/students'.'/'.$fileName.'.'.$image->getClientOriginalExtension();
        }
        $user = null;
        $data['student_id'] = $student_id;
        if ($request->has('email') && $request->has('password')) {
            if ($studentsInformation->users_id != null && $user = User::find($studentsInformation->users_id)) {
                $userData['name'] = $request->name;
                if ($request->has('password') && $request->password != null) {
                    $userData['password'] = Hash::make($request->password);
                }
                $user->update($userData);
            }else{
                $request->validate([
                    'email' => 'unique:users'
                ]);
                $userData = [
                    'name' => $request->name,
                    'email' => $request->email,
                ];
                if ($request->has('password') && $request->password != "") {
                    $userData['password'] = Hash::make($request->password);
                }
                $user = User::create($userData);
            }
            $user = $user->id;
        }
        $data['users_id'] = $user;
        unset($data['email']);
        unset($data['password']);
        $students = $studentsInformation->update($data);
        return redirect()->route('student.index')->withMessage('Student addedd.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $student = Student::find($id);
        $user = User::find($student->users_id);
        if ($student->delete()) {
            $user->delete();
        }
        return redirect()->route('student.index')->withMessage('Student Deleted Successfully.');
    }
}
