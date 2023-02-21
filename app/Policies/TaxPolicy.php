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
    public function refund(User $user,Tax $tax)
    {   
        return $user->hasPermission('refund_tax') && $tax->user_id == $user->id;
    }
 
}
