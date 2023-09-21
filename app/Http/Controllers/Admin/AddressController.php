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
use App\Repositories\AddressRepository;
use App\Models\Address;
use App\Models\Country;
use App\Models\State;
use App\Services\Excel\Export\ExportAddresses;


class AddressController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Address::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.addresses.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.addresses.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Create $request, AddressRepository $repository)
    {
        if ( $repository->store($request) ){
            session()->flash('alert-success', 'address.Created');
            return redirect()->route('admin.addresses.index');
        }

        return back()->withInput();

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

        return view('admin.addresses.edit', compact('address'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Update $request, Address $address, AddressRepository $repository)
    {
        if ($repository->update($request,$address) ){

            session()->flash('alert-success', 'address.Updated');
            return redirect()->route('admin.addresses.index');

        }
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

        session()->flash('alert-success', 'address.Deleted');
        return back();
    }

    public function exportAddresses(Request $request, AddressRepository $addressRepository)
    {
        $addresses = $addressRepository->getAddresses($request);

        $exportService = new ExportAddresses($addresses);

        return $exportService->handle();
    }
}
