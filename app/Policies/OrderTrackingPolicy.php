<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Traits\ByPassAdminCheck;
class OrderTrackingPolicy
{
    use HandlesAuthorization,ByPassAdminCheck;
    
    public function view(User $user)
    {
        return $user->hasPermission('view_tracking');
    }
}
