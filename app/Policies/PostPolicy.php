<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Log;

class PostPolicy
{
    use HandlesAuthorization;

    /**
     * @param User $user
     * @return bool|void
     */
    public function viewIndex(User $user){
        if ($user->can('view_post')) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the post.
     * @param User|null $user
     * @param Post $post
     * @return bool
     */

    public function show(?User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the post.
     * @param User|null $user
     * @param Post $post
     * @return bool
     */

    public function view(?User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create posts.
     * @param User $user
     * @return bool
     */

    public function create(User $user): bool
    {
        if ($user->can('create_post')) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can update the post.
     *
     * @param User $user
     * @param Post $post
     * @return bool
     */

    public function update(User $user, Post $post): bool
    {
        if ($user->can('update_post')) {
            return $user->id === $post->author;
        }
        return $user->can('update_post');
    }

    /**
     * Determine whether the user can delete the post.
     *
     * @param User $user
     * @param Post $post
     * @return bool|void
     */
    public function delete(User $user, Post $post)
    {
        if ($user->can('delete_post')) {
            return $user->id === $post->author;
        }

        if ($user->can('delete_post')) {
            return true;
        }
    }
}
