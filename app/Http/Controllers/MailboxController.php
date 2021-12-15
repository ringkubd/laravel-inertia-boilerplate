<?php

namespace App\Http\Controllers;

use App\Models\MailBox;
use App\Models\Student;
use App\Models\User;
use Inertia\Inertia;


class MailboxController extends Controller
{
    public function inbox()
    {
        $mailbox = MailBox::orderBy('created_at', 'DESC')->get();

        return Inertia::render('MailBox/Inbox', [
            'mails' => $mailbox,
            'students' => auth()->user()->hasRole('Super Admin') ? User::whereHas('roles', function ($q) {
                $q->where('name', 'Student');
            })->select('name', 'email')->get() : []
        ]);
    }
}
