@extends('layouts.master')

@section('page')
    <section id="prealerts">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">
                            @if(auth()->user()->isAdmin())
                                Support Tickets
                            @else
                                @lang('tickets.My Tickets')
                            @endif
                        </h4>
                        @admin
                        <a href="{{ route('admin.tickets.create') }}" class="pull-right btn btn-primary"> @lang('tickets.Create New Ticket') </a>
                        @endadmin
                    </div>
                    <div class="card-content">
                        <div class="table-responsive-md mt-1">
                            <table class="table table-hover-animation mb-0">
                                <thead>
                                <tr>
                                    <th>@lang('tickets.TicketID')</th>
                                    {{-- @admin --}}
                                    <th>
                                        User
                                    </th>
                                    {{-- @endadmin --}}
                                    <th>@lang('tickets.Issue')</th>
                                    <th>@lang('tickets.Status')</th>
                                    <th>@lang('tickets.Detail')</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($supportTickets as $ticket)
                                    <tr>
                                        <td>{{ $ticket->getHumanID() }}</td>
                                        {{-- @admin --}}
                                        <td>
                                            {{ $ticket->user->name }}
                                        </td>
                                        {{-- @endadmin --}}
                                        <td>
                                            {{ $ticket->subject }}
                                        </td>
                                        <td>
                                             @if($ticket->open == 1) <span class="badge badge-success">open</span> @else <span class="badge badge-danger">close</span> @endif 
                                        </td>
                                        <td class="d-flex">
                                            <a href="{{ route('admin.tickets.show',$ticket->id) }}" class="btn btn-primary mr-2" title="@lang('tickets.Detail')">
                                                <i class="feather icon-eye"></i>
                                            </a>

                                                @if( auth()->user()->isAdmin() && $ticket->isOpen() )
                                                    <form action="{{ route('admin.ticket.mark-closed',$ticket) }}" method="post">
                                                        @csrf
                                                        <button class="btn btn-danger" title="Close Ticket">
                                                            <i class="feather icon-check"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
