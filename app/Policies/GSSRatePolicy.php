<?php

namespace App\Policies;

use App\Models\GSSRate;
use App\Models\User;
use App\Traits\ByPassAdminCheck;
use Illuminate\Auth\Access\HandlesAuthorization;

class GSSRatePolicy
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
        return $user->hasPermission('view_gssRates');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\GSSRate  $gssRate
     * @return mixed
     */
    public function view(User $user, GSSRate $gssRate)
    {
        return $user->hasPermission('show_gssRate') && $gssRate->user_id == $user->id;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasPermission('create_gssRate');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\GSSRate  $gssRate
     * @return mixed
     */
    public function update(User $user, GSSRate $gssRate)
    {
        return $user->hasPermission('edit_gssRate') && $gssRate->user_id == $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\GSSRate  $gssRate
     * @return mixed
     */
    public function delete(User $user, GSSRate $gssRate)
    {
        return $user->hasPermission('delete_gssRate') && $gssRate->user_id == $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\GSSRate  $gssRate
     * @return mixed
     */
    public function restore(User $user, GSSRate $gssRate)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\GSSRate  $gssRate
     * @return mixed
     */
    public function forceDelete(User $user, GSSRate $gssRate)
    {
        //
    }
}
