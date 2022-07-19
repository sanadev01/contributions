<?php

namespace App\Repositories;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Address;
use Exception;

class AddressRepository
{
    public function get(Request $request,$paginate = true,$pageSize=50,$orderBy = 'id',$orderType='asc')
    {
        $query = Address::query()->has('user');

        if ( Auth::user()->isUser() ){
            $query->where('user_id',Auth::id());
        }

        if ( $request->user ){
            $query->whereHas('user',function($query) use($request) {
                return $query->where('pobox_number',"%{$request->user}%")
                            ->orWhere('name','LIKE',"%{$request->user}%")
                            ->orWhere('last_name','LIKE',"%{$request->user}%")
                            ->orWhere('email','LIKE',"%{$request->user}%");
            });
        }

        if ( $request->name ){
            $query->where(function($query) use($request){
                return $query->whereRaw("CONCAT( first_name, ' ', last_name ) LIKE '%{$request->name}%'")
                    ->orWhere('first_name','LIKE',"%{$request->name}%" )
                    ->orWhere('last_name','LIKE',"%{$request->name}%" );
            });
        }

        if ( $request->search ){
            $query->where(function($query) use($request){
                return $query->whereRaw("CONCAT( first_name, ' ', last_name ) LIKE '%{$request->search}%'")
                ->orWhere('first_name','LIKE',"%{$request->search}%" )
                ->orWhere('last_name','LIKE',"%{$request->search}%" );
            })->orWhereHas('user',function($query) use($request) {
                return $query->where('pobox_number',"%{$request->search}%")
                            ->orWhere('name','LIKE',"%{$request->search}%")
                            ->orWhere('last_name','LIKE',"%{$request->search}%")
                            ->orWhere('email','LIKE',"%{$request->search}%");
            })
            ->orWhere('address','LIKE',"%{$request->search}%")
            ->orWhere('address2','LIKE',"%{$request->search}%")
            ->orWhere('city','LIKE',"%{$request->search}%")
            ->orWhere('street_no','LIKE',"%{$request->search}%")
            ->orWhere('phone','LIKE',"%{$request->search}%")
            ->orWhere('state_id',"{$request->search}");
        }

        if ( $request->address ){
            $query->where(function($query) use($request){
                return $query->where('address','LIKE',"%{$request->address}%")
                    ->orWhere('address2','LIKE',"%{$request->address}%");
            });
        }

        if ( $request->street_no ){
            $query->where(function($query) use($request){
                return $query->where('street_no','LIKE',"%{$request->street_no}%");
            });
        }

        if ( $request->phone ){
            $query->where(function($query) use($request){
                return $query->where('phone','LIKE',"%{$request->phone}%");
            });
        }

        if ( $request->city ){
            $query->where(function($query) use($request){
                return $query->where('city','LIKE',"%{$request->city}%");
            });
        }

        if ( $request->state ){
            $query->where(function($query) use($request){
                return $query->where('state_id',"{$request->state}");
            });
        }

        $addresses = $query
            ->orderBy($orderBy,$orderType);

        return $paginate ? $addresses->paginate($pageSize) : $addresses->get();

    }

    public function store(Request $request)
    {   
        try{

            $request->merge([
                'phone' => "+".cleanString($request->phone)
            ]);

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

            $request->merge([
                'phone' => "+".cleanString($request->phone)
            ]);
            
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