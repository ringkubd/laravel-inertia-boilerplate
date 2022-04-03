<?php

namespace Anwar\AutoLogin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AutoLogin
{
    // Build your next great package.
    public function generateToken($email, $password){
        $user = \DB::table(config('autologin.users_table', 'users'))->where('email', $email);
        $userData = $user->first();
        if ($user && Hash::check($password, $userData->password)){
            $user->update([
                'app_token' => Str::random(60),
                'app_reference' => request()->url(),
            ]);
            $userObject = $user->first();
            $userObject->status = true;
            return response()->json($userObject);
        }
        return response()->json([
            'status' => false,
            'message' => "Check your email and password."
        ],403);
    }
}
