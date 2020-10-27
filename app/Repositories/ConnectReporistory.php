<?php

namespace App\Repositories;

use App\Models\Connect;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ConnectReporistory extends Model
{
    public function get()
    {
        $query = Connect::query();
        
        if ( Auth::user()->isUser() ){
            return $query->where('user_id',Auth::id())->with(['user']);
        }

        return $query->paginate(50);
    }
}
