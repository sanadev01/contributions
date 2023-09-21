<?php

namespace App\Repositories;

use App\Models\Document;
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
                ($request->order_webhook_url != null ) ? saveSetting('order_webhook_url', $request->order_webhook_url, $user->id) : saveSetting('order_webhook_url', 0, $user->id);
                ($request->order_webhook_url_method != null ) ? saveSetting('order_webhook_url_method', $request->order_webhook_url_method, $user->id) : saveSetting('order_webhook_url_method', "POST", $user->id);
            $user->update([
                'name' => $request->name,
                'last_name' => $request->last_name,
                'phone' => $request->phone,
                'email' => $request->email,
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

            if ( $request->hasFile('image') ){
                $file = Document::saveDocument($request->file('image'));
                Auth::user()->image()->delete();
                $image = Document::create([
                    'name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'type' => $file->getMimeType(),
                    'path' => $file->filename
                ]);
                Auth::user()->image()->associate($image)->save();

                // dd($image);
            }
            $request->has('auto_charge') ? saveSetting('auto_charge', true, $user->id) : saveSetting('auto_charge', false, $user->id);
            $request->has('return_origin') ? saveSetting('return_origin', true, $user->id) : saveSetting('return_origin', false, $user->id);
            $request->has('dispose_all') ? saveSetting('dispose_all', true, $user->id) : saveSetting('dispose_all', false, $user->id);
            $request->has('individual_parcel') ? saveSetting('individual_parcel', true, $user->id) : saveSetting('individual_parcel', false, $user->id);
            return true;

        }catch(Exception $exception){
            session()->flash('alert-danger','Error while Saving Profie');
            return null;
        }
    }

}