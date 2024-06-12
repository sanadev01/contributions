<?php

namespace App\Http\Livewire\SupportTicket;

use Livewire\Component;

class Note extends Component
{
    public $ticket;
    public $note;
    public function mount($ticket)
    {
        $this->ticket = $ticket;
        $this->note = $ticket->note;
    }

    public function render()
    {
        return view('livewire.support-ticket.note');
    }

    public function save()
    {
        $data = $this->validate([
            'note' => 'required',
        ]);

        return $this->ticket->update([
            'note' => $data['note']
        ]);
    }
}
