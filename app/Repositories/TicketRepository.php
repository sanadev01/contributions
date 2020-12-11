<?php

namespace App\Repositories;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Ticket;
use App\Models\TicketComment;
use App\Mail\User\NewTicketCommentAdded;
use Illuminate\Support\Facades\Mail;
use Exception;

class TicketRepository
{
    public function get()
    {   
        $supportTickets = \auth()->user()->isAdmin() ? Ticket::has('user')->withCount(['comments' => function($q){
                $q->where('read', '0')->where('user_id', '!=', auth()->id() ); 
            }])->get() : Ticket::has('user')->where('user_id', auth()->id())->withCount(['comments' => function($q){
                $q->where('read', '0')->where('user_id', '!=', auth()->id() ); 
            }])->get();
        
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
            
            $addComment = $ticket->addComment($request);
            
            try {

                \Mail::send(new NewTicketCommentAdded($addComment));
            
            } catch (\Exception $ex) {
                \Log::info('Add Comment email send error: '.$ex->getMessage());
            }
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
    
        $ticket->comments()->where('read', false)->where('user_id', '!=', Auth::id())->update([
            'read' => true
        ]);

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