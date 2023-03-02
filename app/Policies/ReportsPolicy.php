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

    public function downloadTrackingReport(User $user)
    {
        return $user->hasPermission('can-download-tracking-report');
    }

    public function viewOrderReport(User $user)
    {
        return $user->hasPermission('order-report');
    }

    public function viewComissionReport(User $user)
    {
        return $user->hasPermission('commission-report');
    }

    public function viewAnjunReport(User $user)
    {
        return $user->hasPermission('anjun-report');
    }

    public function viewKPIReport(User $user)
    {
        return $user->hasPermission('kpi-report');
    }
}
