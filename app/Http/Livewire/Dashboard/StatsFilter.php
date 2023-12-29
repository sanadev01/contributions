<?php

namespace App\Http\Livewire\Dashboard;

use Livewire\Component;
use Illuminate\Http\Request;
use App\Repositories\DashboardRepository;

class StatsFilter extends Component
{
    public $startDate;
    public $endDate;
    public $orders;
    public function render(DashboardRepository $dashboard)
    {
        $this->orders = [];//$this->getOrders();
        return view('livewire.dashboard.stats-filter');
    }

    public function getorders(){

        // return (new DashboardRepository)->getDashboardStats($this->startDate,$this->endDate);

    }

   
}
