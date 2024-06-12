@extends('layouts.master')

@section('page')
    <section id="prealerts">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">
                            @lang('shippingservice.Manage Shipping Services')
                        </h4>
                        @can('create', App\Models\ShippingService::class)
                            <a href="{{ route('admin.shipping-services.create') }}" class="btn btn-primary">
                                @lang('shippingservice.Create Shipping Service')
                            </a>
                        @endcan
                    </div>
                    <div class="card-content">
                        <div class="table-responsive-md mt-1">
                            <table class="table table-hover-animation mb-0">
                                <thead>
                                <tr>
                                    <th>@lang('shippingservice.Name')</th>
                                    <th>@lang('shippingservice.Max length allowed')</th>
                                    <th>@lang('shippingservice.Max width allowed')</th>
                                    <th>@lang('shippingservice.Min width allowed')</th>
                                    <th>@lang('shippingservice.Min length allowed')</th>
                                    <th>@lang('shippingservice.Max sum of all sides')</th>
                                    <th>@lang('shippingservice.Contains battery charges')</th>
                                    <th>@lang('shippingservice.Contains perfume charges')</th>
                                    <th>@lang('shippingservice.Contains flammable liquid charges')</th>
                                    <th>Sub Class</th>
                                    <th>@lang('shippingservice.Action')</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($shippingservices as $service)
                                    <tr>
                                        <td>{{ $service->name }}</td>
                                        <td>{{ $service->max_length_allowed }}</td>
                                        <td>{{ $service->max_width_allowed }}</td>
                                        <td>{{ $service->min_width_allowed }}</td>
                                        <td>{{ $service->min_length_allowed }}</td>
                                        <td>{{ $service->max_sum_of_all_sides }}</td>
                                        <td>{{ $service->contains_battery_charges }}</td>
                                        <td>{{ $service->contains_perfume_charges }}</td>
                                        <td>{{ $service->contains_flammable_liquid_charges }}</td>
                                        <td>{{ $service->service_sub_class }}</td>
                                        <td>
                                            @can('update', App\Models\ShippingService::class)
                                            <a href="{{ route('admin.shipping-services.edit',$service) }}" title="@lang('shippingservice.Edit Service')" class="btn btn-sm btn-primary mr-2">
                                                <i class="feather icon-edit"></i>
                                            </a>
                                            @endcan

                                            @can('delete', App\Models\ShippingService::class)
                                            <form action="{{ route('admin.shipping-services.destroy',$service) }}" method="POST" onsubmit="return confirmDelete()" class="d-inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button title="@lang('shippingservice.Delete Service')" class="btn btn-sm btn-danger mr-2">
                                                    <i class="feather icon-trash"></i>
                                                </button>
                                            </form>
                                            @endcan
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
 