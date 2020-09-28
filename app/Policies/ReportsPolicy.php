<?php

namespace App\Policies;

use App\Models\User;
use App\Traits\ByPassAdminCheck;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReportsPolicy
{
    use HandlesAuthorization,
        ByPassAdminCheck;

    public function viewUserShipmentReport(User $user)
    {
        return $user->hasPermission('view-user-shipment-report');
    }
}
