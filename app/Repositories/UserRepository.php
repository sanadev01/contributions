<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserRepository
{
    public function get(Request $request, $paginated = true)
    {
        $query = User::user();

        if ( $request->search ){
            $query->where('name','LIKE',"%{$request->search}%")
                ->orWhere('email','LIKE',"%{$request->search}%")
                ->orWhere('pobox_number','LIKE',"%{$request->search}%")
                ->orWhere('phone','LIKE',"%{$request->search}%")
                ->orWhere('account_type','LIKE',"%{$request->search}%");
        }

        $query->latest();

        if ( $paginated )
            return $query->paginate(10);
            
        return $query->get();
    }
}