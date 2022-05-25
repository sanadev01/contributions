<?php

namespace App\Repositories;

use Exception;
use App\Models\User;
use App\Models\Ticket;
use App\Models\Document;
use Illuminate\Http\Request;
use App\Models\TicketComment;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\Warehouse\AccrualRate;
use App\Mail\User\NewTicketCommentAdded;

class AccrualRateRepository
{
    public function get(Request $request)
    {   
        $query = AccrualRate::query();

        if($request->service){
            $query->where('service', $request->service);
        }
        if($request->country_id){
            $query->where('country_id', $request->country_id);
        }
        if($request->weight){
            $query->where('weight', 'LIKE', "%{$request->weight}%");
        }
        if($request->cwb){
            $query->where('cwb', 'LIKE', "%{$request->cwb}%");
        }
        if($request->gru){
            $query->where('gru', 'LIKE', "%{$request->gru}%");
        }
        if($request->commission){
            $query->where('commission', 'LIKE', "%{$request->commission}%");
        }

        return $query->get();
    }

}