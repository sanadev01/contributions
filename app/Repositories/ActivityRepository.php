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
        $query =Activity::with(['causer' => function($query) {
            $query->select('id', 'name');  
        }]);
        if ($user_id = request('id')){
            $query->where('causer_id', $user_id);
        } 
        if (Auth::user()->isUser()){
            $query->where('causer_id', Auth::id());
        }
        if ($date = $request->date){
             $query->whereDate('created_at', 'LIKE', "%{$date}%");
        }
        if ($name = $request->name){
            $query->whereHasMorph('causer', User::class, function ($query) use ($name) {
                $query->where('name', 'LIKE', "%{$name}%");
            });
        }
        if ($model = $request->model) {
            $query->where('subject_type', 'LIKE', "%{$model}%");
        }
        if ($content = $request->content) {
            $query->where('properties', 'LIKE', "%{$content}%");
        }
        if ($description = $request->description) {
            $query->where('description', 'LIKE', "%{$description}%");
        }
        $activities = $query->orderBy($orderBy, $orderType);
        return ($paginate ? $activities->paginate($pageSize) : $activities->get());
    }


}