<div @if($isComplete) wire:poll.30000ms @endif>
    <div class="row mb-2 no-print">
        <div class="col-1">
            <select class="form-control" wire:model="pageSize">
                <option value="1">1</option>
                <option value="5">5</option>
                <option value="10">10</option>
                <option value="20">20</option>
                <option value="50">50</option>
                <option value="100">100</option>
                <option value="300">300</option>
            </select>
        </div>
    </div>
    <table class="table mb-0 table-responsive-md">
        <thead>
            <tr>
                <th>@lang('orders.Report Name')</th>
                <th>@lang('orders.name')</th>
                <th>@lang('orders.From Date')</th>
                <th>@lang('orders.To Date')</th>
                <th>@lang('orders.action')</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reports as $report)
                <tr>
                    <td>{{ $report->name }}</td>
                    <td>{{ $report->user->name .' | '. $report->user->pobox_number }}</td>
                    <td>{{ date('d-M-Y', strtotime($report->start_date)) }}</td>
                    <td>{{ date('d-M-Y', strtotime($report->end_date)) }}</td>
                    <td>
                        @if($report->is_complete)
                            <a href="javascript:void(0)"><button class="btn btn-success btn-sm pr-3" wire:click="download({{ $report->id }})">Download</button></a>
                        @else
                            <button class="btn btn-warning btn-sm disabled">Processing..</button>
                        @endif
                            <a href="javascript:void(0)"><button type="" class="btn btn-danger btn-sm" wire:click="delete({{ $report->id }})">Delete</button></a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center h4 text-danger">No Record Found</td>
                </tr>                
            @endforelse
        </tbody>
    </table>
    <div class="d-flex justify-content-end my-2 pb-4 mx-2">
        {{ $reports->links() }}
    </div>
    {{-- @include('layouts.livewire.loading') --}}
</div>
