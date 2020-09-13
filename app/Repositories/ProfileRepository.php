<?php

namespace App\Repositories;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Profie;
use App\Models\User;
use Exception;


class ProfileRepository
{

    public function store(Request $request)
    {   
        try{

            $user = User::find(Auth::id());

            $user->update([
                'name' => $request->name,
                'last_name' => $request->last_name,
                'phone' => $request->phone,
                'state_id' => $request->state_id,
                'country_id' => $request->country_id,
                'city' => $request->city,
                'street_no' => $request->street_no,
                'address' => $request->address,
                'address2' => $request->address2,
                'tax_id' => $request->tax_id,
                'zipcode' => $request->zipcode,
                'locale' => $request->locale,
            ]);
    
            if ($request->password) {
                $user->update([
                    'password' => bcrypt($request->password)
                ]);
            }
    
            // if (Auth::user()->isAdmin()) {
            //     $pobox = PoBox::first() ?? new PoBox();
            //     $pobox->address = $request->pobox_address;
            //     $pobox->extra_data = [
            //         'city' => $request->pobox_city, 
            //         'state' => $request->pobox_state, 
            //         'country' => $request->pobox_country, 
            //         'zipcode' => $request->pobox_zipcode, 
            //         'phone' => $request->pobox_phone, 
            //     ];
    
            //     $pobox->save();
            // }
    

            return true;

        }catch(Exception $exception){
            session()->flash('alert-danger','Error while Saving Profie');
            return null;
        }
    }

}