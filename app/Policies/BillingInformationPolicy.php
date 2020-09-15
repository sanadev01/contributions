<?php 

namespace App\Policies;

use App\Models\BillingInformation;
use App\Models\User;
use App\Traits\ByPassAdminCheck;
use Illuminate\Auth\Access\HandlesAuthorization;

class BillingInformationPolicy
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
        return $user->hasPermission('view_billingInformations');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\BillingInformation  $billingInformation
     * @return mixed
     */
    public function view(User $user, BillingInformation $billingInformation)
    {
        return $user->hasPermission('show_billingInformation') && $billingInformation->user_id == $user->id;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasPermission('create_billingInformation');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\BillingInformation  $billingInformation
     * @return mixed
     */
    public function update(User $user, BillingInformation $billingInformation)
    {
        return $user->hasPermission('edit_billingInformation') && $billingInformation->user_id == $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\BillingInformation  $billingInformation
     * @return mixed
     */
    public function delete(User $user, BillingInformation $billingInformation)
    {
        return $user->hasPermission('delete_billingInformation') && $billingInformation->user_id == $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\BillingInformation  $billingInformation
     * @return mixed
     */
    public function restore(User $user, BillingInformation $billingInformation)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\BillingInformation  $billingInformation
     * @return mixed
     */
    public function forceDelete(User $user, BillingInformation $billingInformation)
    {
        //
    }
}
