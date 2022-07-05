@extends('layouts.master') 

@section('page')
    <section id="prealerts">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">
                            @lang('handlingservice.Manage Services')
                        </h4>
                        @can('create', App\Models\HandlingService::class)
                        <a href="{{ route('admin.tax.create') }}" class="btn btn-primary">
                            @lang('Create')
                        </a>
                        @endcan
                    </div>
                    <div class="card-content">
                        <livewire:tax.tax/>
                        <div class="row col-12 table-responsive-md m-1">
                            <table class="table table-hover-animation mb-0">
                                <thead>
                                <tr>
                                    <th>@lang('handlingservice.Name')</th>
                                    <th>@lang('handlingservice.Cost')</th>
                                    <th>@lang('handlingservice.Price')</th>
                                    <th>@lang('handlingservice.Profit')</th>
                                    <th>@lang('handlingservice.Action')</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($services ?? [] as $service)
                                    <tr>
                                        <td>{{ $service->name }}</td>
                                        <td>
                                            {{ $service->cost }} @lang('handlingservice.USD')
                                        </td>
                                        <td>
                                            {{ $service->price }} @lang('handlingservice.USD')
                                        </td>
                                        <td>
                                            {{ $service->price -$service->cost  }} @lang('handlingservice.USD')
                                        </td>
                                        
                                        <td>
                                            @can('update', $service)
                                                <a href="{{ route('admin.handling-services.edit',$service) }}" title="@lang('handlingservice.Edit Service')" class="btn btn-sm btn-primary mr-2">
                                                    <i class="feather icon-edit"></i>
                                                </a>
                                            @endcan

                                            @can('delete', $service)
                                                <form action="{{ route('admin.handling-services.destroy',$service) }}" method="POST" onsubmit="return confirmDelete()" class="d-inline-block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button title="@lang('handlingservice.Delete Service')" class="btn btn-sm btn-danger mr-2">
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
