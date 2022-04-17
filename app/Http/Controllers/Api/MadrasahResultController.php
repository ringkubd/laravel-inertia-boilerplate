<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\ResultResource;
use App\Models\MadrasahResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MadrasahResultController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user();
        $result = MadrasahResult::where('student_id', $user?->student?->id)->first();
        return response()->json([
            'success' => true,
            'data' =>new ResultResource($result),
        ], 201);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'nine_gpa' => 'required'
        ]);
        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validate->messages(),
            ], 422);
        }
        $data = $request->all();
        $data['student_id'] = auth()->user()?->student?->id;
        $result = MadrasahResult::create($data);
        return response()->json([
            'success' => true,
            'data' =>new ResultResource($result),
        ], 201);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $result = MadrasahResult::find($id);
        return response()->json([
            'success' => true,
            'data' =>new ResultResource($result),
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $validate = Validator::make($request->all(), [
            'nine_gpa' => 'required'
        ]);
        $result = MadrasahResult::find($id);
        if (!$result) {
            return response()->json([
                'success' => false,
                'error' => "Result not found.",
            ], 404);
        }
        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validate->messages(),
            ], 422);
        }
        $data = $request->all();
        $result::update($data);
        return response()->json([
            'success' => true,
            'data' => new ResultResource($result),
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $result = MadrasahResult::find($id);
        if (!$result) {
            return response()->json([
                'success' => false,
                'error' => "Result not found.",
            ], 404);
        }
        $result->destroy();
        return response()->json([
            'success' => true,
            'data' => [],
        ], 201);
    }
}
