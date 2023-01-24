<div>
    <div>
        @if ($orderStatus)
            <div class="row mb-3 col-12 alert alert-danger">
                <div class="">
                    {{ $orderStatus }}
                </div>
            </div>
        @endif
        <div class="row">
            <div class="form-group col-12 row">
                <label class="col-3 text-center" style="font-size: 30px;"> @lang('orders.print-label.Scan Package')</label>
                <input type="text" class="form-control col-8 w-100 text-center border border-primary"
                    style="height: 50px; font-size: 30px;" wire:model.debounce.500ms="tracking">
            </div>

        </div>
        <table class="table table-bordered">
            <tr>
                <th>Tracking No</th>
                <th>Client</th>
                <th>Dimensions</th>
                <th>Weight</th>
                <th>Reference</th>
                <th>Recpient</th>
                <th>Action</th>
            </tr>
            @foreach ($packagesRows as $key => $package)
                <tr id="{{ $key }}">
                    <td>
                        {{ $package['tracking_code'] }}
                    </td>
                    <td>
                        {{ $package['client'] }}
                    </td>
                    <td>
                        {{ $package['dimensions'] }}
                    </td>
                    <td>
                        {{ $package['kg'] . ' kg (' . $package['lbs'] }} lbs)
                    </td>
                    <td>
                        @if ($package['reference'])
                            {{ $package['reference'] }}
                        @endif
                    </td>
                    <td>
                        {{ $package['recpient'] }}
                    </td>

                    <td>

                        @if (!$error)
                            @if ($package['client'])
                                <a href="{{ route('warehouse.search_package.show', $package['reference']) }}"
                                    class="btn btn-success mr-2" onclick="addClass({{ $key }})"
                                    title="@lang('orders.import-excel.Download')">
                                    <i class="fa fa-search"></i> Find
                                </a>
                            @endif
                        @endif

                        <button class="btn btn-danger" role="button" tabindex="-1" type="button"
                            wire:click='removeRow({{ $key }})'>
                            @lang('orders.print-label.Remove')
                        </button>
                    </td>
                </tr>
            @endforeach
        </table>

        @if (count($packagesRows) == 50)
            <!-- Modal -->
            <div class="modal fade show d-block" id="removeModal" tabindex="-1" role="dialog"
                aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3 class="modal-title text-danger" id="exampleModalLabel"><b>STOP</b></h3>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                                onclick="removeCss()">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row justify-content-center">
                                <i class="feather icon-x-circle text-danger display-1"> </i>
                            </div>
                            <div class="row justify-content-center">
                                <p class="h3 text-danger" style="text-align: center !important;">You have reached your
                                    labels print limit</p>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" onclick="removeCss()"
                                data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif



        @include('layouts.livewire.loading')
    </div>

</div>
