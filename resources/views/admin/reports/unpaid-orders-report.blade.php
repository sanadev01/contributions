@extends('layouts.master')
@section('page')
    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Un Paid Order Report</h4>
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
                                    <form action="{{ route('admin.reports.unpaid-orders') }}" method="GET">
                                        @csrf
                                        <div class="row">
                                            <div class="offset-4 col-md-3">
                                                <div class="row">
                                                    <div class="col-md-4 mt-2">
                                                        <label>Start Date</label>
                                                    </div>
                                                    <div class="col-md-8 pl-0 pr-0">
                                                        <input type="date" name="start_date" class="form-control" id="startDate" placeholder="mm/dd/yyyy">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="row">
                                                    <div class="col-md-4 mt-2 pl-0">
                                                        <label>End Date</label>
                                                    </div>
                                                    <div class="col-md-8 pl-0">
                                                        <input type="date" name="end_date" class="form-control" id="endDate" placeholder="mm/dd/yyyy">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-1">
                                                <button class="btn btn-primary btn-md">
                                                    @lang('user.Search')
                                                </button>
                                            </div>
                                        </form>
                                        <form action="{{ route('admin.reports.unpaid-orders-download') }}" method="POST">
                                            @csrf
                                            @if($unPaidOrders)
                                                <input type="hidden" name="order" value="{{ collect($unPaidOrders) }}">
                                            @endif   
                                            <div class="col-md-1 justify-content-end">
                                                <button class="btn btn-success" {{ !empty($unPaidOrders)? '' : 'disabled' }}  title="@lang('orders.import-excel.Download')">
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
                                        <th>User Name</th>
                                        <th>Po Box Number</th>
                                        <th>Tracking Code</th>
                                        <th>Create Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($unPaidOrders)
                                        @foreach($unPaidOrders as $order)
                                            <tr>
                                                <td>{{ $order->user->name }}</td>
                                                <td>{{ $order->user->pobox_number }}</td>
                                                <td>{{ $order->corrios_tracking_code }}</td>
                                                <td>{{ date('d-M-Y', strtotime($order->created_at)) }}</td>
                                            </tr>
                                        @endforeach
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
