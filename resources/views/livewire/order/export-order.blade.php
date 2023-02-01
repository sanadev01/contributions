<div @if(!$report->is_complete) wire:poll.30000ms @endif>
    <div class="card-header">
        <h4 class="mb-0">{{ $report->name }} Report</h4>
    </div><br>
    <table class="table mb-0 table-responsive-md">
        <thead>
            <tr>
                <th>@lang('orders.Report Name')</th>
                <th>@lang('orders.From Date')</th>
                <th>@lang('orders.To Date')</th>
                <th>@lang('orders.Status')</th>
            </tr>
        </thead>
        <tbody>
            @if($report)
                <tr>
                    <td>{{ $report->name }}</td>
                    <td>{{ date('d-M-Y', strtotime($report->start_date)) }}</td>
                    <td>{{ date('d-M-Y', strtotime($report->end_date)) }}</td>
                    @if($report->is_complete == '0')
                        <td><button class="btn btn-warning btn-sm disabled">Processing..</button></td>
                    @else
                        <td><a href="{{ $report->path }}" download><button class="btn btn-success btn-sm">Download</button></a></td>
                    @endif
                </tr>
            @else
                <tr><td colspan='4'>No Reports Found</td></tr>
            @endif
        </tbody>
    </table>
    @include('layouts.livewire.loading')
</div>
