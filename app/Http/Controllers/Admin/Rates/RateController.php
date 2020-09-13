<?php

namespace App\Http\Controllers\Admin\Rates;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\Rate\CreateRequest;
use App\Models\ShippingService;
use App\Repositories\RateRepository;

class RateController extends Controller
{
    public function index(RateRepository $repository)
    {
        $rates = $repository->get();
        return view('admin.rates.bps-leve.index', compact('rates'));
    }

    public function create()
    {   
        $shipping_services = ShippingService::all();
        return view('admin.rates.bps-leve.create', compact('shipping_services'));
    }

    public function store(CreateRequest $request, RateRepository $repository)
    {   
        if ( $repository->store($request) ){
            return  redirect()->route('admin.rates.bps-leve.index');
        }
    }

}
