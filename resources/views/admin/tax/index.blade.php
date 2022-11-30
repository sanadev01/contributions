@extends('layouts.master')

@section('page')
    <section id="prealerts">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-end">
                        @section('title', __('tax.Manage Tax Services'))
                        @can('create', App\Models\HandlingService::class)
                            <button type="btn" onclick="toggleOrderPageSearch()" id="orderSearch" class="btn btn-primary mr-1">
                                <i class="feather icon-search"></i>
                            </button>
                            <a href="{{ route('admin.tax.create') }}" class="btn btn-primary">
                                @lang('tax.Pay Tax')
                            </a>
                        @endcan
                    </div></br>
                    <div class="table-responsive-md mt-1 mr-4 ml-4 mb-5">
                        <div class="filters p-2" id="singleSearch"
                            @if (old('search', request('search'))) style="display: block" @endif>
                            <div class="row">
                                <div class="col-md-6">
                                    <form action="" method="GET">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <input type="search" class="form-control" name="search" value="{{ old('search',request('search')) }}" placeholder="@lang('tax.Search By Name, Warehouse No. or Tracking Code')">
                                            </div>
                                            <div class="col-md-4">
                                                <button class="btn btn-primary">
                                                    @lang('user.Search')
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="col-md-6 pr-0">
                                    <form action="{{ route('admin.reports.tax-report') }}" method="GET" class="d-flex justify-content-end text-right">
                                        <div class="row col-md-12">
                                            <div class="col-md-2 text-right">
                                                <label>Start Date</label>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="date" class="form-control" name="start_date" >
                                            </div>
                                            <div class="col-md-2 text-right">
                                                <label>End Date</label>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="date" class="form-control" name="end_date" >
                                            </div>
                                            <div class="col-md-2">
                                                <button class="btn btn-success">
                                                    Download
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <table class="table mb-0 table-bordered table-responsive-sm">
                            <thead>
                                <tr>
                                    <th>@lang('tax.User Name')</th>
                                    <th>@lang('tax.Warehouse No.')</th>
                                    <th>@lang('tax.Tracking Code')</th>
                                    <th>@lang('tax.Tax Payment')</th> 
                                    <th>@lang('tax.Herco Buying Rate') </th>
                                    <th>@lang('tax.Herco Selling Rate') </th>
                                    <th>@lang('tax.Herco Buying USD') </th>
                                    <th>@lang('tax.Herco Selling USD')</th>
                                    <th>@lang('Profit USD')</th>
                                    <th>@lang('tax.Receipt')</th>
                                    <th>@lang('tax.Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($taxes as $tax)
                                <tr>
                                    <td>{{ $tax->user->name }}</td>
                                    <td>
                                        <span>
                                            <a href="#" title="Click to see Shipment" data-toggle="modal"
                                                data-target="#hd-modal"
                                                data-url="{{ route('admin.modals.parcel.shipment-info', $tax->order_id) }}">
                                                WRH#: {{ $tax->order->warehouse_number }}
                                            </a>
                                        </span>
                                    </td>
                                    <td>{{ $tax->order->corrios_tracking_code }}</td>
                                    <td>{{ $tax->tax_payment }}</td>
                                    <td>{{ $tax->buying_br }}</td>
                                    <td>{{ $tax->selling_br }}</td>
                                    <td>{{ $tax->buying_usd }}</td>
                                    <td>{{ $tax->selling_usd }}</td>
                                    <td>{{ ( $tax->selling_usd - $tax->buying_usd ) }}</td>
                                    <td>
                                        @if(optional($tax->deposit)->depositAttchs)
                                            @foreach ($tax->deposit->depositAttchs as $attachedFile )
                                            <div class="{{$loop->first? '':'mt-2'}}"> 
                                                <a target="_blank" href="{{ $attachedFile->getPath() }}">Download</a><br>
                                            </div>
                                            @endforeach
                                        @else
                                            Not Found
                                        @endif
                                    </td>
                                    <td class="d-flex">
                                        <a href="{{ route('admin.tax.edit',$tax->id) }}" class="btn btn-primary mr-2" title="Edit">
                                            <i class="feather icon-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer d-flex justify-content-end">
                        {{ $taxes->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>

        </div>
    </section>
@endsection
@section('modal')
<x-modal />
@endsection
