<?php

namespace App\Http\Middleware;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function version(Request $request)
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function share(Request $request)
    {
        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $request->user()
            ],
            'user' => $request->user(),
            'active_support' => $request->user()?->activeSupport,
            'menu_permission' => [
                'user_management' => $request->user()?->hasRole('Super Admin') ?? false,
                'support' => $request->user()?->hasRole('Super Admin') || $request->user()?->hasRole('Admin') ?? false,
                'paymentSlip' => $request->user()?->hasRole('Super Admin') || $request->user()?->hasRole('Admin') || $request->user()?->hasPermissionTo('view_payment-slip'),
                'blog_management' => $request->user()?->hasRole('Super Admin') ?? false,
                'madrasah' => $request->user()?->hasPermissionTo('view_madrasa') || $request->user()?->hasRole('Super Admin'),
                'teacher' => $request->user()?->hasPermissionTo('view_teacher') || $request->user()?->hasRole('Super Admin'),
                'madrasah_student' => $request->user()?->hasPermissionTo('view_madrasa_student') || $request->user()?->hasRole('Super Admin'),
                'madrasah_result' => $request->user()?->hasPermissionTo('view_madrasa_result') || $request->user()?->hasRole('Super Admin'),
                'polytechnic' => $request->user()?->hasPermissionTo('view_polytechnic') || $request->user()?->hasRole('Super Admin'),
                'polytechnic_student' => $request->user()?->hasPermissionTo('view_polytechnic_student') || $request->user()?->hasRole('Super Admin'),
                'polytechnic_result' => $request->user()?->hasPermissionTo('view_polytechnic_result') || $request->user()?->hasRole('Super Admin'),
                'trade' => $request->user()?->hasPermissionTo('view_trade') || $request->user()?->hasRole('Super Admin'),
                'academic_session' => $request->user()?->hasPermissionTo('view_academic_session') || $request->user()?->hasRole('Super Admin'),
                'invoice' => $request->user()?->hasPermissionTo('view_invoice') || $request->user()?->hasRole('Super Admin'),
                'fee' => $request->user()?->hasPermissionTo('view_fee') || $request->user()?->hasRole('Super Admin'),
                'admission' => $request->user()?->hasPermissionTo('view_admission') || $request->user()?->hasRole('Super Admin'),
                'mail_inbox' => $request->user()?->hasRole('student') || $request->user()?->hasRole('Super Admin'),
                'super_admin' => $request->user()?->hasRole('Super Admin'),
                'student' => $request->user()?->hasRole('Student'),
                'teacher_attendance' => !$request->user()?->hasRole('Student'),
                'app_notice' => $request->user()?->hasPermissionTo('create_notice') || $request->user()?->hasRole('Super Admin') || $request->user()?->hasPermissionTo('delete_notice') || $request->user()?->hasPermissionTo('view_notice'),
                'app_attendance' => $request->user()?->hasPermissionTo('create_app_attendance') || $request->user()?->hasRole('Super Admin') || $request->user()?->hasPermissionTo('delete_app_attendance') || $request->user()?->hasPermissionTo('view_app_attendance'),
            ],
            'flash' => [
                'message' => fn () => $request->session()->get('message'),
                'error' => fn () => $request->session()->get('error'),
                'success' => fn () => $request->session()->get('success')
            ],
            'online' => User::where('online', 1)
                    ->where('id', '!=',$request->user()->id ?? "")
                    ->with(['conversation' => function ($q){
                        $q->whereHas('conversationUsers', function ($q) {
                            $q->where('user_id',  request()->user()->id ?? "");
                        });
                    }])
                    ->get() ?? [],
            'offline' => User::where('online', 0)
                    ->where('id','!=', $request->user()->id?? "")
                    ->with(['conversation' => function ($q){
                        $q->whereHas('conversationUsers', function ($q) {
                            $q->where('user_id',  request()->user()->id ?? "");
                        });
                    }])
                    ->get() ?? [],
            'public_menus' => [
                "About Us" => url('about_us')
            ],
            'urlPrev'	=> function() {
                if (url()->previous() !== route('login') && url()->previous() !== '' && url()->previous() !== url()->current()) {
                    return url()->previous();
                }else {
                    return '#'; // used in javascript to disable back button behavior
                }
            },
        ]);
    }
}
