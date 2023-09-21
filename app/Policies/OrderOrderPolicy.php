<?php

namespace App\Policies;

use App\Models\OrderOrder;
use App\Models\User;
use App\Traits\ByPassAdminCheck;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrderOrderPolicy
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
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\OrderOrder  $orderOrder
     * @return mixed
     */
    public function view(User $user, OrderOrder $orderOrder)
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
     * @param  \App\Models\OrderOrder  $orderOrder
     * @return mixed
     */
    public function update(User $user, OrderOrder $orderOrder)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\OrderOrder  $orderOrder
     * @return mixed
     */
    public function delete(User $user, OrderOrder $orderOrder)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\OrderOrder  $orderOrder
     * @return mixed
     */
    public function restore(User $user, OrderOrder $orderOrder)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\OrderOrder  $orderOrder
     * @return mixed
     */
    public function forceDelete(User $user, OrderOrder $orderOrder)
    {
        //
    }
}
