<?php

namespace App\Http\Livewire\ChileContainer;

use Livewire\Component;
use Illuminate\Support\Facades\Http;

class SearchAirport extends Component
{
    public $search;
    public $airport = [];
    public $message ;
    public $textClass;
    
    public function mount($origin_operator_name = null)
    {
        $this->search = $origin_operator_name;
    }

    public function render()
    {
        return view('livewire.chile-container.search-airport');
    }

    public function updatedsearch($search)
    {
        
        if ( $search != null && $search != '' &&  strlen($search) > 2 )
        {
            $response = Http::get("https://www.airport-data.com/api/ap_info.json?iata=$search");

            $response = $response->json();

            if($response['status'] == '200' && ($response['country_code'] != null || $response['country'] != null))
            {
                $this->airport = $response;
            }else {
                $this->message = 'Airport not found';
                $this->textClass = 'text-danger';
            }

            if($response['status'] != '200')
            {
                $this->message = 'Airport not found.';
                $this->textClass = 'text-danger';
            }
        }

        return ;
    }

    public function selectAirport($iata)
    {
        $this->search = $iata;
        $this->airport = [];
        $this->message = 'Airport selected.';
        $this->textClass = 'text-success';
        return ;
    }
}
