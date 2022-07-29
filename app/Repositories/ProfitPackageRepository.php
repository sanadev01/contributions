<?php

namespace App\Repositories;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\ProfitPackage;
use App\Models\ProfitSetting;
use Illuminate\Support\Facades\Auth;

class ProfitPackageRepository
{
    public function get()
    {   
        $packages = ProfitPackage::query()->with('shippingService')->orderBy('name','ASC')->get();
        return $packages;

    }

    public function store(Request $request)
    {   
        try{
            
            $data = $request->slab;
            $arrayCounter = 0;

            foreach( $request->slab as $slab ){
            
                if($arrayCounter == 0){
                    $slab['min_weight'] = 0;
                } else {
                    $minWeight = $arrayCounter - 1;
                    $prev_maxWeight = $data[ $minWeight ]['max_weight'];
                    $slab['min_weight'] = $prev_maxWeight + 1;
                }

                $arrayCounter ++;
                $profitPackageslab[] = $slab ;
            }
            
            $profitPackage = ProfitPackage::create([
                'name' => $request->package_name,
                'shipping_service_id' => $request->shipping_service_id,
                'type' => $request->type,
                'data' => $profitPackageslab
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
            
            $data = $request->slab;
            $arrayCounter = 0;

            foreach( $request->slab as $slab ){
            
                if($arrayCounter == 0){
                    $slab['min_weight'] = 0;
                } else {
                    $minWeight = $arrayCounter - 1;
                    $prev_maxWeight = $data[ $minWeight ]['max_weight'];
                    $slab['min_weight'] = $prev_maxWeight + 1;
                }

                $arrayCounter ++;
                $profitPackageslab[] = $slab ;
            }
    
            $profitPackage->update([
                'name' => $request->package_name,
                'shipping_service_id' => $request->shipping_service_id,
                'type' => $request->type,
                'data' => $profitPackageslab
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

    public function getPackageUsers($package)
    {
        $settings = ProfitSetting::where('package_id', $package->id)->get();
        
        if($settings->isNotEmpty())
        {
            foreach ($settings as $setting) 
            {
                $settingIds[] = $setting->user_id;
            }

            return User::findMany($settingIds);
        }
        
        return null;
    }

}