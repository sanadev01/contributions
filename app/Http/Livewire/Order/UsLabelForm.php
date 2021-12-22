<?php

namespace App\Http\Livewire\Order;

use Exception;
use Livewire\Component;
use Illuminate\Support\Facades\Http;

class UsLabelForm extends Component
{
    public $order;
    public $states;
    public $usShippingServices;
    public $errors;

    public $firstName;
    public $lastName;
    public $senderState;
    public $senderAddress;
    public $senderCity;
    public $senderZipCode;
    public $service;


    public function mount($order, $states, $usShippingServices, $errors)
    {
        $this->order = $order;
        $this->states = $states;
        $this->usShippingServices = $usShippingServices;
        $this->errors = $errors;
    }

    public function render()
    {
        return view('livewire.order.us-label-form');
    }

    public function updatedsenderState()
    {
        
        $this->validateUSAddress();
    }

    public function updatedsenderAddress()
    {
        $this->validateUSAddress();
    }

    public function updatedsenderCity()
    {
        $this->validateUSAddress();
    }

    private function validateUSAddress()
    {
        $api_url = 'https://api.myibservices.com/v1/address/validate';
        $email = config('usps.email');           
        $password = config('usps.password');

        $data = $this->make_request_attributes();

        try {

            $response = Http::withBasicAuth($email, $password)->post($api_url, $data);
            
            if($response->status() == 200) {
                
                $this->senderZipCode = $response->json()['zip5'];
            }

            if($response->status() != 200) {
                $this->error = $response->json()['message'];
            }
        } catch (Exception $e) {
            
            $this->error = $e->getMessage();
        }
    }

    private function make_request_attributes()
    {
        $data = [
            'company_name' => 'Herco',
            'line1' => $this->senderAddress,
            'state_province' => $this->senderState,
            'city' => $this->senderCity,
            'postal_code' => '',
            'country_code' => 'US'
        ];

        return $data;
    }

    public function getRates()
    {
        return true;
    }
}
