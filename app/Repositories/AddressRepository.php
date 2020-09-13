<?php

namespace App\Repositories;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Address;
use Exception;

class AddressRepository
{
    public function get()
    {
        $query = Address::query()->has('user');

        if ( Auth::user()->isUser() ){
            $query->where('user_id',Auth::id());
        }

        $addresses = $query
            ->latest()
            ->get();

        return $addresses;

    }

    public function store(Request $request)
    {   
        try{

            $address = Auth::user()->addresses()->create(
                $request->only(['first_name','last_name','email', 'phone', 'city', 'street_no','address', 'address2', 'country_id', 'state_id', 'account_type', 'tax_id', 'zipcode'])
            );

            return true;

        }catch(Exception $exception){
            session()->flash('alert-danger','Error while Saving Address');
            return null;
        }
    }

    public function update(Request $request,Address $Address)
    {   
        
        try{

            // if (! $request->has('default')) {
            //     $request->merge([
            //         'default' => false
            //     ]);
            // }

            // if ($request->has('default')) {
            //     Auth::user()->addresses()->update([
            //         'default' => false
            //     ]);
            // }

            $Address->refresh();

            $Address->update(
                $request->only(['first_name','last_name','email', 'phone', 'city', 'street_no','address', 'address2', 'country_id', 'state_id', 'account_type', 'tax_id', 'zipcode'])
            );

            return true;

        }catch(Exception $exception){
            session()->flash('alert-danger','Error while Address');
            return null;
        }
    }

}