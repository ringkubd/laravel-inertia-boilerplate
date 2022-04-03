<?php

namespace Anwar\AutoLogin\Controllers;

use Anwar\AutoLogin\AutoLogin;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class AutoLoginController extends BaseController
{
    public function generateToken(Request $request){
        $validate = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required'
        ]);

        if ($validate->fails()) {
            return [
                'status' => false,
                'error' => $validate->errors(),
            ];
        }
        $email =$request->email;
        $password = $request->password;
        $autologin = new AutoLogin();
        return $autologin->generateToken($email, $password);
    }

    public function autoLogin(Request $request){
        $validate = Validator::make($request->all(), [
            'app_token' => 'required',
        ]);

        if ($validate->fails()) {
            return [
                'status' => false,
                'error' => $validate->errors(),
            ];
        }

        $app_token = $request->app_token;
        $user = DB::table(config('autologin.users_table'))->where('app_token', $app_token)
//            ->where('app_reference', $request->url())
            ->first();
        if ($user) {
            $u = auth()->loginUsingId($user->id);
            return redirect(config('autologin.redirect_to'));
        }else{
            return [
                'status' => false,
                'error' => "Login Failed",
            ];
        }
    }
}
