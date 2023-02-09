<?php

namespace App\Http\Livewire\Deposit;

use Livewire\Component;
use App\Models\BillingInformation;

class AutoCharge extends Component
{
    public $charge_amount;
    public $charge_limit;
    public $charge_biling_information;
    public $charge;

    public function mount()
    {
        $this->charge_amount = old('charge_amount') ?? setting('charge_amount', null, auth()->id());
        $this->charge_limit = old('charge_limit') ?? setting('charge_limit', null, auth()->id());
        $this->charge_biling_information = old('charge_biling_information') ?? setting('charge_biling_information', null, auth()->id());
        $this->charge = old('charge') ?? setting('charge', null, auth()->id());
    }

    public function render()
    {
        return view('livewire.deposit.auto-charge');
    }

    public function save()
    {
        $data = $this->validate([
            'charge_amount' => 'required',
            'charge_limit'  => 'required',
            'charge_biling_information' => 'required',
            'charge'    => 'nullable',
        ]);
        $authId = Auth()->id();
        $isCharge = setting('charge', null, auth()->id()) ? false : true;
        if (BillingInformation::where('user_id', $authId)->where('id', $data['charge_biling_information'])->exists()) {
            saveSetting('charge_amount', $data['charge_amount'],  $authId);
            saveSetting('charge_limit', $data['charge_limit'], $authId);
            saveSetting('charge_biling_information', $data['charge_biling_information'], $authId);
            saveSetting('charge', $isCharge, $authId);
            $this->charge = setting('charge', null, auth()->id());
        }
    }

    public function updating()
    {
        $this->resetPage();
    }
}
