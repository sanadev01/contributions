<div>
    <div class="table-responsive-md mt-1">
        <table class="table table-hover-animation mb-0">
            <thead>
                <tr>
                    <th>@lang('tickets.TicketID')</th>
                    <th></th>
                    <th>@lang('tickets.Date')</th>
                    <th>@lang('tickets.pobox')</th>
                    <th>@lang('tickets.User')</th>
                    <th>@lang('tickets.Issue')</th>
                    <th>@lang('tickets.Status')</th>
                    <th>@lang('tickets.Open Days')</th>
                    <th>@lang('tickets.Detail')</th>
                </tr>
                <tr>
                    <th></th>
                    <th></th>
                    <th>
                        <input type="search" class="form-control" wire:model.debounce.1000ms="date">
                    </th>
                    <th>
                        <input type="search" class="form-control" wire:model.debounce.1000ms="pobox">
                    </th>
                    <th>
                        <input type="search" class="form-control" wire:model.debounce.1000ms="user">
                    </th>
                    <th></th>
                    {{-- @can('open_and_close',App\Models\Ticket::class) --}}
                        
                    <th style="min-width: 100px;">
                        
                        <select name="status" class="form-control" wire:model.debounce.1000ms="status">
                            <option value="all">All</option>
                            <option value="open">Open</option>
                            <option value="close">Close</option>
                        </select>
                    </th>
                        {{-- @endcan --}}
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($tickets as $ticket)
                    <tr>
                        <td>{{ $ticket->getHumanID() }}</td>
                        <td>
                            <span class="bg-danger border border-danger rounded-circle text-light m-2 p-2 justify-content-center align-items-center {{ $ticket->comments_count > 0 ? 'd-inline-flex' : 'd-none' }}" style="height: 25px; width:25px; top:0; right:0">
                                {{ $ticket->comments_count }}
                            </span>
                        </td>
                        <td>
                            {{ $ticket->created_at->format('Y-m-d') }}
                        </td>
                        <td>
                            {{ $ticket->user->pobox_number }}
                        </td>
                        <td>
                            {{ $ticket->user->name }}
                        </td>
                        <td>
                            {{ $ticket->subject }}
                            
                        </td>
                        <td>
                            @if($ticket->open == 1) <span class="badge badge-success">@lang('tickets.open')</span> @else <span class="badge badge-danger">@lang('tickets.close')</span> @endif 
                        </td>
                        <td>
                            {{ $ticket->getOpenDays() }}
                        </td>
                        <td class="d-flex">
                            <a href="{{ route('admin.tickets.show',$ticket->id) }}" class="btn btn-primary mr-2" title="@lang('tickets.Detail')">
                                <i class="feather icon-eye"></i>
                            </a>
                            @if($ticket->isOpen())
                                @can('close',App\Models\Ticket::class)
                                    <form action="{{ route('admin.ticket.mark-closed',$ticket) }}" method="post">
                                        @csrf
                                        <button class="btn btn-danger" title="@lang('tickets.Close Ticket')">
                                            <i class="feather icon-check"></i>
                                        </button>
                                    </form>
                                @endcan
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-end my-2 pb-4 mx-2">
        {{ $tickets->links() }}
    </div>
    @include('layouts.livewire.loading')
</div>
