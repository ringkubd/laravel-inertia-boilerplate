<?php

namespace App\Policies;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class PermissionPolicy
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
        return $user->can('view_permission');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Permission $permission
     * @return bool
     */
    public function view(User $user, Permission $permission)
    {
        return $user->can('view_permission');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user)
    {
        return $user->can('create_permission');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Permission $permission
     * @return bool
     */
    public function update(User $user, Permission $permission)
    {
        return $user->can('update_permission');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Permission $permission
     * @return bool
     */
    public function delete(User $user, Permission $permission)
    {
        return $user->can('delete_permission');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param Permission $permission
     * @return Response|bool
     */
    public function restore(User $user, Permission $permission)
    {
        return $user->can('Super Admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param Permission $permission
     * @return bool
     */
    public function forceDelete(User $user, Permission $permission): bool
    {
        return $user->can('Super Admin');
    }
}
