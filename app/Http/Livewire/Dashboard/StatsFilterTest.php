<?php

namespace App\Http\Livewire\Dashboard;

use Livewire\Component;
use Illuminate\Http\Request;
use App\Repositories\DashboardRepositoryTest;

class StatsFilterTest extends Component
{
    public $startDate;
    public $endDate;
    public $orders;
    public function render(DashboardRepositoryTest $dashboard)
    {
        $this->orders = $this->getOrders();
        return view('livewire.dashboard.stats-filter-test');
    }

    public function getorders(){

        return (new DashboardRepositoryTest)->getDashboardStats($this->startDate,$this->endDate);

    }

   
}
