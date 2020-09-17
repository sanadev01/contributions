<tr>
    <td>
        {{ $order->created_at->format('m/d/Y') }}
    </td>
    <td style="width: 200px;">
        @if( $order->warehouse_number)
            <span>
                <a href="#" title="Click to see Shipment">
                    WRH#: {{ $order->warehouse_number }}
                </a>
            </span>
        @endif
        @if( $order->isConsolidated() )
           <hr>
        @endif
        <span title="Consolidation Requested For Following Shipments">
            @foreach( $order->subOrders as $subOrder)
                <a href="#" class="mb-1 d-block">
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
        <select class="form-control {{ !auth()->user()->isAdmin() ? 'btn disabled' : ''  }}" @if (auth()->user()->isAdmin())  wire:change="$emit('updated-status',{{$order->id}},$event.target.value)" @else disabled="disabled"  @endif>
            <option value="{{ App\Models\Order::STATUS_ORDER }}" {{ $order->status == App\Models\Order::STATUS_ORDER ? 'selected': '' }}>STATUS_ORDER</option>
            <option value="{{ App\Models\Order::STATUS_CONSOLIDATOIN_REQUEST }}" {{ $order->status == App\Models\Order::STATUS_CONSOLIDATOIN_REQUEST ? 'selected': '' }}>STATUS_CONSOLIDATOIN_REQUEST</option>
            <option value="{{ App\Models\Order::STATUS_CONSOLIDATED }}" {{ $order->status == App\Models\Order::STATUS_CONSOLIDATED ? 'selected': '' }}>STATUS_CONSOLIDATED</option>
            <option value="{{ App\Models\Order::STATUS_PAYMENT_PENDING }}" {{ $order->status == App\Models\Order::STATUS_PAYMENT_PENDING ? 'selected': '' }}>STATUS_PAYMENT_PENDING</option>
            <option value="{{ App\Models\Order::STATUS_PAYMENT_DONE }}" {{ $order->status == App\Models\Order::STATUS_PAYMENT_DONE ? 'selected': '' }}>STATUS_PAYMENT_DONE</option>
            <option value="{{ App\Models\Order::STATUS_SHIPPED }}" {{ $order->status == App\Models\Order::STATUS_SHIPPED ? 'selected': '' }}>STATUS_SHIPPED</option>
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
                    <button onclick="mw = window.open('{{route('admin.orders.order-invoice.index',$order)}}','','width=768,height=768')" class="btn dropdown-item w-100" title="Show Order Details">
                        <i class="feather icon-list"></i> @lang('orders.actions.view-order')
                    </button>
                    
                    @if( $order->corrios_tracking_code)
                        <button class="btn dropdown-item w-100" title="@lang('orders.track-order')">
                            <i class="feather icon-truck"></i>@lang('orders.actions.track-order')
                        </button>
                    @endif

                    @can('update',  $order)
                        <a href="{{ route('admin.parcels.edit',$order) }}" class="dropdown-item btn" title="@lang('parcel.Edit Parcel')">
                            <i class="feather icon-edit"></i> @lang('parcel.Edit Parcel')
                        </a>
                    @endcan

                    @if( $order->isPaid() && auth()->user()->can('canPrintLable',$order))
                        <a href="#" class="btn dropdown-item w-100" title="@lang('orders.actions.label')">
                            <i class="feather icon-printer"></i>@lang('orders.actions.label')
                        </a>
                    @endif
                    
                   @can('updateOrder', $order)
                        <a href="{{ route('admin.orders.sender.index',$order) }}" class="btn dropdown-item w-100" title="@lang('orders.actions.update')">
                            <i class="feather icon-edit"></i>@lang('orders.actions.update')
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