<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Shared\Profile\Create;
use App\Models\User;
use App\Models\Country;
use App\Models\State;
use App\Models\ProfitPackage;


class ProfileController extends Controller
{   
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   
        $countries = Country::all();
        $states = State::all();
        $packages = ProfitPackage::all();

        $data = [
            'countries' => $countries, 
            'states' => $states, 
            'packages' => $packages, 
        ];

        return view('admin.profile.index')->with($data);
    }

        /**
     * Store a newly created resource in storage.
     *
     * @param Create $request
     * @return void
     */
    public function store(Create $request)
    {
        $user = User::find(Auth::id());

        $user->update([
            'name' => $request->name,
            'last_name' => $request->last_name,
            'phone' => $request->phone,
            'package_id' => $request->package_id,
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

        session()->flash('alert-success', 'Profile Updated Successfully');
        return back();
    }
}
