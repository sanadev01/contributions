<?php

namespace App\Repositories;

use App\Models\BillingInformation;
use App\Models\Country;
use App\Models\State;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BillingInformationRepository
{
    public function get($paginate = 10)
    {
        $billingInformations = BillingInformation::query();
        
        if ( !Auth::user()->isAdmin() ){
            $billingInformations->where('user_id',Auth::id());
        }

        $billingInformations->latest();

        return $billingInformations->paginate(10);

    }

    public function store(Request $request)
    {   
        try{
            BillingInformation::create([
                'user_id' => Auth::id(),
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'card_no' => $request->card_no,
                'expiration' => $request->expiration,
                'cvv' => $request->cvv,
                'phone' => $request->phone,
                'address' => $request->address,
                'state' => State::find($request->state)->code,
                'zipcode' => $request->zipcode,
                'country' => Country::find($request->country)->name
            ]);

            return true;
        }catch(Exception $exception){
            session()->flash('alert-danger','Error while Saving Billing Information');
            return null;
        }
    }

    public function update(Request $request,BillingInformation $billingInformation)
    {
        try{
            $billingInformation->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'expiration' => $request->expiration,
                'phone' => $request->phone,
                'address' => $request->address,
                'state' => State::find($request->state)->code,
                'zipcode' => $request->zipcode,
                'country' => Country::find($request->country)->name
            ]);

            if($request->has('card_no')){
                $billingInformation->update([
                    'card_no' => $request->card_no
                ]);
            }

            if($request->has('cvv')){
                $billingInformation->update([
                    'cvv' => $request->cvv
                ]);
            }

            return true;
        }catch(Exception $exception){
            session()->flash('alert-danger','Error while Billing Information');
            return null;
        }
    }

}