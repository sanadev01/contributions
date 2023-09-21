<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Repositories\TicketRepository;
use GuzzleHttp\Psr7\Request;

class Tickets extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $searchTerm;
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

        $this->searchTerm = request('searchTerm');
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
            'searchTerm' => $this->searchTerm ? $this->searchTerm : null,
        ]));
    }
}
