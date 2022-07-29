@extends('layouts.master')

@section('page')
    <section id="prealerts">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div id="col-6">
                            <div class="col-12 pl-0" id="printBtnDiv">
                                <button title="Print Labels" id="assignAWB" type="btn"
                                    class="btn btn-primary mr-1 mb-1 waves-effect waves-light"><i
                                        class="fas fa-file-invoice"></i></button>
                            </div>
                        </div>
                        <div class="col-6 d-flex justify-content-end pr-0">
                        @section('title', __('warehouse.containers.Containers'))
                        <a href="{{ route('warehouse.usps_containers.create') }}" class="btn btn-primary">
                            @lang('warehouse.containers.Create Container') </a>
                    </div>
                </div>
                <div class="card-content card-body" style="min-height: 100vh;">
                    <div class="mt-1">
                        <table class="table mb-0 table-bordered">
                            <thead>
                                <tr>
                                    <th id="optionChkbx">
                                        <div class="vs-checkbox-con vs-checkbox-primary" title="Select All">
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
                                            <div class="vs-checkbox-con vs-checkbox-primary" title="@lang('orders.Bulk Print')">
                                                <input type="checkbox" onchange="handleChange(this)" name="containers[]"
                                                    class="bulk-container" value="{{ $container->id }}">
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
                                            @if (!$container->awb)
                                                <span class="text-danger font-italic">Awb Number Required</span>
                                            @endif
                                            {{ $container->awb }}
                                        </td>
                                        <td>
                                            @if ($container->response == 0)
                                                <div class="btn btn-info">
                                                    New
                                                </div>
                                            @endif
                                            @if ($container->response != 0)
                                                <div class="btn btn-primary">
                                                    Registered
                                                </div>
                                            @endif
                                        </td>
                                        <td class="d-flex">
                                            <div class="btn-group">
                                                <div class="dropdown">
                                                    <button type="button" data-toggle="dropdown" aria-haspopup="true"
                                                        aria-expanded="false"
                                                        class="btn btn-success btn-sm dropdown-toggle waves-effect waves-light"
                                                        style="width:100px;">
                                                        @lang('user.Action')
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-right dropright">
                                                        <a href="{{ route('warehouse.usps-container.packages',$container) }}" class="dropdown-item w-100">
                                                            <i class="feather icon-box"></i> @lang('warehouse.actions.Packages')
                                                        </a>
                                                        @if ($container->response == 0)
                                                            <a href="{{ route('warehouse.usps_containers.edit', $container) }}"
                                                                class="dropdown-item w-100">
                                                                <i class="fa fa-edit"></i> @lang('warehouse.actions.Edit')
                                                            </a>

                                                            @if (count($container->orders) > 0)
                                                                <a href="{{ route('warehouse.usps_container.register', $container) }}"
                                                                    class="dropdown-item w-100" id="register_unit">
                                                                    <i class="feather icon-box"></i> Register Unit
                                                                </a>
                                                            @endif

                                                            <form
                                                                action="{{ route('warehouse.usps_containers.destroy', $container) }}"
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
                                                        @if ($container->response != 0)
                                                            <a href="{{ route('warehouse.usps_container.download', $container) }}"
                                                                class="dropdown-item w-100">
                                                                <i class="feather icon-box"></i> Get CN35
                                                            </a>
                                                            <a href="{{ route('warehouse.download.usps_manifest_excel', $container) }}"
                                                                class="dropdown-item w-100">
                                                                <i class="fa fa-cloud-download"></i> Download Manifest
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
@endsection
@section('js')
<script>
    $(document).ready(function() {
        $('#upload_manifest').click(function() {
            $('#loading').fadeIn();
        });
    });
</script>

<script>
    $('body').on('change', '#checkAll', function() {

        if ($('#checkAll').is(':checked')) {
            $('.bulk-container').prop('checked', true)
            document.getElementById("printBtnDiv").style.display = 'block';
        } else {
            $('.bulk-container').prop('checked', false)
            document.getElementById("printBtnDiv").style.display = 'none';
        }

    })
    $('body').on('click', '#assignAWB', function() {

        var containerIds = [];
        $.each($(".bulk-container:checked"), function() {
            containerIds.push($(this).val());

            // $(".result").append('HD-' + this.value + ',');
        });

        $('#bulk_sale_form #command').val('assign-awb');
        $('#bulk_sale_form #data').val(JSON.stringify(containerIds));
        $('#confirm').modal('show');
        // $('#bulk_sale_form').submit();

    })
</script>
@endsection
