@extends('layouts.master')

@section('page')
    <section id="prealerts">
        <div class="row">
            <div class="col-12">
                <div class="card">
<<<<<<<< HEAD:resources/views/admin/warehouse/totalExpressContainers/index.blade.php
                    <div class="card-header">
                        <h4 class="mb-0">
                            @lang('warehouse.containers.Containers')
                        </h4>
                        <a href="{{ route('warehouse.totalexpress_containers.create') }}" class="pull-right btn btn-primary"> @lang('warehouse.containers.Create Container') </a>
                    </div>
========
                    <div class="row mt-4 ">
                        <div class="col-md-1">
                            <div class="card-header">
                                <h4 class="mb-0">
                                    @lang('warehouse.containers.Containers')
                                </h4>
                            </div>
                        </div>
                        <div class="col-md-2">
                        </div>
                        <div class="col-md-2">
                            <label>Start Date</label>
                            <input type="date" class="form-control mb-2 mr-sm-2" wire:model="startDate">
                        </div>
                        <div class="col-md-2">
                            <label>End Date</label>
                            <input type="date" class="form-control" wire:model="endDate">
                        </div>
                        <div class="col-md-2">
                            <label>Service</label>
                            <select class="form-control mb-2 mr-sm-2" wire:model="service">
                                <option value="">All</option>
                                <option value="{{ json_encode(['NX', 'IX']) }}">Correios Brazil</option>
                                <option value="{{ json_encode(['AJ-IX', 'AJ-NX']) }}">Anjun </option>
                            </select>
                        </div>
                        <div class="col-md-1 d-flex mt-2">
                            <div class="mt-4">
                                <button wire:click="download" class="btn btn-success waves-effect waves-light"
                                    type="submit">Download</button>
                            </div>
                        </div>
                        <div class="col-md-2 d-flex mt-2 justify-content-center ">
                            <div class="mt-4">
                                <a href="{{ route('warehouse.containers.create') }}" class="pull-right btn btn-primary">
                                    @lang('warehouse.containers.Create Container') </a>

                            </div>
                        </div>
                    </div> 
