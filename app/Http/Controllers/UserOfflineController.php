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
        $userC =  User::query()
            ->where('id', $user->id)
            ->with(['conversation' => function ($q) use($user){
                $q->whereHas('conversationUsers', function ($q) use($user){
                    $q->where('user_id',  $user->id);
                });
            }])
            ->first();
        broadcast(new OfflineEvent($userC));
    }
}
