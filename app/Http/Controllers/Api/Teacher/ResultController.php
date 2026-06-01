<?php

namespace App\Http\Controllers\Api\Teacher;

use App\Http\Controllers\Controller;
use App\Models\MadrasahResult;
use App\Models\Result;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ResultController extends Controller
{
    use \App\Http\Controllers\Api\Teacher\Concerns\AuthorizesTeachers;
    // ─── Polytechnic Results ─────────────────────────────────────────

    public function polytechnicIndex(Request $request)
    {
        $studentIds = $this->teacherStudentIds();
        $query = Result::with(['student', 'addedBy'])->whereIn('student_id', $studentIds);

        if ($request->student_id) {
            $query->where('student_id', $request->student_id);
        }

        return response()->json([
            'data' => $query->latest()->get()->map(function ($r) {
                return [
                    'id' => $r->id,
                    'student_id' => $r->student_id,
                    'student_name' => $r->student?->name,
                    'semester' => $r->semester,
                    'gpa' => $r->gpa,
                    'status' => $r->status,
                    'failed_in_subject' => $r->failed_in_subject,
                    'added_by' => $r->addedBy?->name,
                    'created_at' => $r->created_at,
                ];
            }),
        ]);
    }

    public function polytechnicStore(Request $request)
    {
        if (!$this->ownsStudent($request->student_id)) {
            return response()->json(['message' => 'This student is not in your classes'], 403);
        }

        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
            'semester' => 'required|integer|min:1|max:8',
            'gpa' => 'nullable|numeric|min:0|max:5',
            'status' => 'required|in:Passed,Referred,Dropout',
            'failed_in_subject' => 'nullable|string',
            'attachment' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $result = Result::create([
            'student_id' => $request->student_id,
            'semester' => $request->semester,
            'gpa' => $request->gpa,
            'status' => $request->status,
            'failed_in_subject' => $request->failed_in_subject,
            'added_by' => auth()->id(),
        ]);

        if ($request->attachment) {
            $image = base64_decode($request->attachment);
            $safeName = Str::random(10) . '.png';
            Storage::disk('public_path')->put("result_document/" . $safeName, $image);
            $result->attachments()->create(['attachment' => "result_document/{$safeName}"]);
        }

        if ($request->status === 'Passed' && $request->semester < 8) {
            $student = Student::find($request->student_id);
            $nextSemester = $request->semester + 1;
            $student->update(['semester' => $nextSemester]);
        }
        if ($request->semester == 8 && $request->status === 'Passed') {
            Student::find($request->student_id)->update(['polytechnic_completed' => 1]);
        }

        return response()->json(['message' => 'Polytechnic result saved', 'data' => $result], 201);
    }

    public function polytechnicUpdate(Request $request, $id)
    {
        $result = Result::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'gpa' => 'nullable|numeric|min:0|max:5',
            'status' => 'sometimes|in:Passed,Referred,Dropout',
            'failed_in_subject' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $result->update($validator->validated());
        return response()->json(['message' => 'Result updated', 'data' => $result]);
    }

    // ─── Madrasah Results ───────────────────────────────────────────

    public function madrasahIndex(Request $request)
    {
        $studentIds = $this->teacherStudentIds();
        $query = MadrasahResult::with(['student', 'addedBy'])->whereIn('student_id', $studentIds);

        if ($request->student_id) {
            $query->where('student_id', $request->student_id);
        }

        return response()->json([
            'data' => $query->latest()->get()->map(function ($r) {
                return [
                    'id' => $r->id,
                    'student_id' => $r->student_id,
                    'student_name' => $r->student?->name,
                    'nine_gpa' => $r->nine_gpa,
                    'ten_gpa' => $r->ten_gpa,
                    'status' => $r->status,
                    'added_by' => $r->addedBy?->name,
                    'created_at' => $r->created_at,
                ];
            }),
        ]);
    }

    public function madrasahStore(Request $request)
    {
        if (!$this->ownsStudent($request->student_id)) {
            return response()->json(['message' => 'This student is not in your classes'], 403);
        }

        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
            'nine_gpa' => 'required|numeric|min:0|max:5',
            'ten_gpa' => 'nullable|numeric|min:0|max:5',
            'status' => 'required|in:Pass,Fail',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $result = MadrasahResult::create([
            'student_id' => $request->student_id,
            'nine_gpa' => $request->nine_gpa,
            'ten_gpa' => $request->ten_gpa,
            'status' => $request->status,
            'added_by' => auth()->id(),
        ]);

        if ($request->status === 'Pass') {
            Student::find($request->student_id)->update(['madrasa_completed' => 1]);
        }

        return response()->json(['message' => 'Madrasah result saved', 'data' => $result], 201);
    }

    public function madrasahUpdate(Request $request, $id)
    {
        $result = MadrasahResult::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nine_gpa' => 'sometimes|numeric|min:0|max:5',
            'ten_gpa' => 'nullable|numeric|min:0|max:5',
            'status' => 'sometimes|in:Pass,Fail',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $result->update($validator->validated());
        return response()->json(['message' => 'Result updated', 'data' => $result]);
    }

    public function madrasahDestroy($id)
    {
        MadrasahResult::findOrFail($id)->delete();
        return response()->json(['message' => 'Result deleted']);
    }
}
