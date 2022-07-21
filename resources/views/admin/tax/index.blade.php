@extends('layouts.master')

@section('page')
    <section id="prealerts">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">
                            @lang('taxservice.Manage Tax Services')
                        </h4>
                        @can('create', App\Models\HandlingService::class)
                        <a href="{{ route('admin.tax.create') }}" class="btn btn-primary">
                            @lang('taxservice.Pay Tax')
                        </a>
                        @endcan
                    </div></br>
                    <div class="table-responsive-md mt-1 mr-4 ml-4">
                        <table class="table mb-0 table-responsive-md">
                            <thead>
                                <tr>
                                    <th>@lang('taxservice.User Name')</th>
                                    <th>@lang('taxservice.Warehouse No.')</th>
                                    <th>@lang('taxservice.Tracking Code')</th>
                                    <th>@lang('taxservice.Tax Payment 1')</th>
                                    <th>@lang('taxservice.Tax Payment 2')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($taxlist as $tax)
                                <tr>
                                    <td>{{ $tax->user->name }}</td>
                                    <td>
                                        <span>
                                            <a href="#" title="Click to see Shipment" data-toggle="modal" data-target="#hd-modal" data-url="{{ route('admin.modals.parcel.shipment-info',$tax->order_id) }}">
                                                WRH#: {{ $tax->order->warehouse_number }}
                                            </a>
                                        </span>
                                    </td>
                                    <td>{{ $tax->order->corrios_tracking_code }}</td>
                                    <td>{{ $tax->tax_1 }}</td>
                                    <td>{{ $tax->tax_2 }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('modal')
<x-modal />
@endsection
