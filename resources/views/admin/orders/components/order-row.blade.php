<tr>
    <td>
        <div class="vs-checkbox-con vs-checkbox-primary" >
            <input type="checkbox" name="orders[]" class="bulk-orders" value="{{$order->id}}">
            <span class="vs-checkbox vs-checkbox-lg">
                <span class="vs-checkbox--check">
                    <i class="vs-icon feather icon-check"></i>
                </span>
            </span>
            <span class="h3 mx-2 text-primary my-0 py-0"></span>
        </div>
    </td>
    <td class="d-flex justify-content-between align-items-center">
        <div class="vs-radio-con" wire:click="$emit('edit-order',{{$order->id}})">
            <input type="radio" name="edit_order" class="edit-order" value="false">
            <span class="vs-radio vs-radio-lg">
                <span class="vs-radio--border"></span>
                <span class="vs-radio--circle"></span>
            </span>
        </div>
        {{ optional($order->order_date)->format('m/d/Y') }}
    </td>
    <td style="width: 200px;">
        @if ( $order->isArrivedAtWarehouse() )
            <i class="fa fa-star text-success p-1"></i>
        @endif
        @if( $order->warehouse_number)
            <span>
                <a href="#" title="Click to see Shipment" data-toggle="modal" data-target="#hd-modal" data-url="{{ route('admin.modals.parcel.shipment-info',$order) }}">
                    WRH#: {{ $order->warehouse_number }}
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
    <td>
        ${{ number_format($order->gross_total,2) }}
    </td>
    <td>
        <select class="form-control {{ !auth()->user()->isAdmin() ? 'btn disabled' : ''  }} {{ $order->getStatusClass() }}" @if (auth()->user()->isAdmin())  wire:change="$emit('updated-status',{{$order->id}},$event.target.value)" @else disabled="disabled"  @endif>
            <option value="{{ App\Models\Order::STATUS_ORDER }}" {{ $order->status == App\Models\Order::STATUS_ORDER ? 'selected': '' }}>ORDER</option>
            <option value="{{ App\Models\Order::STATUS_PAYMENT_PENDING }}" {{ $order->status == App\Models\Order::STATUS_PAYMENT_PENDING ? 'selected': '' }}>PAYMENT_PENDING</option>
            <option value="{{ App\Models\Order::STATUS_PAYMENT_DONE }}" {{ $order->status == App\Models\Order::STATUS_PAYMENT_DONE ? 'selected': '' }}>PAYMENT_DONE</option>
            <option value="{{ App\Models\Order::STATUS_SHIPPED }}" {{ $order->status == App\Models\Order::STATUS_SHIPPED ? 'selected': '' }}>SHIPPED</option>
        </select>
    </td>
    <td style="zoom: 0.8">
        @if ( $order->is_consolidated )
            <span class="btn btn-primary">
                Consolidated
            </span>
        @else
            <span class="btn btn-primary">
                Non-Consolidated
            </span>
        @endif
    </td>
    <td class="font-large-1">
        @if( $order->isPaid() )
            <i class="feather icon-check text-success"></i>
        @else
            <i class="feather icon-x text-danger"></i>
        @endif
    </td>
    <td class="d-flex no-print" >
        <div class="btn-group">
            <div class="dropdown">
                <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    @lang('orders.actions.actions')
                </button>
                <div class="dropdown-menu dropdown-menu-right dropright">

                    @user
                        @if( !$order->isPaid() )

                            @if ( optional($order)->getPaymentInvoice() )
                                <a href="{{ route('admin.payment-invoices.invoice.show',optional($order)->getPaymentInvoice()) }}" class="btn w-100 dropdown-item" title="Pay Order">
                                    <i class="feather icon-dollar-sign"></i> @lang('orders.actions.pay-order')
                                </a>
                            @else
                                <a href="{{ route('admin.payment-invoices.orders.index',['order'=>$order]) }}" class="btn w-100 dropdown-item" title="Pay Order">
                                    <i class="feather icon-dollar-sign"></i> @lang('orders.actions.pay-order')
                                </a>
                            @endif
                            
                        @endif
                    @enduser
                    <button data-toggle="modal" data-target="#hd-modal" data-url="{{ route('admin.modals.order.invoice',$order) }}" class="btn dropdown-item w-100" title="Show Order Details">
                        <i class="feather icon-list"></i> @lang('orders.actions.view-order')
                    </button>
                    
                    @if( $order->corrios_tracking_code)
                        <button class="btn dropdown-item w-100" data-target="#hd-modal" data-toggle="modal" data-modal-type="html" data-content='<p class="h4">{{ $order->corrios_tracking_code }}</p> <a href="https://www2.correios.com.br/sistemas/rastreamento/default.cfm" target="_blank">https://www2.correios.com.br/sistemas/rastreamento/default.cfm</a>' title="@lang('orders.track-order')">
                            <i class="feather icon-truck"></i>@lang('orders.actions.track-order')
                        </button>
                    @endif

                    @can('update',  $order)
                        <a href="{{ route('admin.parcels.edit',$order) }}" class="dropdown-item btn" title="@lang('parcel.Edit Parcel')">
                            <i class="feather icon-edit"></i> @lang('parcel.Edit Parcel')
                        </a>
                    @endcan

                    @if( $order->isPaid() && auth()->user()->can('canPrintLable',$order))
                        <a href="{{ route('admin.orders.label.index',$order) }}" class="btn dropdown-item w-100" title="@lang('orders.actions.label')">
                            <i class="feather icon-printer"></i>@lang('orders.actions.label')
                        </a>
                    @endif
                    
                   @can('updateOrder', $order)
                        <a href="{{ route('admin.orders.sender.index',$order) }}" class="btn dropdown-item w-100" title="@lang('orders.actions.update')">
                            <i class="feather icon-edit"></i>@lang('orders.actions.update')
                        </a>
                   @endcan
                   @can('updateOrder', $order)
                        <a href="{{ route('admin.orders.duplicate',$order) }}" class="btn dropdown-item w-100" title="@lang('orders.actions.duplicate-order')">
                            <i class="feather icon-copy"></i>@lang('orders.actions.duplicate-order')
                        </a>
                   @endcan
                    
                    <form action="{{ route('admin.orders.destroy',$order->id) }}" method="post" onsubmit="return confirmDelete()">
                        @csrf
                        @method('DELETE')
                        <button class="btn dropdown-item w-100 text-danger" title="Delete Record">
                            <i class="feather icon-trash"></i> Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </td>
</tr>