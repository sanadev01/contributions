<?php

namespace App\Repositories;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\HandlingService;
use App\Models\User;
use Exception;

class HandlingServiceRepository
{
    public function get()
    {   
        $services = HandlingService::all();
        return $services;

    }

    public function store(Request $request)
    {   
        try{

            HandlingService::create(
                $request->only([
                    'name',
                    'cost',
                    'price',
                ])
            );

            return true;

        }catch(Exception $exception){
            session()->flash('alert-danger','Error while Saving HandlingService');
            return null;
        }
    }

    public function update(Request $request,HandlingService $service)
    {   
        
        try{
            
            $service->update(
                $request->only([
                    'name',
                    'cost',
                    'price',
                ])
            );

            return true;

        }catch(Exception $exception){
            session()->flash('alert-danger','Error while HandlingService');
            return null;
        }
    }

    public function delete(HandlingService $service){

        $service->delete();
        return true;

    }

}