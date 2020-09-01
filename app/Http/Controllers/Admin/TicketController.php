<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shared\Ticket\Create;
use App\Http\Requests\Shared\Ticket\Update;
use Illuminate\Http\Request;
use App\Models\Ticket;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Auth;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $supportTickets  = \auth()->user()->isAdmin() ? Ticket::has('user')->get() : Auth::user()->tickets;
        $supportTickets  = Ticket::has('user')->get();
        return view('admin.tickets.index', compact('supportTickets'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.tickets.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Create $request)
    {
        $ticket = Auth::user()->tickets()->create([
            'subject' => $request->subject
        ]);

        $ticket->comments()->create([
            'user_id' => Auth::id(),
            'text' => $request->detail
        ]);

        session()->flash('alert-success', 'Ticket Generated Successfully');
        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Ticket $ticket)
    {
        if (! $ticket || (\auth()->user()->isUser() && $ticket->user_id != Auth::id())) {
            return new NotFoundHttpException('Not found');
        }

        return  view('admin.tickets.show', compact('ticket'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Update $request, Ticket $ticket)
    {
        $ticket->addComment($request);

        session()->flash('alert-success', 'Comment Added');

        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function markClose(Ticket $ticket)
    {
        // if (! Auth::user()->isAdmin()) {
        //     throw new UnauthorizedHttpException('You are not authorized to perform this action');
        // }

        $ticket->markClosed();

        session()->flash('alert-success', 'Ticket Closed');
        return back();
    }
}
