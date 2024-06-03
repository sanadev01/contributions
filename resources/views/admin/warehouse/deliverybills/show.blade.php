@extends('layouts.master')

@section('page')
<section id="prealerts">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        @lang('warehouse.containers.Containers')
                    </h4>
                    <a href="{{ route('warehouse.delivery_bill.index') }}" class="pull-right btn btn-primary">@lang('warehouse.deliveryBill.List Delivery Bills')</a>
                </div>
                <div class="card-content card-body" style="min-height: 100vh;">
                    <div class="mt-1">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>@lang('warehouse.containers.Dispatch Number')</th>
                                    <th>@lang('warehouse.containers.Seal No')</th>
                                    <th>
                                        Weight (Kg) / Pieces
                                    </th>
                                    <th>
                                        @lang('warehouse.containers.Origin Country')
                                    </th>
                                    <th>@lang('warehouse.containers.Destination Airport')</th>
                                    <th>@lang('warehouse.containers.Container Type')</th>
                                    <th>@lang('warehouse.containers.Distribution Service Class')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($containers as $container)
                                <tr>
                                    <td>{{ $container->dispatch_number }}</td>
                                    <td>{{ $container->seal_no }}</td>
                                    <td>
                                        {{ $container->total_weight }} KG / {{ $container->total_orders }}
                                    </td>
                                    <td>
                                        {{ $container->origin_country }}
                                    </td>
                                    <td>
                                        {{ $container->destination_ariport }}
                                    </td>
                                    <td>
                                        {{ $container->container_type }}
                                    </td>
                                    <td>
                                        {{ $container->service_subclass_name }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="d-flex justify-content-end py-2 px-3">
                            {{ $containers->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection