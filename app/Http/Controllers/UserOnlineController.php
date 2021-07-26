<?php

namespace App\Http\Controllers;

use App\Events\OnlineEvent;
use App\Models\User;
use Illuminate\Http\Request;

class UserOnlineController extends Controller
{
    public function __invoke(User $user)
    {
        $user->online = 1;
        $user->save();
        broadcast(new OnlineEvent($user));
    }
}
