<?php

namespace App\Policies;

use App\Models\Tax;
use App\Models\User;
use App\Traits\ByPassAdminCheck;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaxPolicy
{
    use HandlesAuthorization, ByPassAdminCheck;

   
    public function view(User $user)
    {   
        return $user->hasPermission('view_tax');
    }
    public function update(User $user,Tax $tax)
    {   
        return $user->hasPermission('update_tax') && $tax->user_id == $user->id;
    }
    public function create(User $user)
    {   
        return $user->hasPermission('create_tax');
    }

       
    public function viewAdjustment(User $user)
    {   
        return $user->hasPermission('view_adjustment');
    }
    public function updateAdjustment(User $user,Tax $tax)
    {   
        return $user->hasPermission('update_adjustment') && $tax->user_id == $user->id;
    }
    public function createAdjustment(User $user)
    {   
        return $user->hasPermission('create_adjustment');
    }

    public function refund(User $user,Tax $tax)
    {   
        return $user->hasPermission('refund_tax') && $tax->user_id == $user->id;
    }
 
}
