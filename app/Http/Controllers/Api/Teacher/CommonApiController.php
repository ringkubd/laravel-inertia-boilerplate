<?php

namespace App\Http\Controllers\Api\Teacher;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\ClassRoom;
use App\Models\Polytechnic;
use App\Models\Student;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CommonApiController extends Controller
{
    /**
     * @return AcademicSession[]|Collection
     */
    public function academicSession(){
        return AcademicSession::all();
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function classRoom(Request $request){
        $is_madrasah = $request->is_madrasah == 1 ? 1 : 0;
        return ClassRoom::where('is_madrasa',$is_madrasah)->get();
    }

    /**
     * @return Polytechnic[]|Collection
     */
    public function allPolytechnic(){
        return Polytechnic::all();
    }

    public function students(Request $request){
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
                    $q->where('ssc_session', 'like', "%$v%");
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
                ->with('madrasha')
                ->with('polytechnic')
                ->where('status', true)
                ->get();
        }
        return $students;
    }
}
