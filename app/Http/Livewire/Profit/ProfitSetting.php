<?php

namespace App\Http\Livewire\Profit;

use App\Models\User;
use Livewire\Component;
use App\Models\ProfitPackage;
use App\Models\ShippingService;
use App\Models\ProfitSetting as PacakgeSetting;

class ProfitSetting extends Component
{
    public $type;
    public $value;
    public $package_id;
    public $service_id;
    public $user_id;

    public function mount($userId)
    {
        $this->user_id = $userId;

    }

    public function render()
    {
        return view('livewire.profit.profit-setting',[
            'packages' =>  ProfitPackage::orderBy('name','ASC')->get(),
            'services' => ShippingService::orderBy('name','ASC')->get(),
            'profitSettings' => PacakgeSetting::where('user_id', $this->user_id)->get()
        ]);
    }

    public function save()
    {
        $data = $this->validate([
            'package_id'    => 'required',
            'service_id'    => 'required',
        ]);
        
        $profitSetting = $this->getQuery();
       
        if(!$profitSetting){
           return PacakgeSetting::create([
               'user_id'    => $this->user_id,
               'package_id' => $this->package_id,
               'service_id' => $this->service_id,
           ]);
        }

        return $profitSetting->update([
            'user_id'    => $this->user_id,
            'package_id' => $this->package_id,
            'service_id' => $this->service_id,
        ]);
    }

    public function getQuery()
    {
        return PacakgeSetting::where('user_id', $this->user_id)->where('service_id', $this->service_id)->first();
    }
    
    public function remove(PacakgeSetting $profitSetting)
    {
        $profitSetting->delete();
    }
}
