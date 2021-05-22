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
                        <a href="{{ route('warehouse.containers.create') }}" class="pull-right btn btn-primary"> @lang('warehouse.containers.Create Container') </a>
                    </div>
                    <div class="card-content card-body" style="min-height: 100vh;">
                        <div class="mt-1">
                            <table class="table mb-0">
                                <thead>
                                <tr>
                                    <th>@lang('warehouse.containers.Dispatch Number')</th>
                                    <th>@lang('warehouse.containers.Seal No')</th>
                                    <th>
                                        Weight (Kg)
                                    </th>
                                    <th>
                                        Pieces
                                    </th>
                                    <th>
                                        @lang('warehouse.containers.Origin Country')
                                    </th>
                                    <th>@lang('warehouse.containers.Destination Airport')</th>
                                    <th>@lang('warehouse.containers.Container Type')</th>
                                    <th>@lang('warehouse.containers.Distribution Service Class')</th>
                                    <th>
                                        Unit Code
                                    </th>
                                    <th>
                                        Status
                                    </th>
                                    <th>@lang('warehouse.actions.Action')</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($containers as $container)
                                    <tr>
                                        <td>{{ $container->dispatch_number }}</td>
                                        <td>{{ $container->seal_no }}</td>
                                        <td>
                                            {{ $container->getWeight() }} KG
                                        </td>
                                        <td>
                                            {{  $container->getPiecesCount() }}
                                        </td>
                                        <td>
                                            {{ $container->origin_country }}
                                        </td>
                                        <td>
                                            {{ $container->getDestinationAriport() }}
                                        </td>
                                        <td>
                                            {{ $container->getContainerType() }}
                                        </td>
                                        <td>
                                            {{ $container->getServiceSubClass() }}
                                        </td>
                                        <td>
                                            {{ $container->getUnitCode() }}
                                        </td>
                                        <td>
                                            @if(!$container->isRegistered())
                                                <div class="btn btn-info">
                                                    New
                                                </div>
                                            @endif
                                            @if($container->isRegistered())
                                                <div class="btn btn-success">
                                                    Registered
                                                </div>
                                            @endif
                                        </td>
                                        <td class="d-flex">
                                            <div class="btn-group">
                                                <div class="dropdown">
                                                    <button type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="btn btn-success dropdown-toggle waves-effect waves-light">
                                                        @lang('user.Action')
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-right dropright">
                                                        <a href="{{ route('warehouse.containers.packages.index',$container) }}" class="dropdown-item w-100">
                                                            <i class="feather icon-box"></i> @lang('warehouse.actions.Packages')
                                                        </a>
                                                        @if( !$container->isRegistered() )
                                                            <a href="{{ route('warehouse.containers.edit',$container) }}" class="dropdown-item w-100">
                                                                <i class="fa fa-edit"></i> @lang('warehouse.actions.Edit')
                                                            </a>
                                                            
                                                            <a href="{{ route('warehouse.container.register',$container) }}" class="dropdown-item w-100">
                                                                <i class="feather icon-box"></i> Register Unit
                                                            </a>
                                                            <form action="{{ route('warehouse.containers.destroy',$container) }}" class="d-flex" method="post" onsubmit="return confirmDelete()">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button class="dropdown-item w-100 text-danger">
                                                                    <i class="feather icon-trash-2"></i> @lang('warehouse.actions.Delete')
                                                                </button>
                                                            </form>
                                                        @endif
                                                        @if( $container->isRegistered() )
                                                            <a href="{{ route('warehouse.container.download',$container) }}" class="dropdown-item w-100">
                                                                <i class="feather icon-box"></i> Get CN35
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
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
