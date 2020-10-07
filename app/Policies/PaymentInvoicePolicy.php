<?php

namespace App\Policies;

use App\Models\PaymentInvoice;
use App\Models\User;
use App\Traits\ByPassAdminCheck;
use Illuminate\Auth\Access\HandlesAuthorization;

class PaymentInvoicePolicy
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
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PaymentInvoice  $paymentInvoice
     * @return mixed
     */
    public function view(User $user, PaymentInvoice $paymentInvoice)
    {
        return $user->id == $paymentInvoice->paid_by;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PaymentInvoice  $paymentInvoice
     * @return mixed
     */
    public function update(User $user, PaymentInvoice $paymentInvoice)
    {
        return $user->id == $paymentInvoice->paid_by;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PaymentInvoice  $paymentInvoice
     * @return mixed
     */
    public function delete(User $user, PaymentInvoice $paymentInvoice)
    {
        return true;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PaymentInvoice  $paymentInvoice
     * @return mixed
     */
    public function restore(User $user, PaymentInvoice $paymentInvoice)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PaymentInvoice  $paymentInvoice
     * @return mixed
     */
    public function forceDelete(User $user, PaymentInvoice $paymentInvoice)
    {
        //
    }
}
