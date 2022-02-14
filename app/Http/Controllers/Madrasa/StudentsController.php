<?php

namespace App\Http\Controllers\Madrasa;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\Bank;
use App\Models\ClassRoom;
use App\Models\Madrasha;
use App\Models\Polytechnic;
use App\Models\Student;
use App\Models\Trade;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use PDF;


class StudentsController extends Controller
{
    protected $root_component = "Student/Madrasa/";
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('view_madrasa_student');
        $madrasah_id = $request->has('madrasah') ? currentMadrasah($request->madrasah): currentMadrasah();

        if ($request->pdf) {
            if (($request->has('search') && $request->search != "") || ($request->has('query') && $request->input('query') != "")) {
                $search = $request->search ?? $request->input('query');
                $students = Student::search($search)->get();
            }else{
                $students = Student::query()
                    ->with('users')
                    ->with('classroom')
                    ->with('madrasha')
                    ->with('polytechnic')
                    ->where('status', true)
                    ->when($madrasah_id, function ($q, $v){
                        $q->where('madrasha_id', $v);
                    })
                    ->when($request->current_session, function ($q, $v){
                        $q->where('current_session', 'like', "%$v%");
                    },function ($q){
                        $q->where('madrasa_completed', false);
                    })
                    ->when($request->trade, function ($q, $v){
                        $q->where('madrasa_trade_id', 'like', "%$v%");
                    })
                    ->when($request->classroom, function ($q, $v){
                        $q->whereHas('classroom', function ($q) use ($v){
                            $q->where('class_room_students.class_room_id',$v);
                        });
                    })
                    ->get();
            }
            $pdf = PDF::loadView('reports.madrasah.student', compact('students'));
            $pdf->setPaper('A4', 'portrait');
            $pdf->setOptions(['defaultFont' => 'sans-serif']);
            return $pdf->stream();
        }

        if (($request->has('search') && $request->search != "") || ($request->has('query') && $request->input('query') != "")) {
            $search = $request->search ?? $request->input('query');
            $students = Student::search($search)->paginate();
        }else{
            $students = Student::query()
                ->with('users')
                ->with('classroom')
                ->when($request->search, function ($q, $v){
                    $q
                        ->where('name', 'like', "%$v%")
                        ->orWhere('mobile', 'like', "%$v%")
                        ->orWhereHas('madrasha', function ($q) use($v){
                            $q->where('madrashas.name', 'like', "%$v%");
                        })
                        ->orWhereHas('classroom', function ($q) use($v){
                            $q->where('class_rooms.name', 'like', "%$v%");
                        });
                })->when($request->current_session, function ($q, $v){
                    $q->where('current_session', 'like', "%$v%");
                },function ($q){
                    $q->where('madrasa_completed', false);
                })
                ->when($request->trade, function ($q, $v){
                    $q->where('madrasa_trade_id', 'like', "%$v%");
                })
                ->when($request->classroom, function ($q, $v){
                    $q->whereHas('classroom', function ($q) use ($v){
                        $q->where('class_room_students.class_room_id',$v);
                    });
                })
                ->when($madrasah_id, function ($q, $v){
                    $q->where('madrasha_id', $v);
                })
                ->with('madrasha')
                ->with('polytechnic')
                ->where('status', true)
                ->paginate();
        }

        $classes =  ClassRoom::where('is_madrasa', true)->get();
        $session = AcademicSession::all();
        $trade = Trade::where('is_madrasa', 1)->get();
        $madrasah = Madrasha::all();

