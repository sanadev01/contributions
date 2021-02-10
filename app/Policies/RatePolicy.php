<?php

namespace App\Policies;

use App\Models\Rate;
use App\Models\User;
use App\Traits\ByPassAdminCheck;
use Illuminate\Auth\Access\HandlesAuthorization;

class RatePolicy
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
        return $user->hasPermission('view_rates');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Rate  $rate
     * @return mixed
     */
    public function view(User $user, Rate $rate)
    {
        return $user->hasPermission('show_rate') && $rate->user_id == $user->id;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasPermission('create_rate');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Rate  $rate
     * @return mixed
     */
    public function update(User $user, Rate $rate)
    {
        return $user->hasPermission('edit_rate') && $rate->user_id == $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Rate  $rate
     * @return mixed
     */
    public function delete(User $user, Rate $rate)
    {
        return $user->hasPermission('delete_rate') && $rate->user_id == $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Rate  $rate
     * @return mixed
     */
    public function restore(User $user, Rate $rate)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Rate  $rate
     * @return mixed
     */
    public function forceDelete(User $user, Rate $rate)
    {
        //
    }

    public function updatefixedRates()
    {
        return false;
    }
}
