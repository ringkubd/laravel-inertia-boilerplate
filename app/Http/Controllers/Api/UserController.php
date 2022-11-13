<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Api\UserService;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

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


   public function instructor_login(Request $request){
       $validator = Validator::make($request->all(), [
           'email' => 'required|email',
           'password' => 'required',
           'device_name' => 'required',
       ]);

       if ($validator->fails()) {
           return response()->json([
               'status' => 'failed',
               'error' => $validator->errors(),
           ], 419);
       }
       $user = User::where('email', $request->email)->first();
       if (! $user || ! Hash::check($request->password, $user->password) || !$user->hasAnyRole(Role::whereIn('name', ['Instructor', 'Lab Attendant'])->get())) {
           return response()->json([
               'status' => 'failed',
               'error' => [
                   'email' => ['The provided credentials are incorrect.'],
                   'role' => $user->roles->first()
               ],
           ], 403);
       }

       return response()->json(['status' => true, 'token' => $user->createToken($request->device_name)->plainTextToken, 'user' => $user]);
   }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */

   public function store_public_Key(Request $request){
       $validation = Validator::make($request->all(), [
           'public_key' => 'required'
       ]);

       if ($validation->fails()){
           return response()->json([
               'message' => 'Public Key is required.'
           ], 419);
       }
       $user = auth()->user()->update(['public_key' => $request->public_key]);
       return response()->json(['message' => auth()->user()->public_key]);
   }

    /**
     * @param Request $request
     * @return
     */
    public function isValid(Request $request)
    {

        $signature = $request->signature;
        $payload = $request->signature_string;
        if (! $signature) {
            return false;
        }
        $publicKey = auth()->user()->public_key;
        $publicKey = openssl_pkey_get_public( "-----BEGIN PUBLIC KEY-----\n$publicKey\n-----END PUBLIC KEY-----");
        if (!$publicKey) {
            return false;
        }
        $signature = base64_decode($signature);
        $result = openssl_verify($payload, $signature, $publicKey, OPENSSL_ALGO_SHA256);
        if ($result === 1) {
            return response()->json(['status' => $result], 200);
        }
        return response()->json([], 419);
    }
}
