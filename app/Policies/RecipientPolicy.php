<?php

namespace App\Policies;

use App\Models\Recipient;
use App\Models\User;
use App\Traits\ByPassAdminCheck;
use Illuminate\Auth\Access\HandlesAuthorization;

class RecipientPolicy
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
     * @param  \App\Models\Recipient  $recipient
     * @return mixed
     */
    public function view(User $user, Recipient $recipient)
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
     * @param  \App\Models\Recipient  $recipient
     * @return mixed
     */
    public function update(User $user, Recipient $recipient)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Recipient  $recipient
     * @return mixed
     */
    public function delete(User $user, Recipient $recipient)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Recipient  $recipient
     * @return mixed
     */
    public function restore(User $user, Recipient $recipient)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Recipient  $recipient
     * @return mixed
     */
    public function forceDelete(User $user, Recipient $recipient)
    {
        //
    }
}
