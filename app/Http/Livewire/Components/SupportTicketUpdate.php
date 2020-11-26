<?php

namespace App\Http\Livewire\Components;

use App\Models\TicketComment;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class SupportTicketUpdate extends Component
{
    public $ticketId;
    public $userId;
    public function mount($ticket)
    {
        $this->ticketId = $ticket->id;
        $this->userId = $ticket->user_id;
    }
    public function render()
    {
        $supportTicketsComments = TicketComment::where('user_id', '!=', Auth::id())
            ->where('ticket_id', $this->ticketId )
            ->where('read', false)
            ->get();
        foreach($supportTicketsComments as $comments){
            $comments->read = 1;
            $comments->save();
        }
        return view('livewire.components.support-ticket-update');
    }
}
