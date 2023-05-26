<?php

namespace App\Repositories;

use Exception;
use App\Models\User;
use App\Models\Ticket;
use App\Models\Document;
use Illuminate\Http\Request;
use App\Models\TicketComment;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\User\NewTicketCommentAdded;

class TicketRepository
{
    public function get(Request $request)
    {   

        $tickets = Ticket::query();

        $tickets->has('user')->withCount(['comments' => function($q){
            $q->where('read', '0')->where('user_id', '!=', auth()->id() ); 
        }]);
  
         if(Auth::user()->cannot('reply', Ticket::class)){
             $tickets->where('user_id', auth()->id());
         }
 

        $tickets->when($request->filled('date'), function ($query) use ($request) {
            return $query->where('created_at', 'LIKE', "%{$request->date}%");
        });

        $tickets->when($request->filled('pobox'), function ($query) use ($request) {
            return $query->whereHas('user', function ($query) use ($request) {
                return $query->where('pobox_number', 'like', '%'.$request->pobox.'%');
            });
        });

        $tickets->when($request->filled('user'), function ($query) use ($request) {
            return $query->whereHas('user', function ($query) use ($request) {
                return $query->where('name', 'like', '%'.$request->user.'%');
            });
        });

        $tickets->when($request->filled('status'), function ($query) use ($request) {
            return $query->where('open', $request->status);
        });

        return $tickets->orderBy('id','DESC')->paginate(25);
    }

    public function store(Request $request)
    {
        
        try{

            $ticket = Auth::user()->tickets()->create([
                'subject' => $request->subject
            ]);
    
            $comment = $ticket->comments()->create([
                'user_id' => Auth::id(),
                'text' => $request->text
            ]);
            
            $user = User::whereHas('role', function($q){$q->where('id',1)->orWhere('name', 'admin');})->first();
            $comment = $ticket->comments()->create([
                'user_id' => $user ? $user->id: 1,
                'text' => 'your ticket will be responded maximum 24 hours during business hours'
            ]);
            
            try{

                \Mail::send(new NewTicketCommentAdded($comment));
            
            }catch (\Exception $ex) {
                \Log::info('Add Comment email send error: '.$ex->getMessage());
            }

            return true;

        }catch(Exception $exception){
            session()->flash('alert-danger','Error while Saving Ticket');
            return null;
        }
    }

    public function update(Request $request, Ticket $ticket)
    {
        try{
            
            $comment = $ticket->addComment($request);
            
            if($request->file('file')){
                $document = Document::saveDocument($request->file('file'));
                $comment->images()->create([
                    'name' => $document->getClientOriginalName(),
                    'size' => $document->getSize(),
                    'type' => $document->getMimeType(),
                    'path' => $document->filename
                ]);
            }


            try {

                \Mail::send(new NewTicketCommentAdded($comment));
            
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
            
        $ticket->comments()->where('read', false)->where('user_id', '!=', Auth::id())->update([
            'read' => true
        ]);

        return true;
    }

    public function markcLose(Ticket $ticket){

        $ticket->markClosed();
        return true;

    }

    // public function importFile(UploadedFile $file)
    // {
    //     $fiename = md5(microtime()).'.'.$file->getClientOriginalExtension();
    //     $file->storeAs("comments/", $fiename);
    //     return $fiename;
    // }

    // public function getStoragePath($filename)
    // {
    //     return storage_path("app/comments/{$filename}");
    // }


}