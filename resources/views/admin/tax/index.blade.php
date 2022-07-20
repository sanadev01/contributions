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
                    <div class="card-content">
                        <div class="table-responsive-md mt-1">
                            <table class="table table-hover-animation mb-0">
                                <thead>
                                <tr>
                                    <th>@lang('taxservice.Order ID')</th>
                                    <th>@lang('taxservice.User Name')</th>
                                    <th>@lang('taxservice.Tracking Code')</th>
                                    <th>@lang('taxservice.Tax Payment 1')</th>
                                    <th>@lang('taxservice.Tax Payment 2')</th>
                                    <th>@lang('taxservice.Action')</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1137</td>
                                        <td>Test Name</td>
                                        <td>BN45680023440</td>
                                        <td>20.0</td>
                                        <td>25.0</td>
                                        <td>
                                            @can('update', App\Models\ShippingService::class)
                                            <a href="{{ route('admin.shipping-services.edit',1) }}" title="@lang('shippingservice.Edit Service')" class="btn btn-sm btn-primary mr-2">
                                                <i class="feather icon-edit"></i>
                                            </a>
                                            @endcan

                                            @can('delete', App\Models\ShippingService::class)
                                            <form action="{{ route('admin.shipping-services.destroy',1) }}" method="POST" onsubmit="return confirmDelete()" class="d-inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button title="@lang('shippingservice.Delete Service')" class="btn btn-sm btn-danger mr-2">
                                                    <i class="feather icon-trash"></i>
                                                </button>
                                            </form>
                                            @endcan
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
