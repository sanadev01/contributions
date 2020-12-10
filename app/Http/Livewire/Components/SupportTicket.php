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
        
        if (Auth::user()->isUser()) {
            $supportTickets = Ticket::query()
                ->has('user')
                ->where('user_id', auth()->id())
                ->whereHas('comments', function($q){
                    $q->where('read', false)
                    ->where('user_id', '!=', auth()->id());
                })->count();
        }else{
            $supportTickets = Ticket::query()
                ->has('user')
                ->whereHas('comments', function($q){
                    $q->where('read', false)
                    ->where('user_id', '!=', auth()->id());
                })->count();
        }
        
        return $supportTickets;

    }
}
