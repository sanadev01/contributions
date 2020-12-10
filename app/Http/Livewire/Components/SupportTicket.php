<?php

namespace App\Http\Livewire\Components;
use Illuminate\Support\Facades\Auth;
use App\Models\TicketComment;
use App\Models\Ticket;

use Livewire\Component;

class SupportTicket extends Component
{
    public function render()
    {
        return view('livewire.components.support-ticket', [
            'tickets' => $this->getQuery()
        ]);
    }
   
    public function getQuery()
    {
        $supportTickets = Ticket::query()->has('user');

        if ( Auth::user()->isUser() ){
            $supportTickets->where('user_id',Auth::id());
        }

        $supportTickets->whereHas('comments', function($q){
            $q->where('read', false)
            ->where('user_id', '!=', auth()->id());
        });
        
        return $supportTickets->count();

    }
}
