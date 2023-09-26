<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shared\Ticket\Create;
use App\Http\Requests\Shared\Ticket\Update;
use App\Models\Ticket;
use App\Repositories\TicketRepository;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   
        $this->authorize('viewAny',Ticket::class);

        return view('admin.tickets.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create',Ticket::class);
        return view('admin.tickets.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Create $request, TicketRepository $repository)
    {   
        $this->authorize('create',Ticket::class);

        if($repository->store($request)){
            session()->flash('alert-success', 'tickets.Generated');
        }

        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Ticket $ticket, TicketRepository $repository)
    {
        $this->authorize('view',$ticket);

        if($repository->show($ticket)){
            return view('admin.tickets.show', compact('ticket'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Update $request, Ticket $ticket, TicketRepository $repository)
    {
        $this->authorize('update',$ticket);

        if($repository->update($request, $ticket)){
            session()->flash('alert-success', 'tickets.Comment');
        }

        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function markClose(Ticket $ticket, TicketRepository $repository)
    {
        $this->authorize('close',$ticket);
        if($repository->markcLose($ticket)){
            session()->flash('alert-success', 'tickets.Closed');
        }

        return back();
    }
}
