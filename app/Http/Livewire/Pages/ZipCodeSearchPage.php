<?php

namespace App\Http\Livewire\Pages;

use App\Models\ZipCode;
use Livewire\Component;
use Livewire\WithPagination;

class ZipCodeSearchPage extends Component
{
    use WithPagination;

    public $zipcode;
    public $city;
    public $address;
    public $state;

    public function render()
    {
        return view('livewire.pages.zip-code-search-page',[
            'zipcodes' => $this->getZipCodes()
        ]);
    }

    public function getZipCodes()
    {
        return (new ZipCode())->query()
            ->where(function($query){
                $query->where('city','LIKE', "%{$this->city}%")
                ->orWhere('address','LIKE', "%{$this->address}%");
            })
            ->when($this->zipcode,function($query){
                $query->where('zipcode',$this->zipcode);
            })
            ->when($this->state,function($query){
                $query->where('state', $this->state);
            })
            ->paginate(50);
    }

    public function updating()
    {
        $this->resetPage();
    }


}
