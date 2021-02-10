<?php

namespace App\Repositories;

use App\Models\Connect;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConnectReporistory
{
    public function get()
    {
        $query = Connect::query();
        
        if ( Auth::user()->isUser() ){
            $query->where('user_id',Auth::id());
        }

        return $query->paginate(50);
    }

    public function update(Request $request, Connect $connect)
    {
        $connect->update([
            'store_name' => $request->connect_name,
            'default_shipping_service' => $request->default_shipping_service
        ]);

        return true;
    }
}
