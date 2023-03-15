<?php

namespace App\Http\Livewire\Deposit;

use App\Mail\Admin\AutoChargeChanged;
use Livewire\Component;
use App\Models\BillingInformation;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AutoCharge extends Component
{
    public $charge_amount;
    public $charge_limit;
    public $charge_biling_information;
    public $selected_card;
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
        $this->selected_card = optional(auth()->user()->billingInformations->where('id', $this->charge_biling_information))->first();
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

        $user = Auth::user();
        $oldData = getAutoChargeData($user);
        $authId = Auth()->id();
        $isCharge = setting('charge', null, auth()->id()) ? false : true;
        if (BillingInformation::where('user_id', $authId)->where('id', $data['charge_biling_information'])->exists()) {
            saveSetting('charge_amount', $data['charge_amount'],  $authId);
            saveSetting('charge_limit', $data['charge_limit'], $authId);
            saveSetting('charge_biling_information', $data['charge_biling_information'], $authId);
            saveSetting('charge', $isCharge, $authId);
            $this->charge = setting('charge', null, $authId);
            $message = 'Auto Charge deActivate Successfully';
            $type = 'info';
            if ($this->charge) {
                $message = 'Auto Charge activated Successfully';
                $type = 'success';

            }
            try {
                 Mail::send(new AutoChargeChanged($oldData,getAutoChargeData($user)));
            } catch (Exception $ex) {
                
            $this->dispatchBrowserEvent('alert', ['type' =>  $type,  'message' => $ex->getMessage()]);
            return;
                // Log::info('Autocharge change setting email send error: '.$ex->getMessage());
            }

            $this->dispatchBrowserEvent('alert', ['type' => $type,  'message' => $message]);
            return;
        }
        $this->dispatchBrowserEvent('alert', ['type' => 'error',  'message' => 'Auto Charge Something Went Wrong']); 
    }
}
