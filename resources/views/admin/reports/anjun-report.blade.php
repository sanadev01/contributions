@extends('layouts.master')

@section('page')
    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">
                                @lang('menu.Reports.Service Report')
                        </h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
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
                                <div class="col-5">
                                    <form action="{{ url()->current() }}" method="GET" id="searchForm">
                                        <div class="row">                                                
                                            <div class="col-md-7">
                                                <input type="hidden" id="hiddenType"  name="type" value="{{request('type')}}" >
                                                <input type="search" class="form-control" name="search" value="{{ old('search',request('search')) }}" placeholder="@lang('orders.Search By Name, Warehouse No. or Tracking Code')">
                                            </div>
                                            <div class="col-md-2">
                                                <button class="btn btn-primary btn-md">
                                                    @lang('user.Search')
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="col-6 text-right">
                                    <form action="{{ route('admin.order.exports') }}" method="GET" target="_blank">
                                        @csrf
                                       
                                        <div class="row">
                                            <div class="offset-1 col-md-3">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                    <select name="type"  class="form-control" id="searchType"   >
                                                        <option class="form-control" value="bcn" @if(request('type')=='bcn') selected @endif>BCN</option>
                                                        <option class="form-control" value="anjun"  @if(request('type')=='anjun') selected @endif>ANJUN</option>
                                                    </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
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
                                                <button class="btn btn-success" title="@lang('orders.import-excel.Download')">
                                                    <i class="fa fa-arrow-down"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <table class="table mb-0 table-responsive-md">
                                <thead>
                                    <tr>
                                        <th>@lang('orders.date')</th>
                                        <th>@lang('orders.warehouse-no')</th>
                                        <th>@lang('orders.user-name')</th>
                                        <th>@lang('orders.tracking-code')</th>
                                        <th>@lang('orders.Amount Customers Paid')</th>
                                        <th>@lang('orders.Correios Anjun')</th>
                                        <th>@lang('Anjun Commission')</th>
                                        <th>@lang('orders.status')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orders as $order)
                                        <tr>
                                            <td>{{ date('d-m-Y', strtotime($order->order_date)) }}</td>
                                            <td>
                                                <span>
                                                    <a href="#" title="Click to see Shipment" data-toggle="modal" data-target="#hd-modal" data-url="{{ route('admin.modals.parcel.shipment-info',$order->id) }}">
                                                          {{ $order->warehouse_number }}
                                                    </a>
                                                </span>
                                            </td>
                                            <td>{{ $order->user->name }}</td>
                                            <td>{{ $order->corrios_tracking_code }}</td>
                                            <td>{{ $order->gross_total }}</td>
                                            <td><livewire:reports.anjun-report :order="$order" :isCommission="false"/></td>
                                            <td><livewire:reports.anjun-report :order="$order" :isCommission="true"/></td>
                                            <td>{{ $order->status_name }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="d-flex justify-content-end my-2 pb-4 mx-2">
                                {{ $orders->links('pagination::bootstrap-4') }}
                            </div>
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
@section('js')
 
<script>
    $(document).ready(function () {
        $('#searchType').change(function () {
            $('#hiddenType').val($(this).val()) 
            $('#searchForm').submit();
         
        });
    });
</script>
@endsection