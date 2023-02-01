@extends('layouts.master')
@section('page')
    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">{{ $report->name }} Report</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <div class="row mb-4 no-print ">
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
                                                <td><button class="btn btn-warning btn-sm disabled">Processing</button></td>
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
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('modal')
<x-modal />
@endsection
