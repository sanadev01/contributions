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
                <label class="col-2 text-right" style="font-size: 30px;"> Tracking Code</label>
                <input type="text" placeholder="Enter Tracking Number or order ID"
                    class="form-control col-8 w-100 text-center hd-search border border-primary"
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
                <th>Status</th>
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

                        @if ($package['status'] == App\Models\Order::STATUS_ORDER)
                            {{ 'ORDER' }}
                        @endif
                        @if ($package['status'] == App\Models\Order::STATUS_CANCEL)
                            {{ 'CANCEL' }}
                        @endif
                        @if ($package['status'] == App\Models\Order::STATUS_REJECTED)
                            {{ 'REJECTED' }}
                        @endif
                        @if ($package['status'] == App\Models\Order::STATUS_RELEASE)
                            {{ 'RELEASE' }}
                        @endif
                        @if ($package['status'] == App\Models\Order::STATUS_PAYMENT_PENDING)
                            {{ 'PAYMENT PENDING' }}
                        @endif
                        @if ($package['status'] == App\Models\Order::STATUS_PAYMENT_DONE)
                            {{ 'PAYMENT DONE' }}
                        @endif
                        @if ($package['status'] == App\Models\Order::STATUS_SHIPPED)
                            {{ 'SHIPPED' }}
                        @endif
                        @if ($package['status'] == App\Models\Order::STATUS_REFUND)
                            {{ 'REFUND' }}
                        @endif
                    </td>

                    <td>

                        @if (!$error)
                            @if ($package['client'])
                                <a href="{{ route('admin.tracking.show', $package['reference']) }}"
                                    class="btn btn-success mr-2" onclick="addClass({{ $key }})"
                                    title="More Details">
                                    <i class="fa fa-search"></i> More Details
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

        @include('layouts.livewire.loading')
    </div>

</div>
