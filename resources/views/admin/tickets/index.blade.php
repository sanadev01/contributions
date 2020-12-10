@extends('layouts.master')

@section('page')
    <section id="prealerts">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">
                            @if(auth()->user()->isAdmin())
                                @lang('tickets.Support Tickets')
                            @else
                                @lang('tickets.My Tickets')
                            @endif
                        </h4>
                        @user
                            <a href="{{ route('admin.tickets.create') }}" class="pull-right btn btn-primary"> @lang('tickets.Create New Ticket') </a>
                        @enduser
                    </div>
                    <div class="card-content">
                        <div class="table-responsive-md mt-1">
                            <table class="table table-hover-animation mb-0">
                                <thead>
                                <tr>
                                    <th>@lang('tickets.TicketID')</th>
                                    {{-- @admin --}}
                                    <th>
                                        
                                    </th>
                                    <th>
                                        @lang('tickets.User')
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
                                            
                                            {{-- @if($ticket->comments_count > 0) --}}
                                                <span class="bg-danger border border-danger rounded-circle text-light m-2 p-2 justify-content-center align-items-center {{ $ticket->comments_count > 0 ? 'd-inline-flex' : 'd-none' }}" style="height: 25px; width:25px; top:0; right:0">
                                                    {{ $ticket->comments_count }}
                                                </span>
                                            {{-- @endif --}}
                                        </td>
                                        <td>
                                            {{ $ticket->user->name }}
                                        </td>
                                        {{-- @endadmin --}}
                                        <td>
                                            {{ $ticket->subject }}
                                            
                                        </td>
                                        <td>
                                             @if($ticket->open == 1) <span class="badge badge-success">@lang('tickets.open')</span> @else <span class="badge badge-danger">@lang('tickets.close')</span> @endif 
                                        </td>
                                        <td class="d-flex">
                                            <a href="{{ route('admin.tickets.show',$ticket->id) }}" class="btn btn-primary mr-2" title="@lang('tickets.Detail')">
                                                <i class="feather icon-eye"></i>
                                            </a>

                                                @if( auth()->user()->isAdmin() && $ticket->isOpen() )
                                                    <form action="{{ route('admin.ticket.mark-closed',$ticket) }}" method="post">
                                                        @csrf
                                                        <button class="btn btn-danger" title="@lang('tickets.Close Ticket')">
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
