<?php

namespace App\Repositories;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ProfitPackage;
use App\Models\User;
use Exception;

class ProfitPackageRepository
{
    public function get()
    {   
        $packages = ProfitPackage::get();
        return $packages;

    }

    public function store(Request $request)
    {   
        try{

            foreach( $request->slab as $slab ){
                $profitPackageslab[] = $slab ;
            }
    
            $profitPackage = ProfitPackage::create([
                'name' => $request->package_name,
                'data' => json_encode($profitPackageslab)
            ]);

            return true;

        }catch(Exception $exception){
            session()->flash('alert-danger','Error while Saving ProfitPackage');
            return null;
        }
    }

    public function update(Request $request,ProfitPackage $profitPackage)
    {   
        
        try{

            foreach( $request->slab as $slab ){
                $profitPackageslab[] = $slab;
            }
    
            $profitPackage->update([
                'name' => $request->package_name,
                'data' => json_encode($profitPackageslab)
            ]);

            return true;

        }catch(Exception $exception){
            session()->flash('alert-danger','Error while ProfitPackage');
            return null;
        }
    }

    public function delete(ProfitPackage $profitPackage){

        $profitPackage->delete();
        return true;

    }

}