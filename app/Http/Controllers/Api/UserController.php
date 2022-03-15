<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Api\UserService;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
   public function register(Request $request){
        $response = (new UserService($request->email, $request->password))->register($request->devicename);
        return $response;
   }

   public function login(Request $request){
       $validator = Validator::make($request->all(), [
           'email' => 'required|email',
           'password' => 'required',
           'device_name' => 'required',
       ]);

       if ($validator->fails()) {
           return [
               'status' => 'failed',
               'error' => $validator->errors(),
           ];
       }

       $user = User::where('email', $request->email)->first();

       if (! $user || ! Hash::check($request->password, $user->password)) {
           return [
               'status' => 'failed',
               'error' => [
                   'email' => ['The provided credentials are incorrect.'],
               ],
           ];
       }

       return ['status' => true, 'token' => $user->createToken($request->device_name)->plainTextToken, 'user' => $user];
   }
}
