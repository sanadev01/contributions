<?php

namespace App\Http\Livewire\SupportTicket;

use App\Models\TicketComment;
use Livewire\Component;
use App\Repositories\TicketRepository;

class ShowTicket extends Component
{
    public $ticket;
    public $extensions;

    public function mount($ticket){

        $this->ticket = $ticket;
        $this->getTicketComments();
        $extensions = array('image/jpg','image/jpe','image/jpeg','image/jfif','image/png','image/bmp','image/dib','image/gif');
        $this->extensions = $extensions;
    }
    public function render()
    {
        return view('livewire.support-ticket.show-ticket');
    }

    public function getTicketComments(){
        return (new TicketRepository)->show($this->ticket);
        
    }
}
