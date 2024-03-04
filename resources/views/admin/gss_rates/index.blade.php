@extends('layouts.master') 

@section('page')
    <section id="prealerts">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">
                            @lang('gssRate.Manage GSS Rates')
                        </h4>
                        @can('create', App\Models\GSSRate::class)
                        <a href="{{ route('admin.gss-rates.create') }}" class="btn btn-primary">
                            @lang('gssRate.Create GSS Rates')
                        </a>
                        @endcan
                    </div>
                    <div class="card-content">
                        <div class="table-responsive-md mt-1">
                            <table class="table table-hover-animation mb-0">
                                <thead>
                                <tr>
                                    <th>@lang('gssRate.User')</th>
                                    <th>@lang('gssRate.Shipping Service')</th>
                                    <th>@lang('gssRate.Country')</th>
                                    <th>@lang('gssRate.Api Discount')</th>
                                    <th>@lang('gssRate.User Discount')</th>
                                    <th>@lang('gssRate.Action')</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($gssRates as $gssRate)
                                    <tr>
                                        <td>{{ $gssRate->user->full_name }}</td>
                                        <td>{{ $gssRate->shipping_service->name }} </td>
                                        <td>{{ $gssRate->country->name }} </td>
                                        <td>{{ $gssRate->api_discount  }} </td>
                                        <td>{{ $gssRate->user_discount  }} </td>
                                        <td>
                                            @can('update', $gssRate)
                                                <a href="{{ route('admin.gss-rates.edit',$gssRate) }}" title="@lang('gssRate.Edit gss rate')" class="btn btn-sm btn-primary mr-2">
                                                    <i class="feather icon-edit"></i>
                                                </a>
                                            @endcan

                                            @can('delete', $gssRate)
                                                <form action="{{ route('admin.gss-rates.destroy',$gssRate) }}" method="POST" onsubmit="return confirmDelete()" class="d-inline-block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button title="@lang('gssRate.Delete gss rate')" class="btn btn-sm btn-danger mr-2">
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
