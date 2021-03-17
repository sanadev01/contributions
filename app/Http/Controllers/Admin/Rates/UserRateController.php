<?php

namespace App\Http\Controllers\Admin\Rates;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ProfitPackage;
use App\Repositories\Reports\RateReportsRepository;

class UserRateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(RateReportsRepository $rateReportsRepository)
    {
        $this->authorize('userSellingRates',ProfitPackage::class);
        if(auth()->user()->package_id){
            $packageId = auth()->user()->package_id;
        }else{
            $packageId = ProfitPackage::where('type', 'default')->first()->id; 
        }

        $rates = $rateReportsRepository->getRateReport($packageId);
        return view('admin.rates.profit-packages.user-profit-package.index', compact('rates','packageId'));
    }
    
}
