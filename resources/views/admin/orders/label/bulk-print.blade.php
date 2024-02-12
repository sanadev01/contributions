@extends('layouts.master')

@section('page')
<div class="card">
    <div class="card-header">
        <h4 class="card-title" id="basic-layout-form"></h4>

        <div class="col-7 d-flex justify-content-end">
            <form action="{{ route('admin.label.scan.store') }}" method="post">
                @csrf
                @foreach ($orders as $order)
                <input type="hidden" name="order[]" value="{{ $order->id }}">
                @endforeach
                <button type="submit" class="btn btn-success mr-2" title="@lang('orders.import-excel.Download')">
                    <i class="feather icon-download"></i> @lang('orders.import-excel.Download') All
                </button>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-primary pull-right">
                    @lang('shipping-rates.Return to List')
                </a>
            </form>
        </div>
    </div>
    <hr>
    <div class="card-content collapse show">
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead>
                    <th>
                        <a href="#" wire:click.prevent="sortBy('id')">@lang('orders.order-id')</a> <i> </i>
                    </th>
                    @admin
                    <th>User Name</th>
                    @endadmin
                    <th>Loja/Cliente</th>
                    <th>Carrier Tracking</th>
                    <th>Referência do Cliente</th>
                    <th>Tracking Code</th>
                    <th>
                        Label Data
                    </th>
                </thead>
                <tbody>
                    @forelse ($orders as $order)
                    @if( $order->isPaid() && auth()->user()->can('canPrintLable',$order))
                    <tr>

                        <td style="width: 200px;">
                            @if ( $order->is_arrived_at_warehouse )
                            <i class="fa fa-star text-success p-1"></i>
                            @endif
                            @if( $order->warehouse_number)
                            <span>
                                <a href="#" title="Click to see Shipment" data-toggle="modal" data-target="#hd-modal" data-url="{{ route('admin.modals.parcel.shipment-info',$order) }}">
                                    {{ $order->warehouse_number }}
                                </a>
                            </span>
                            @endif
                            @if( $order->isConsolidated() )
                            <hr>
                            @endif
                            <span title="Consolidation Requested For Following Shipments">
                                @foreach( $order->subOrders as $subOrder)
                                <a href="#" class="mb-1 d-block" data-toggle="modal" data-target="#hd-modal" data-url="{{ route('admin.modals.parcel.shipment-info',$subOrder) }}">
                                    WHR#: {{ $subOrder->warehouse_number }}
                                </a>
                                @endforeach
                            </span>
                        </td>
                        @admin
                        <td>
                            {{ $order->user->name }} - {{ $order->user->hasRole('wholesale') ? 'W' : 'R' }}
                        </td>
                        @endadmin
                        <td>
                            {{ ucfirst($order->merchant) }}
                        </td>
                        <td>
                            {{ ucfirst($order->tracking_id) }}
                        </td>
                        <td>
                            {{ ucfirst($order->customer_reference) }}
                        </td>
                        <td>
                            {{ $order->corrios_tracking_code }}
                        </td>
                        <td id="row_{{$order->id}}" class="label-area" onclick="loadLabel({{$order->id}},'#row_{{$order->id}}',false)">
                            <div class="d-flex justify-content-center align-items-center h4 flex-column">
                                <p><i class="fa fa-spinner fa-spin"></i></p>
                                <p class="mt-1">Loading Label...</p>
                            </div>
                        </td>
                    </tr>
                    @endif
                    @empty
                    <tr>
                        <td colspan="7" class="text-center danger h3">
                            No Order Selected
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="modal fade" id="confirm" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">

                <h4 class="modal-title">Confirm!</h4>
            </div>
            <div class="modal-body">
                <p>
                <h5>@lang('orders.update-label')</h5>
                </p>
            </div>
            <div class="modal-footer">
                @if (isset($order))
                <button type="button" class="btn btn-primary" onclick="updateLabel({{$order->id}},'#row_{{$order->id}}')">Yes</button>
                @endif
                <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')

<script>
    function loadLabel(orderId, referenceId, updateLabel) {
        setTimeout(function() {
            var url = '{{ route("admin.orders.label.store","__order__") }}';
            $.post(url.replace("__order__", orderId), {
                    update_label: updateLabel,
                    buttons_only: true
                })
                .done(function(response) {
                    window.labelLoader = $(referenceId).html();
                    $(referenceId).html(response)
                })
                .fail(function(error) {
                    $(referenceId).text(error.message);
                })
        }, 2000)
    }

    function reloadLabel(orderId, referenceId) {
        $(referenceId).html(window.labelLoader);
        loadLabel(orderId, referenceId, false);
    }

    function updateLabel(orderId, referenceId) {
        $(referenceId).html(window.labelLoader);
        console.log('updating')
        loadLabel(orderId, referenceId, true);
    }

    $(document).ready(function() {
        setTimeout(() => {
            $('.label-area').each(function() {
                $(this).click();
            })
        }, 200)
    })
</script>

@endsection