@extends('layouts.master')
@section('page')
    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">@lang('orders.Key Performance Indicator Report')</h4>
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
                                <div class="col-11 text-right">
                                    <form action="{{ route('admin.reports.kpi-report.index') }}" method="GET">
                                        @csrf
                                        <div class="row">
                                            <div class="offset-2 col-md-3">
                                                <div class="row">
                                                    <div class="col-md-4 mt-2">
                                                        <label>Start Date</label>
                                                    </div>
                                                    <div class="col-md-8 pl-0 pr-0">
                                                        <input type="date" name="start_date" class="form-control">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="row">
                                                    <div class="col-md-4 mt-2 pl-0">
                                                        <label>End Date</label>
                                                    </div>
                                                    <div class="col-md-8 pl-0">
                                                        <input type="date" name="end_date" class="form-control">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-1">
                                                <button class="btn btn-primary btn-md">
                                                    @lang('user.Search')
                                                </button>
                                            </div>
                                            </form>
                                                <form action="{{ route('admin.reports.kpi-report.create') }}" method="GET" target="_blank">
                                                    @csrf
                                                    <div class="offset-2 col-md-1">
                                                        <button class="btn btn-success" title="@lang('orders.import-excel.Download')">
                                                            <i class="fa fa-arrow-down"></i>
                                                        </button>
                                                    </div>
                                                </form>
                                        </div>
                                    
                                </div>
                            </div>
                            <table class="table mb-0 table-responsive-md">
                                <thead>
                                    <tr>
                                        <th>@lang('orders.Tracking')</th>
                                        <th>@lang('orders.Type Package')</th>
                                        <th>@lang('orders.First Event')</th>
                                        <th>@lang('orders.Last Event')</th>
                                        <th>@lang('orders.Days Between')</th>
                                        <th>@lang('orders.Last Event')</th>
                                        <th>@lang('orders.Taxed')</th>
                                        <th>@lang('orders.Delivered')</th>
                                        <th>@lang('orders.Returned')</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($trackings['return']['objeto'] as $data)
                                    @dump($data);
                                        @if(isset($data['evento']))
                                            <tr>
                                                @if(optional($data) && isset($data['numero']))
                                                    <td>{{ $data['numero'] }}</td>
                                                    <td><span>{{ $data['categoria'] }}</span></td>
                                                    <td>{{ $data['evento'][count($data['evento'])-1]['data'] }}</td>
                                                    <td>{{ $data['evento'][0]['data'] }}</td>
                                                    <td>{{ sortTrackingEvents($data)['diffDates'] }}</td>
                                                    <td>{{ $data['evento'][0]['descricao'] }}</td>
                                                    <td>{{ sortTrackingEvents($data)['taxed'] }}</td>
                                                    <td>{{ sortTrackingEvents($data)['delivered'] }}</td>
                                                    <td>{{ sortTrackingEvents($data)['returned'] }}</td>
                                                @else
                                                <td colspan='9'>No Trackings Found</td>
                                                @endif
                                            </tr>
                                        @endif
                                    @endforeach
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
