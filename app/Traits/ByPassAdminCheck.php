<?php 

namespace App\Traits;

trait ByPassAdminCheck{

    public function before($user, $ability)
    {
        if ( $user->isAdmin() ){
            return true;
        }
    }

}