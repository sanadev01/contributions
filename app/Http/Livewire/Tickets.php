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
    public $open;
    public $pobox;
    
    public function mount()
    {
        $this->status = 'all';
    }

    public function render()
    {
        return view('livewire.tickets', [
            'tickets' => $this->getTickets(),
        ]);
    }

    public function updatedStatus($value)
    {
        if ($value == 'open') {
            $this->open = true;
        }

        if ($value == 'close') {
            $this->open = false;
        }
    }

    private function getTickets()
    {
        return (new TicketRepository)->get(request()->merge([
            'date' => $this->date ? $this->date : null,
            'pobox' => $this->pobox,
            'user' => $this->user,
            'status' => ($this->status != 'all') ? $this->open : null,
        ]));
    }
}
