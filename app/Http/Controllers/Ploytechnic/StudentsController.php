<?php

namespace App\Http\Controllers\Ploytechnic;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\Bank;
use App\Models\ClassRoom;
use App\Models\Madrasha;
use App\Models\Polytechnic;
use App\Models\Student;
use App\Models\Trade;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class StudentsController extends Controller
{
    protected $root_component = "Student/Polytechnic/";
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $students = Student::query()
            ->with('users')
            ->with('classroom')
            ->when($request->search, function ($q, $v){
                $q
                    ->where('name', 'like', "%$v%")
                    ->orWhere('mobile', 'like', "%$v%")
                    ->orWhereHas('classroom', function ($q) use($v){
                        $q->where('class_rooms.name', 'like', "%$v%");
                    });
            })->when($request->current_session, function ($q, $v){
                $q->where('current_session', 'like', "%$v%");
            })
            ->when($request->trade, function ($q, $v){
                $q->where('polytechnic_trade_id', 'like', "%$v%");
            })
            ->when($request->classroom, function ($q, $v){
                $q->whereHas('classroom', function ($q) use ($v){
                    $q->where('class_room_students.class_room_id',$v);
                });
            })
            ->with('madrasha')
            ->with('polytechnic')
            ->whereNotNull('polytechnic')
            ->where('madrasa_completed', true)
            ->where('status', true)
            ->when($request->old_students, function ($q){
                $q->where('polytechnic_completed', true);
            },function ($q){
                $q->where('polytechnic_completed', false);
            })
            ->paginate();

        $classes = ClassRoom::where('is_madrasa', false)->get();
        $session = AcademicSession::all();
        $trade = Trade::where('is_madrasa', 0)->get();

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
            'classes' => $classes
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

        $students = Student::query()
            ->where('status', true)
            ->whereNull('polytechnic' )
            ->where('madrasa_completed', true)->get();
        $student = new Student();
        $academic_session = AcademicSession::all();
        $polytechnic = Polytechnic::all();
        $trade_polytechnic = Trade::whereIsMadrasa(0)->get();
        $classes = ClassRoom::where('is_madrasa', false)->get();

        $selected_trade = null;
        $selected_session = null;
        $banks = Bank::all();
        $selected_bank = null;
        $user = new User();
        return Inertia::render($this->root_component.'Create', [
            'students' => $students,
            'student' => $student,
            'selected_trade' => $selected_trade,
            'selected_session' => $selected_session,
            'academic_session' => $academic_session,
            'trade_polytechnic' => $trade_polytechnic,
            'banks' => $banks,
            'selected_bank' => $selected_bank,
            'user' => $user,
            'polytechnic' => $polytechnic,
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
        $request->validate([
            'student_id' => 'required',
            'polytechnic_trade_id' => 'required',
            'trade' => 'required',
            'polytechnic' => 'required',
            'polytechnic_roll' => 'required',
            'polytechnic_session' => 'required',
            'semester' => 'required',
            'current_session' => 'required',
            'classroom' => 'required',
        ]);
        $data = $request->all();
        $user = null;
        $data['student_id'] = $request->student_id;
        $student = Student::findOrFail($request->student_id);
        $student->update($data);
        $student->classroom()->sync($request->classroom);
        return redirect()->route('polytechnic.student.index')->withMessage('Student addedd.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $student = Student::with('classroom')->findOrFail($id);
        if (!$student) {
            return back();
        }
        $academic_session = AcademicSession::all();
        $selected_trade = $student->trade;
        $selected_session = $student->current_session;
        $banks = Bank::all();
        $selected_bank = $student->bank_name;
        $polytechnic = Polytechnic::all();
        $trade_polytechnic = Trade::whereIsMadrasa(0)->get();

        $classes = ClassRoom::where('is_madrasa', false)->get();

        $students = Student::query()
            ->where('status', true)
            ->whereNotNull('polytechnic')
            ->where('madrasa_completed', true)->get();

        return Inertia::render($this->root_component.'Edit', [
            'student' => $student,
            'selected_trade' => $selected_trade,
            'selected_session' => $selected_session,
            'academic_session' => $academic_session,
            'banks' => $banks,
            'selected_bank' => $selected_bank,
            'classes' => $classes,
            'polytechnic' => $polytechnic,
            'trade_polytechnic' => $trade_polytechnic,
            'students' => $students
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
            'student_id' => 'required',
            'polytechnic_trade_id' => 'required',
            'trade' => 'required',
            'polytechnic' => 'required',
            'polytechnic_roll' => 'required',
            'polytechnic_session' => 'required',
            'semester' => 'required',
            'current_session' => 'required',
            'classroom' => 'required',
        ]);
        $data = $request->all();
        $user = null;
        $data['student_id'] = $request->student_id;
        $student = Student::findOrFail($request->student_id);
        $student->update($data);
        $student->classroom()->sync($request->classroom);
        return redirect()->route('polytechnic.student.index')->withMessage('Student Updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
