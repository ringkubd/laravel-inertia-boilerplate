<?php

namespace App\Policies;

use App\Models\Trade;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class TradePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('view_trade');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Trade $trade
     * @return void
     */
    public function view(User $user, Trade $trade)
    {
        //
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return Response|bool
     */
    public function create(User $user)
    {
        return $user->hasPermissionTo('create_trade');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Trade $trade
     * @return Response|bool
     */
    public function update(User $user, Trade $trade)
    {
        return $user->hasPermissionTo('update_trade');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Trade $trade
     * @return Response|bool
     */
    public function delete(User $user, Trade $trade)
    {
        return $user->hasPermissionTo('delete_trade');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param Trade $trade
     * @return bool
     */
    public function restore(User $user, Trade $trade)
    {
        return $user->hasRole('Super Admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param Trade $trade
     * @return bool
     */
    public function forceDelete(User $user, Trade $trade)
    {
        return $user->hasRole('Super Admin');
    }
}
