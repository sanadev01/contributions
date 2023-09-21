<?php

namespace App\Policies;

use App\Models\HandlingService;
use App\Models\User;
use App\Traits\ByPassAdminCheck;
use Illuminate\Auth\Access\HandlesAuthorization;

class HandlingServicePolicy
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
        return $user->hasPermission('view_handlingServices');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\HandlingService  $handlingService
     * @return mixed
     */
    public function view(User $user, HandlingService $handlingService)
    {
        return $user->hasPermission('show_handlingService') && $handlingService->user_id == $user->id;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasPermission('create_handlingService');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\HandlingService  $handlingService
     * @return mixed
     */
    public function update(User $user, HandlingService $handlingService)
    {
        return $user->hasPermission('edit_handlingService') && $handlingService->user_id == $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\HandlingService  $handlingService
     * @return mixed
     */
    public function delete(User $user, HandlingService $handlingService)
    {
        return $user->hasPermission('delete_handlingService') && $handlingService->user_id == $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\HandlingService  $handlingService
     * @return mixed
     */
    public function restore(User $user, HandlingService $handlingService)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\HandlingService  $handlingService
     * @return mixed
     */
    public function forceDelete(User $user, HandlingService $handlingService)
    {
        //
    }
}
