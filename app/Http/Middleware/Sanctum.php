<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Sanctum
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $bearer = $request->bearerToken();
        if ($token = \DB::table('personal_access_tokens')->where('token',hash('sha256',$bearer))->first())
        {
            if ($user = \App\User::find($token->tokenable_id))
            {
                \Auth::login($user);
                return $next($request);
            }
        }

        return response()->json([
            'success' => false,
            'error' => $request->header('Authorization'),
        ]);
    }
}
