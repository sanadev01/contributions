<?php

namespace App\Policies;

use App\Models\ProfitPacakge;
use App\Models\User;
use App\Traits\ByPassAdminCheck;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProfitPackagePolicy
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
        return $user->hasPermission('view_profitPacakges');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ProfitPacakge  $profitPacakge
     * @return mixed
     */
    public function view(User $user, ProfitPacakge $profitPacakge)
    {
        return $user->hasPermission('show_profitPacakge') && $profitPacakge->user_id == $user->id;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasPermission('create_profitPacakge');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ProfitPacakge  $profitPacakge
     * @return mixed
     */
    public function update(User $user, ProfitPacakge $profitPacakge)
    {
        return $user->hasPermission('edit_profitPacakge') && $profitPacakge->user_id == $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ProfitPacakge  $profitPacakge
     * @return mixed
     */
    public function delete(User $user, ProfitPacakge $profitPacakge)
    {
        return $user->hasPermission('delete_profitPacakge') && $profitPacakge->user_id == $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ProfitPacakge  $profitPacakge
     * @return mixed
     */
    public function restore(User $user, ProfitPacakge $profitPacakge)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ProfitPacakge  $profitPacakge
     * @return mixed
     */
    public function forceDelete(User $user, ProfitPacakge $profitPacakge)
    {
        //
    }

    public function userSellingRates(User $user)
    {
        return $user->hasPermission('user_selling_rates');

    }
}