>>>>>>>> 148a108bb953eb768579be0a29bd022d7e94a8a6:resources/views/livewire/container/container.blade.php
                    <div class="card-content card-body" style="min-height: 100vh;">
                        <div class="mt-1">
                            <table class="table mb-0">
                                <thead>
                                    <tr>
                                        <th style="min-width: 100px;">
                                            <select name="" id="bulk-actions" class="form-control">
                                                <option value="clear">Clear All</option>
                                                <option value="checkAll">Select All</option>
                                                <option value="assign-awb">Assign AWB</option>
                                            </select>
                                        </th>
                                        <th>@lang('warehouse.containers.Dispatch Number')</th>
                                        <th>@lang('warehouse.containers.Seal No')</th>
                                        <th>Weight (Kg)</th>
                                        <th>Pieces</th>
                                        <th>@lang('warehouse.containers.Origin Country')</th>
                                        <th>@lang('warehouse.containers.Destination Airport')</th>
                                        <th>@lang('warehouse.containers.Container Type')</th>
                                        <th>@lang('warehouse.containers.Distribution Service Class')</th>
                                        <th>Unit Code</th>
                                        <th>AWB#</th>
                                        <th>Status</th>
                                        <th>@lang('warehouse.actions.Action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($containers as $container)
                                    <tr>
                                        <td>
                                            <div class="vs-checkbox-con vs-checkbox-primary" title="@lang('orders.Bulk Print')">
                                                <input type="checkbox" name="containers[]" class="bulk-container" value="{{$container->id}}">
                                                <span class="vs-checkbox vs-checkbox-lg">
                                                    <span class="vs-checkbox--check">
                                                        <i class="vs-icon feather icon-check"></i>
                                                    </span>
                                                </span>
                                                <span class="h3 mx-2 text-primary my-0 py-0"></span>
                                            </div>
                                        </td>
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
                                        <td>{{ $container->getUnitCode() }}</td>
                                        <td>
                                            @if ( !$container->awb)
                                                <span class="text-danger font-italic">Awb Number Required</span>
                                            @endif
                                            {{ $container->awb }}
                                        </td>
                                        <td>
                                            @if(!$container->isRegistered())
                                                <div class="btn btn-info">
                                                    New
                                                </div>
                                            @endif
                                            @if($container->isRegistered() && !$container->isShipped())
                                                <div class="btn btn-primary">
                                                    Registered
                                                </div>
                                            @endif

                                            @if($container->isShipped())
                                                <div class="btn btn-success">
                                                    Shipped
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
                                                        <a href="{{ route('warehouse.totalexpress_container.packages.index',$container) }}" class="dropdown-item w-100">
                                                            <i class="feather icon-box"></i> @lang('warehouse.actions.Packages')
                                                        </a>
                                                        @if( !$container->isRegistered() )
                                                            <a href="{{ route('warehouse.totalexpress_containers.edit',$container) }}" class="dropdown-item w-100">
                                                                <i class="fa fa-edit"></i> @lang('warehouse.actions.Edit')
                                                            </a>
                                                            @if( !$container->isRegistered() && $container->hasOrders())
                                                                <a href="{{ route('warehouse.totalexpress_container.createRequest',$container) }}" class="dropdown-item w-100">
                                                                    <i class="feather icon-box"></i> Create Unit
                                                                </a>
                                                            @endif
                                                            @if( $container->unit_response_list )
                                                                <a href="{{ route('warehouse.totalexpress_container.registerBox',$container) }}" class="dropdown-item w-100">
                                                                    <i class="feather icon-box"></i> Register MasterBox
                                                                </a>
                                                            @endif
                                                            <form action="{{ route('warehouse.totalexpress_containers.destroy',$container) }}" class="d-flex" method="post" onsubmit="return confirmDelete()">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button class="dropdown-item w-100 text-danger">
                                                                    <i class="feather icon-trash-2"></i> @lang('warehouse.actions.Delete')
                                                                </button>
                                                            </form>
                                                        @endif
                                                        @if( $container->isRegistered() )
                                                        <a href="{{ optional(optional(json_decode($container->unit_response_list))->data)->pdf_label_url }}" class="dropdown-item w-100">
                                                            <i class="feather icon-box"></i> Get CN35
                                                        </a>
                                                        {{-- <a href="{{ route('warehouse.totalexpress_container.download',['container'=>$container,'type'=>'hd']) }}" class="dropdown-item w-100">
                                                            <i class="feather icon-box"></i> HD CN35
                                                        </a> --}}
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
            <div class="modal fade" id="confirm" role="dialog">
                <div class="modal-dialog modal-lg">
                  <div class="modal-content">
                    <div class="modal-header">
                        <div class="col-8">
                            <h4>
                               Are you Sure!
                            </h4>
                        </div>
                    </div>
                    <form action="{{ route('warehouse.container.awb') }}" method="GET" id="bulk_sale_form">
                        <div class="modal-body" style="font-size: 15px;">
                            <p>
                                Are you Sure want to Assign AWB number to Selected Containers  {{-- <span class="result"></span> --}}
                            </p>
                            <input type="text" name="awb" required class="form-control" value="">
                            <input type="hidden" name="command" id="command" value="">
                            <input type="hidden" name="data" id="data" value="">
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary" id="save"> Yes Add</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal"> @lang('consolidation.Cancel')</button>
                        </div>
                    </form>
                  </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('js')
<script>
    $(document).ready(function(){
        $('#upload_manifest').click(function(){
                $('#loading').fadeIn();
            }); 
        });
</script>

    <script>
        $('body').on('change','#bulk-actions',function(){
            if ( $(this).val() == 'clear' ){
                $('.bulk-container').prop('checked',false)
            }else if ( $(this).val() == 'checkAll' ){
                $('.bulk-container').prop('checked',true)
            }else if ( $(this).val() == 'assign-awb' ){
                var containerIds = [];
                $.each($(".bulk-container:checked"), function(){
                    containerIds.push($(this).val());
                    
                    // $(".result").append('HD-' + this.value + ',');
                });
                
                $('#bulk_sale_form #command').val('assign-awb');
                $('#bulk_sale_form #data').val(JSON.stringify(containerIds));
                $('#confirm').modal('show');
                // $('#bulk_sale_form').submit();
            }
        })
    </script>
@endsection