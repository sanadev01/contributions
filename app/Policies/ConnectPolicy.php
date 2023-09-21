<?php

namespace App\Policies;

use App\Models\Connect;
use App\Models\User;
use App\Traits\ByPassAdminCheck;
use Illuminate\Auth\Access\HandlesAuthorization;

class ConnectPolicy
{
    use HandlesAuthorization,
        ByPassAdminCheck;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->hasPermission('view_connects');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Connect  $connect
     * @return mixed
     */
    public function view(User $user, Connect $connect)
    {
        //
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasPermission('create_connect');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Connect  $connect
     * @return mixed
     */
    public function update(User $user, Connect $connect)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Connect  $connect
     * @return mixed
     */
    public function delete(User $user, Connect $connect)
    {
        return $user->hasPermission('delete_connect') && $user->id == $connect->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Connect  $connect
     * @return mixed
     */
    public function restore(User $user, Connect $connect)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Connect  $connect
     * @return mixed
     */
    public function forceDelete(User $user, Connect $connect)
    {
        //
    }
}
