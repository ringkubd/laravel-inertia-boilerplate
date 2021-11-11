<?php

namespace App\Http\Controllers;

use App\Http\Resources\ActivityResource;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ActivityLogController extends Controller
{
    public function index(User $user)
    {
        if ($user) {
            $activities = $user->activity()->latest()->get();
        }else{
            $activities = User::fist()->activity()->latest()->get();
        }

        return Inertia::render('Activity/Activity', [
            'users' => User::paginate(),
            'activities' => ActivityResource::collection($activities),
            'active_user' => $user ?? User::fist()
        ]);
    }
}
