<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Repositories\TicketRepository;

class Tickets extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $date;
    public $user;
    public $status;
    
    public function render()
    {
        return view('livewire.tickets', [
            'tickets' => $this->getTickets(),
        ]);
    }

    public function updatedStatus($value)
    {
        if ($value == 'open') {
            $this->status = true;
        }

        if ($value == 'close') {
            $this->status = false;
        }
    }

    private function getTickets()
    {
        return (new TicketRepository)->get(request()->merge([
            'date' => $this->date ? $this->date.' 00:00:00' : null,
            'user' => $this->user,
            'status' => ($this->status != 'all') ? $this->status : null,
        ]));
    }
}
