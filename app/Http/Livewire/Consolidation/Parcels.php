<?php

namespace App\Http\Livewire\Consolidation;

use App\Repositories\PreAlertRepository;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Parcels extends Component
{
    public $userId;

    public $selected = [];

    protected $listeners = [
        'user:updated' => 'onUserUpdated'
    ];

    public function mount($userId=null, $selected = [])
    {
        $this->userId = Auth::user()->isAdmin() ? $userId : Auth::id();
        $this->selected = $selected;
    }

    public function onUserUpdated($userId)
    {
        $this->userId = $userId;
    }

    public function render()
    {
        return view('livewire.consolidation.parcels',[
            'parcels' => $this->getParcels()
        ]);
    }

    public function getParcels()
    {
        return (new PreAlertRepository())->getReadyParcels(
            request()->merge([
                'user_id' => $this->userId
            ]),
            count($this->selected) > 0 ? true :false
        );
    }
}
