<?php

namespace App\Http\Controllers\Api\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'sometimes|email|max:255|unique:users,email,' . auth()->id(),
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $user = auth()->user();
        $user->update($validator->validated());

        if ($user->teacher) {
            $teacherUpdate = [];
            if ($request->name) $teacherUpdate['name'] = $request->name;
            if ($request->phone) $teacherUpdate['mobile'] = $request->phone;
            if (!empty($teacherUpdate)) {
                $user->teacher->update($teacherUpdate);
            }
        }

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user->load('teacher', 'madrasah'),
        ]);
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'Current password is incorrect'], 422);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return response()->json(['message' => 'Password changed successfully']);
    }
}
