<?php

namespace App\Http\Livewire\Affiliate;


use App\Models\CommissionSetting;
use App\Models\User;
use Livewire\Component;

class CommisionSetting extends Component
{
    
    public $type;
    public $value;
    public $user_id;
    private $commissionSetting;

    public function mount($userId)
    {
        $this->user_id = $userId;
        $this->commissionSetting = $this->getQuery();
        $this->type     = optional($this->commissionSetting)->type;
        $this->value    = optional($this->commissionSetting)->value;

        if(!$this->type){
            $this->type = 'flat';
        }
    }

    public function render()
    {
        return view('livewire.affiliate.commision-setting');
    }

    public function save()
    {
        $data = $this->validate([
            'user_id' => 'required',
            'type'  => 'required',
            'value' => 'required',
        ]);
        
        $commissionSetting = $this->getQuery();
       
        if(!$commissionSetting){
           return CommissionSetting::create($data);
        }

        return $commissionSetting->update($data);
    }

    public function getQuery()
    {
        return CommissionSetting::where('user_id', $this->user_id)->first();
    }
}
