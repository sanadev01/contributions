<?php

namespace App\Policies;

use App\Models\AffiliateSale;
use App\Models\User;
use App\Traits\ByPassAdminCheck;
use Illuminate\Auth\Access\HandlesAuthorization;

class AffiliateSalePolicy
{
    use HandlesAuthorization;
    use ByPassAdminCheck;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->hasPermission('affiliate_sale');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\AffiliateSale  $affiliateSale
     * @return mixed
     */
    public function view(User $user, AffiliateSale $affiliateSale)
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
        //
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\AffiliateSale  $affiliateSale
     * @return mixed
     */
    public function update(User $user, AffiliateSale $affiliateSale)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\AffiliateSale  $affiliateSale
     * @return mixed
     */
    public function delete(User $user, AffiliateSale $affiliateSale)
    {
        return $user->hasPermission('delete_affiliate_sale') && $user->id == $affiliateSale->user->id;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\AffiliateSale  $affiliateSale
     * @return mixed
     */
    public function restore(User $user, AffiliateSale $affiliateSale)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\AffiliateSale  $affiliateSale
     * @return mixed
     */
    public function forceDelete(User $user, AffiliateSale $affiliateSale)
    {
        //
    }
}
