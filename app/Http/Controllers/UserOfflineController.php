<?php

namespace App\Http\Controllers;

use App\Events\OfflineEvent;
use App\Models\User;
use Illuminate\Http\Request;

class UserOfflineController extends Controller
{
    public function __invoke(User $user)
    {
        $user->online = 0;
        $user->save();
        broadcast(new OfflineEvent($user));
    }
}
