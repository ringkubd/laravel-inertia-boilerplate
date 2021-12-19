<?php

namespace App\Http\Controllers;

use App\Models\MailBox;
use App\Models\Student;
use App\Models\User;
use Inertia\Inertia;


class MailboxController extends Controller
{
    /**
     * @return \Inertia\Response|never
     */
    public function inbox()
    {
        if (!auth()->user()->hasRole('Super Admin') && !auth()->user()->hasRole('Student')) return abort(403);

        $mailbox = "";
        if (auth()->user()->hasRole('Super Admin')) {
            $mailbox = MailBox::orderBy('created_at', 'DESC')->paginate(25);
        }elseif (auth()->user()->hasRole('Student')){
            $mailbox = MailBox::where('to', auth()->user()->email)->orderBy('created_at', 'DESC')->paginate(25);
        }

        return Inertia::render('MailBox/Inbox', [
            'mails' => $mailbox,
            'students' => auth()->user()->hasRole('Super Admin') ? User::whereHas('roles', function ($q) {
                $q->where('name', 'Student');
            })->select('name', 'email')->get() : []
        ]);
    }

    /**
     * @param MailBox $mailBox
     * @return void
     */

    public function details(MailBox $mailBox){
        if (auth()->user()->hasRole('Student')) {
            $mailBox->update(['seen_at'=> now()]);
        }
        return Inertia::render('MailBox/Details', [
            'mail' => $mailBox
        ]);
    }
}