        return Inertia::render($this->root_component.'Index', [
            'students' => $students,
            'can' => [
                'create' => auth()->user()->can('create_student'),
                'view' => auth()->user()->can('view_student'),
                'update' => auth()->user()->can('update_student'),
                'delete' => auth()->user()->can('delete_student'),
            ],
            'trades' => $trade,
            'academic_session' => $session,
            'classes' => $classes,
            'madrasahs' => $madrasah,
            'selected_madrasah' => $madrasah_id
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create_madrasa_student');

        $student = new Student();
        $academic_session = AcademicSession::all();
        $polytechnic = Polytechnic::all();
        $madrasa = Madrasha::all();

        $trade_polytechnic = Trade::whereIsMadrasa(0)->get();
        $trade_madrasa = Trade::whereIsMadrasa(1)->get();
        $classes = ClassRoom::where('is_madrasa', true)->get();

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
            'classes' => $classes,
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
        $this->authorize('create_madrasa_student');
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
            'classroom' => 'required',
        ]);
        $data = $request->all();
        $student_id = random_int(99, 9999);
        if ($request->hasFile('photo')) {
            $image = $request->file('photo');
            $fileName = $request->madrashas_id.'_'.$request->class_roll.'_'.$request->trade.'_'.$student_id.'_'.rand(99,999);
            $path = $image->move(public_path('photos/students'), $fileName.'.'.$image->getClientOriginalExtension());
            $data['photo'] ='photos/students'.'/'.$fileName.'.'.$image->getClientOriginalExtension();
        }
        if ($request->hasFile('id_card')) {
            $image = $request->file('id_card');
            $fileName = $request->madrashas_id.'_'.$request->class_roll.'_'.$request->trade.'_'.$student_id.'_idcard'.'_'.rand(99,999);
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
            $user->assignRole('Student');
            $user = $user->id;
        }
        $data['users_id'] = $user;
        unset($data['email']);
        unset($data['password']);
        $students = Student::create($data);
        $students->classroom()->attach($request->classroom);
        return redirect()->route('madrasa.student.index')->withMessage('Student addedd.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $student = Student::with('madrasahResult', 'polytechnicResult', 'classroom', 'madrasha', 'polytechnic', 'users')->find($id);
        if(auth()->user()->hasRole('Student') && $student->users_id != auth()->user()->id){
            abort(403);
        }
        return Inertia::render('Student/Profile', [
            'student' => $student
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->authorize('update_madrasa_student');
        $student = Student::with('classroom')->findOrFail($id);
        if (!$student) {
            return back();
        }
        $madrasa = Madrasha::all();

        $academic_session = AcademicSession::all();
        $trade_madrasa = Trade::whereIsMadrasa(1)->get();
        $selected_trade = $student->trade;
        $selected_session = $student->current_session;
        $banks = Bank::all();
        $selected_bank = $student->bank_name;

        $classes = ClassRoom::where('is_madrasa', true)->get();


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
            'trade_madrasa' => $trade_madrasa,
            'banks' => $banks,
            'selected_bank' => $selected_bank,
            'user' => $user,
            'madrasa' => $madrasa,
            'classes' => $classes
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
        $this->authorize('update_madrasa_student');
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
            'classroom' => 'required',
        ]);
        $studentsInformation = Student::find($id);
        $studentsInformation->classroom()->sync($request->classroom);
        $data = $request->all();
        $student_id = $studentsInformation->student_id;
        if ($request->hasFile('photo')) {
            if ($studentsInformation->photo != null && file_exists(public_path($studentsInformation->photo))) {
                unlink(public_path($studentsInformation->photo));
            }
            $image = $request->file('photo');
            $fileName = $request->madrashas_id.'_'.$request->class_roll.'_'.$request->trade.'_'.$student_id.'_'.rand(99,999);
            $path = $image->move(public_path('photos/students'), $fileName.'.'.$image->getClientOriginalExtension());
            unset($data['photo']);
            $data['photo'] ='photos/students'.'/'.$fileName.'.'.$image->getClientOriginalExtension();
        }
        if ($request->hasFile('id_card')) {
            if ($studentsInformation->id_card != null && file_exists(public_path($studentsInformation->id_card))) {
                unlink(public_path($studentsInformation->id_card));
            }
            $image = $request->file('id_card');
            $fileName = $request->madrashas_id.'_'.$request->class_roll.'_'.$request->trade.'_'.$student_id.'_'.rand(99,999);
            $path = $image->move(public_path('photos/students/id_card'), $fileName.'.'.$image->getClientOriginalExtension());
            unset($data['id_card']);
            $data['id_card'] ='photos/students/id_card/'.$fileName.'.'.$image->getClientOriginalExtension();
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
        unset($data['classroom']);
        $studentsInformation->update($data);
        return redirect()->route('madrasa.student.index')->withMessage('Student Updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->authorize('delete_madrasa_student');
        $student= Student::findOrFail($id);
        $student->delete();
        return redirect()->route('madrasa.student.index')->withMessage('Student Deleted.');
    }

    /**
     * Search Data
     *
     * @param Request $request
     * @return Collection
     */

    public function search(Request $request){
        $students =  Student::search($request->search, function ($meilisearch, $query, $options) use($request){
            if ($request->only_polytechnic) {
                $filter = "";
                if (auth()->user()->hasRole('Student')) {
                    $madrasahName = auth()->user()?->student_madrasah->first()?->name;
                    $filter = ["madrasah_name='$madrasahName'"];
                }
                $options['filter'] = [['polytechnic_name!=null'], ['madrasa_completed=1'], $filter];;
            }
            if ($request->only_madrasa) {
                $options['filter'] = [['madrasa_completed=0'], ['madrasah_name!=null']];
            }
            $options['attributesToHighlight'] = ["*"];
            return $meilisearch->search($query, $options);
        })->paginate(100);
        return $students;
    }
}
