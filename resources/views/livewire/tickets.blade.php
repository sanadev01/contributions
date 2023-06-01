<div>
    <div class="row col-8 pr-0 pl-0 " @if ($this->searchTerm) style="display: block !important;" @endif
        id="singleSearch">
        <div class="form-group singleSearchStyle col-12">
            <form wire:click="$emitSelf('submit')">
                <div class="form-group mb-2 col-12 row">
                    <input type="text" name="searchTerm" class="form-control col-8 hd-search">
                    <button type="submit" class="btn btn-primary ml-2" onclick="getTickets()">
                        <i class="fa fa-search"></i>
                    </button>
                    <button class="btn btn-primary ml-1 waves-effect waves-light" onclick="window.location.reload();">
                        <i class="fa fa-undo" data-bs-toggle="tooltip" title=""
                            data-bs-original-title="fa fa-undo" aria-label="fa fa-undo" aria-hidden="true"></i></button>
                </div>
            </form>
        </div>
    </div>
    <div class="row col-12 pr-0 m-0 pl-0" id="datefilters">
        <div class=" col-6 text-left mb-2">
            <div class="row col-12 my-3 pl-1" id="dateSearch">

            </div>
        </div>

    </div>
    <div class="table-responsive-md mt-1">
        <table class="table mb-0  table-bordered">
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
            </thead>
            <tbody>
                @foreach ($tickets as $ticket)
                    <tr>
                        <td>{{ $ticket->getHumanID() }}</td>
                        <td>
                            <span
                                class="bg-danger border border-danger rounded-circle text-light m-2 p-2 justify-content-center align-items-center {{ $ticket->comments_count > 0 ? 'd-inline-flex' : 'd-none' }}"
                                style="height: 25px; width:25px; top:0; right:0">
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
                            @if ($ticket->open == 1)
                                <span class="badge badge-success">@lang('tickets.open')</span>
                            @else
                                <span class="badge badge-danger">@lang('tickets.close')</span>
                            @endif
                        </td>
                        <td>
                            {{ $ticket->getOpenDays() }}
                        </td>
                        <td class="d-flex">
                            <a href="{{ route('admin.tickets.show', $ticket->id) }}" class="btn btn-primary mr-2"
                                title="@lang('tickets.Detail')">
                                <i class="feather icon-eye"></i>
                            </a>
                            @if ($ticket->isOpen())
                                @can('close',App\Models\Ticket::class)
                                    <form action="{{ route('admin.ticket.mark-closed', $ticket) }}" method="post">
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
    <script>
        function toggleDateSearch() {
            const div = document.getElementById('dateSearch');
            if (div.style.display != 'block') {
                div.style.display = 'block';
            } else {
                div.style.display = 'none';
            }
        }

        function toggleOrderPageSearch() {
            const div = document.getElementById('singleSearch');
            console.log(div);
            if (div.style.display != 'block') {
                div.style.display = 'block';
            } else {
                div.style.display = 'none';
            }
        }
    </script>
</div>
