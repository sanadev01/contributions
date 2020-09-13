<?php

namespace App\Repositories;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Ticket;
use Exception;

class TicketRepository
{
    public function get()
    {   
        $supportTickets = \auth()->user()->isAdmin() ? Ticket::has('user')->get() : Auth::user()->tickets;
        return $supportTickets;

    }

    public function store(Request $request)
    {   
        try{

            $ticket = Auth::user()->tickets()->create([
                'subject' => $request->subject
            ]);
    
            $ticket->comments()->create([
                'user_id' => Auth::id(),
                'text' => $request->text
            ]);

            return true;

        }catch(Exception $exception){
            session()->flash('alert-danger','Error while Saving Ticket');
            return null;
        }
    }

    public function update(Request $request, Ticket $ticket)
    {   
        
        try{

            $ticket->addComment($request);
            return true;

        }catch(Exception $exception){
            session()->flash('alert-danger','Error while Ticket');
            return null;
        }
    }

    public function show(Ticket $ticket){

        if (! $ticket || (\auth()->user()->isUser() && $ticket->user_id != Auth::id())) {
            return new NotFoundHttpException('Not found');
        }
        return true;

    }

    public function markcLose(Ticket $ticket){

        if (! Auth::user()->isAdmin()) {
            throw new UnauthorizedHttpException('You are not authorized to perform this action');
        }

        $ticket->markClosed();
        return true;

    }


}