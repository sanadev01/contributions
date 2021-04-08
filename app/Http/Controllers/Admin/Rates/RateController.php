<?php

namespace App\Http\Controllers\Admin\Rates;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\Rate\CreateRequest;
use App\Models\ShippingService;
use App\Repositories\RateRepository;
use App\Models\Rate;

class RateController extends Controller
{   
    public function __construct()
    {
        $this->authorizeResource(Rate::class);
    } 

    public function index(RateRepository $repository)
    {
        $shippingRates = $repository->get();
        return view('admin.rates.shipping-rates.index', compact('shippingRates'));
    }

    public function create()
    {   
        $shipping_services = ShippingService::all();
        return view('admin.rates.shipping-rates.create', compact('shipping_services'));
    }

    public function store(CreateRequest $request, RateRepository $repository)
    {   
        if ( $repository->store($request) ){
            return  redirect()->route('admin.rates.shipping-rates.index');
        }

        
        // session()->flash('alert-dange','Error while importing rates');
        return back()->withInput();
    }
    
    public function show($id, RateRepository $repository)
    {   
        return $id;
    }

}
