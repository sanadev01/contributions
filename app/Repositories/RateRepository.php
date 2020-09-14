<?php

namespace App\Repositories;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Rate;
use App\Models\User;
use Exception;
use App\Services\Excel\ImportCharges\ImportBPSCharges;

class RateRepository
{
    public function get()
    {   
        $rates = Rate::first() ?? new Rate;
        return $rates;
    }

    public function store(Request $request)
    {   
        try{

            $file = $request->file('csv_file');

            try {
                $importService = new ImportBPSCharges($file);
                $importService->handle();
                session()->flash('alert-success', 'shipping-rates.Rates Updated Successfully');

                return true;

            } catch (\Exception $exception) {
                throw $exception;
                session()->flash('alert-danger', 'shipping-rates.Error While Updating Rates');
                return back();
            }

        }catch(Exception $exception){
            session()->flash('alert-danger','Error while Saving Rate');
            return null;
        }
    }


}