<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;
use App\Traits\ByPassAdminCheck;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrderPolicy
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
     * @param  \App\Models\Order  $order
     * @return mixed
     */
    public function view(User $user, Order $order)
    {
        return $user->hasPermission('view_parcel') && $user->id == $order->user_id;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasPermission('create_parcel');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Order  $order
     * @return mixed
     */
    public function update(User $user, Order $order)
    {
        return $user->hasPermission('update_parcel') && $user->id == $order->user_id && !$order->isPaid() && !$order->isConsolidated();
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Order  $order
     * @return mixed
     */
    public function delete(User $user, Order $order)
    {
        return $user->hasPermission('delete_parcel') && $user->id == $order->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Order  $order
     * @return mixed
     */
    public function restore(User $user, Order $order)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Order  $order
     * @return mixed
     */
    public function forceDelete(User $user, Order $order)
    {
        //
    }

    public function updateConsolidation(User $user, Order $order)
    {
        return $user->id == $order->user_id && !$order->isPaid() && !$order->isShipmentAdded() && $order->isConsolidated();
    }

    public function addWarehouseNumber(User $user)
    {
        return $user->hasPermission('add_parcel_warehouse_number');
    }

    public function addShipmentDetails(User $user)
    {
        return $user->hasPermission('add_parcel_shipment_details');
    }

    public function editShipmentDetails(User $user,Order $order)
    {
        return $user->hasPermission('edit_parcel_shipment_details') && $user->id == $order->user_id && !$order->isConsolidated();
    }

    public function updateOrder(User $user,Order $order)
    {
        return $user->hasPermission('edit_order') && $order->user_id == $user->id && !$order->isPaid();
    }

    public function canPrintLable(User $user,Order $order)
    {
        return $user->hasPermission('print_label') && $order->user_id == $user->id && $order->isPaid();
    }

    public function importExcel(User $user)
    {
        return $user->hasPermission('import_excel');
    }

}
