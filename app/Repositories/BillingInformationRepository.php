<?php

namespace App\Repositories;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\BillingInformation;
use Exception;

class BillingInformationRepository
{
    public function get()
    {
        $billingInformations = BillingInformation::orderBy('id','desc')->get();
        return $billingInformations;

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
                'state' => $request->state,
                'zipcode' => $request->zipcode,
                'country' => $request->country
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
                'state' => $request->state,
                'zipcode' => $request->zipcode,
                'country' => $request->country
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