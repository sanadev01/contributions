<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests\User\Address\Create;
use App\Http\Requests\User\Address\Update;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use App\Models\Address;
use App\Models\Country;
use App\Models\State;

class AddressController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $query = Address::query()->has('user');

        if ( Auth::user()->isUser() ){
            $query->where('user_id',Auth::id());
        }

        $addresses = $query
            ->latest()
            ->get();
        return view('admin.addresses.index', compact('addresses'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {   
        $countries = Country::all();
        $states = State::all();

        $data = array(
            'countries' => $countries, 
            'states' => $states, 
        );

        return view('admin.addresses.create')->with($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Create $request)
    {

        $address = Auth::user()->addresses()->create(
            $request->only(['first_name','last_name','email', 'phone', 'city', 'street_no','address', 'address2', 'country_id', 'state_id', 'account_type', 'tax_id', 'zipcode'])
        );

        session()->flash('alert-success', 'Address Saved Successfully');

        return redirect()->route('admin.addresses.index');
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Address $address)
    {
        if (! $address) {
            throw new NotFoundHttpException('Resource Not Found');
        }

        if (!Auth::user()->isAdmin() && $address->user_id != Auth::id()) {
            throw new UnauthorizedHttpException('Not Authorized');
        }

        $countries = Country::all();
        $states = State::all();

        return view('admin.addresses.edit', compact('address','countries', 'states'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Update $request, Address $address)
    {
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

        $address->refresh();

        $address->update(
            $request->only(['first_name','last_name','email', 'phone', 'city', 'street_no','address', 'address2', 'country_id', 'state_id', 'account_type', 'tax_id', 'zipcode'])
        );


        session()->flash('alert-success', 'Address Updated');

        return redirect()->route('admin.addresses.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Address $address)
    {
        // if ( $address->orders->count() ){
        //     session()->flash('alert-warning', 'Address has Orders Cannot delete');
        //     return back();
        // }
        
        if ($address) {
            $address->delete();
        }

        session()->flash('alert-success', 'Address Deleted Successfully');
        return back();
    }
}
