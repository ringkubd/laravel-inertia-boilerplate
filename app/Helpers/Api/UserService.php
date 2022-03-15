<?php

namespace App\Helpers\Api;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

class UserService
{
    public $email, $password;

    public function __constructor($email, $password){
        $this->email = $email;
        $this->password = $password;
    }

    public function validationInput(){
        $validator = Validator::make([
            'email' => $this->email,
            'password' => $this->password
        ], [
            'email' => ['required', 'email:rfc,dns', 'unique:users'],
            'password' => ['required', 'string', Password::min(8)]
        ]);

        if ($validator->failed()) {
            return ['status' => false, 'message' => $validator->messages()];
        }else{
            return ['status' => true];
        }
    }

    public function register($deviceName){
        $validate = $this->validationInput();

        if ($validate['status'] == false) {
            return $validate;
        }else{
            $user = User::create(['email' => $this->email, 'password' => Hash::make($this->password)]);
            $token = $user->createToken($deviceName)->plainTextToken;
            return ['status' => true, 'token' => $token, 'user' => $user];
        }
    }

}
