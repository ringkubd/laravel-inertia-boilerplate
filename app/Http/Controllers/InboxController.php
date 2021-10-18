<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InboxController extends Controller
{
    public function inbox(){
        $friends = User::where('id','!=' ,auth()->user()->id)->with('conversation')->get();
        return Inertia::render('Conversation/inbox', [
            'friends' => $friends
        ]);
    }
}
