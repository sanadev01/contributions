<?php

namespace App\Repositories;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\GSSRate;
use App\Models\User;
use Exception;
use App\Http\Requests\GSSRateRequest;
class GssRatesRepository
{
    public function get()
    {   
        return GSSRate::all();
    }

    public function store(GSSRateRequest $request)
    {   
        try{

            GSSRate::create($request->validated());

            return true;

        }catch(Exception $exception){
            session()->flash('alert-danger','Error while Saving gss rates');
            return null;
        }
    }

    public function update(GSSRateRequest $request,GSSRate $gssRate)
    {   
        
        try{  
           $gssRate->update($request->validated());
            return true;
        }catch(Exception $exception){
            session()->flash('alert-danger','Error while gss rates');
            return null;
        }
    }

    public function delete(GSSRate $gssRate){

        $gssRate->delete();
        return true;

    }

}