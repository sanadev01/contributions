<?php

namespace App\Policies;

use App\Models\ShippingService;
use App\Models\User;
use App\Traits\ByPassAdminCheck;
use Illuminate\Auth\Access\HandlesAuthorization;

class ShippingServicePolicy
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
        return $user->hasPermission('view_shippingServices');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ShippingService  $shippingService
     * @return mixed
     */
    public function view(User $user, ShippingService $shippingService)
    {
        return $user->hasPermission('show_shippingService') && $shippingService->user_id == $user->id;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasPermission('create_shippingService');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ShippingService  $shippingService
     * @return mixed
     */
    public function update(User $user, ShippingService $shippingService)
    {
        return $user->hasPermission('edit_shippingService') && $shippingService->user_id == $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ShippingService  $shippingService
     * @return mixed
     */
    public function delete(User $user, ShippingService $shippingService)
    {
        return $user->hasPermission('delete_shippingService') && $shippingService->user_id == $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ShippingService  $shippingService
     * @return mixed
     */
    public function restore(User $user, ShippingService $shippingService)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ShippingService  $shippingService
     * @return mixed
     */
    public function forceDelete(User $user, ShippingService $shippingService)
    {
        //
    }
}
