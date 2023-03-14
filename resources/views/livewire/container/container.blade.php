<div>
    <section id="prealerts">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        @section('title', __('warehouse.containers.Containers'))
                        <div>
                            <div id="printBtnDiv">
                                <button title="Assign AWB" id="assignAwb" type="btn"
                                    class="btn btn-primary mr-1 mb-1 waves-effect waves-light"><i
                                        class="fas fa-file-invoice"></i></button>

                            </div>
                        </div>
                        <div>
                            <button class="btn btn-primary waves-effect waves-light mr-1" title="search"
                                onclick="toggleLogsSearch()">
                                <i class="fa fa-search" aria-hidden="true"></i>
                            </button>
                            <a href="{{ route('warehouse.containers.create') }}" class="pull-right btn btn-primary">
                                @lang('warehouse.containers.Create Container') </a>
                        </div>
                    </div>
                    <div class="card-content card-body" style="min-height: 100vh;">

                        <div class="mb-2 row col-md-12 hide"
                            @if ($this->search || $this->packetType) style="display: flex !important;" @endif id="logSearch">
                            <form class="col-12 d-flex pl-0" wire:submit.prevent="render">
                                <div class="col-2 pl-0">
                                    <label>Search</label>
                                    <input type="search" class="form-control" wire:model.defer="search">
                                </div>
                                <div class="col-2">
                                    <label>Distribution Service Class</label>
                                    <select class="form-control" wire:model="packetType">
                                        <option value="">Select Type</option>
                                        <option value="NX">Packet Standard</option>
                                        <option value="IX">Packet Express</option>
                                        <option value="XP">Packet Mini</option>
                                        <option value="AJC-NX">Anjun Standard</option>
                                        <option value="AJC-IX">Anjun Express</option>
                                    </select>

                                </div>
                                <div class="mt-1">
                                    <button type="submit" class="btn btn-primary mt-4">
                                        <i class="fa fa-search"></i>
                                    </button>
                                    <button class="btn btn-primary ml-1 mt-4 waves-effect waves-light"
                                        onclick="window.location.reload();">
                                        <i class="fa fa-undo" data-bs-toggle="tooltip" title=""
                                            data-bs-original-title="fa fa-undo" aria-label="fa fa-undo"
                                            aria-hidden="true"></i></button>
                                </div>
                            </form>


                        </div>
                        <div class="mt-1 table-bordered">
                            <table class="table mb-0 table-bordered">
                                <thead>
                                    <tr>
                                        <th id="optionChkbx">
                                            <div class="vs-checkbox-con vs-checkbox-primary" title="Select All"
                                                style="margin-left: 12px;">
                                                <input type="checkbox" id="checkAll" name="orders[]" class="check-all"
                                                    value="">
                                                <span class="vs-checkbox vs-checkbox-sm">
                                                    <span class="vs-checkbox--check">
                                                        <i class="vs-icon feather icon-check"></i>
                                                    </span>
                                                </span>
                                            </div>
                                        </th>
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
                                            AWB#
                                        </th>
                                        <th>
                                            Status
                                        </th>
                                        <th>@lang('warehouse.actions.Action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($containers as $container)
                                        <tr>
                                            <td>
                                                <div class="vs-checkbox-con vs-checkbox-primary"
                                                    title="@lang('orders.Bulk Print')" style="margin-left: 12px;">
                                                    <input type="checkbox" name="containers[]"
                                                        onchange='handleChangeContainer(this);' class="bulk-container"
                                                        value="{{ $container->id }}">
                                                    <span class="vs-checkbox vs-checkbox-sm">
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
                                                {{ $container->getPiecesCount() }}
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
                                                {{ $container->awb }}
                                            </td>
                                            <td>
                                                @if (!$container->isRegistered())
                                                    <div class="btn btn-info">
                                                        New
                                                    </div>
                                                @endif
                                                @if ($container->isRegistered() && !$container->isShipped())
                                                    <div class="btn btn-primary">
                                                        Registered
                                                    </div>
                                                @endif

                                                @if ($container->isShipped())
                                                    <div class="btn btn-success">
                                                        Shipped
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="d-flex">
                                                <div class="btn-group">
                                                    <div class="dropdown">
                                                        <button type="button" data-toggle="dropdown"
                                                            aria-haspopup="true" aria-expanded="false"
                                                            class="btn btn-success btn-sm dropdown-toggle waves-effect waves-light"
                                                            style="width:100px;">
                                                            @lang('user.Action')
                                                        </button>
                                                        <div class="dropdown-menu dropdown-menu-right dropright">
                                                            <a href="{{ route('warehouse.containers.packages.index', $container) }}"
                                                                class="dropdown-item w-100">
                                                                <i class="feather icon-box"></i> @lang('warehouse.actions.Packages')
                                                            </a>
                                                            @if (!$container->isRegistered() || !$container->isShipped())
                                                                <a href="{{ route('warehouse.containers.edit', $container) }}"
                                                                    class="dropdown-item w-100">
                                                                    <i class="fa fa-edit"></i> @lang('warehouse.actions.Edit')
                                                                </a>

                                                                <a href="{{  $container->hasAnjunChinaService()?route('warehouse.anjun.container.register',$container):route('warehouse.container.register',$container) }}" class="dropdown-item w-100">
                                                                    <i class="feather icon-box"></i> Register Unit
                                                                </a>
                                                                <a href="{{ route('warehouse.container.cancel', $container) }}"
                                                                    class="dropdown-item w-100">
                                                                    <i class="feather icon-box"></i> Cancel Unit
                                                                </a>
                                                                <form
                                                                    action="{{ route('warehouse.containers.destroy', $container) }}"
                                                                    class="d-flex" method="post"
                                                                    onsubmit="return confirmDelete()">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button class="dropdown-item w-100 text-danger">
                                                                        <i class="feather icon-trash-2"></i>
                                                                        @lang('warehouse.actions.Delete')
                                                                    </button>
                                                                </form>
                                                            @endif
                                                            @if ($container->isRegistered())
                                                            <a href="{{$container->hasAnjunChinaService()?route('warehouse.anjun.container.download',$container):route('warehouse.container.download',$container) }}" class="dropdown-item w-100">
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
                                    Are you Sure want to Assign AWB number to Selected Containers {{-- <span class="result"></span> --}}
                                </p>
                                <input type="text" name="awb" required class="form-control" value="">
                                <input type="hidden" name="command" id="command" value="">
                                <input type="hidden" name="data" id="data" value="">
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary" id="save"> Yes Add</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                    @lang('consolidation.Cancel')</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>


        </div>
    </section>
    @include('layouts.livewire.loading')

</div>
@section('js')
    <script>
        $('body').on('click', '#assignAwb', function() {
            var containerIds = [];
            $.each($(".bulk-container:checked"), function() {
                containerIds.push($(this).val());
            });
            $('#bulk_sale_form #command').val('assign-awb');
            $('#bulk_sale_form #data').val(JSON.stringify(containerIds));
            $('#confirm').modal('show');
        })
        $('body').on('click', '#domesticPrint', function() {
            var orderIds = [];
            $.each($(".bulk-orders:checked"), function() {
                orderIds.push($(this).val());
                console.log($(this).val());
            });
            $('#consolidate_domestic_label_actions_form #command').val('consolidate-domestic-label');
            $('#consolidate_domestic_label_actions_form #data').val(JSON.stringify(orderIds));
            $('#consolidate_domestic_label_actions_form').submit();
        })
        $('body').on('click', '#trash', function() {
            var orderIds = [];
            $.each($(".bulk-orders:checked"), function() {
                orderIds.push($(this).val());
            });

            $('#trash_order_actions_form #command').val('move-order-trash');
            $('#trash_order_actions_form #data').val(JSON.stringify(orderIds));
            $('#trash_order_actions_form').submit();

        })
        $('body').on('change', '#checkAll', function() {
            if ($('#checkAll').is(':checked')) {
                $('.bulk-container').prop('checked', true)
                document.getElementById("printBtnDiv").style.display = 'block';
            } else {
                $('.bulk-container').prop('checked', false)
                console.log($(".bulk-container:checked").length);
                document.getElementById("printBtnDiv").style.display = 'none';
            }
        })
    </script>
@endsection
