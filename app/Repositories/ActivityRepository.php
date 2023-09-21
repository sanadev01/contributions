<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Models\Activity;

class ActivityRepository
{
    public function get(Request $request,$paginate = true,$pageSize=50,$orderBy = 'id',$orderType='DESC')
    {

        $query = Activity::query();
        
        $user_id = request('id');
        
        if ( $user_id ){
            $query->where('causer_id',$user_id);
        }

        if ( Auth::user()->isUser() ){
            $query->where('causer_id',Auth::id());
        }

        if ( $request->date ){
            $query->where(function($query) use($request){
                return $query->where('created_at', 'LIKE', "%{$request->date}%");
            });
        }
        
        if ( $request->name ){
            $query->whereHasMorph('causer', User::class ,function($query) use($request) {
                return $query->where('name', 'LIKE', "%{$request->name}%");
            });
        }
        
        if ( $request->model ){
            $query->where(function($query) use($request){
                return $query->where('subject_type', 'LIKE', "%{$request->model}%");
            });
        }
        if ( $request->content ){
            $query->where(function($query) use($request){
                return $query->where('properties', 'LIKE', "%{$request->content}%");
            });
        }

        $activities = $query
        ->orderBy($orderBy,$orderType);

        return $paginate ? $activities->paginate($pageSize) : $activities->get();
    }


}