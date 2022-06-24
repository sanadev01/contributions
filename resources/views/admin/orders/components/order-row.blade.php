<tr @if( $order->user->hasRole('retailer') &&  !$order->isPaid()) class="bg-danger-custom text-white" @endif>
    @if(\Request::route()->getName() != 'admin.reports.order.index'  && !$order->isTrashed())
        <td>
            <div class="vs-checkbox-con vs-checkbox-primary" style="justify-content: center; margin-left:19px;" title="@lang('orders.Bulk Print')">
                <input type="checkbox"  onchange='handleChange(this);' name="orders[]" class="bulk-orders"  value="{{$order->id}}">
                <span class="vs-checkbox vs-checkbox-sm">
                    <span class="vs-checkbox--check">
                        <i class="vs-icon feather icon-check"></i>
                    </span>
                </span>
                <span class="h3 mx-2 text-primary my-0 py-0"></span>
            </div>
        </td>
    @endif
    @admin
    <td id="userNameCol">
        <div class="media media-xs overflow-visible">
            <img class="corrioes-lable" src="{{asset('images/user-icon.png')}}" style="height: 40px; width: 40px" alt="">
        </div>
        <div class="media-body valign-middle" onclick="copyName(this)"  id="imageDecrptionTop" style="width:170px;">
            {{ $order->user->name }} - {{ $order->user->hasRole('wholesale') ? 'W' : 'R' }}
        </div>
        <div id="imageDecrption" onclick="copyCarrier(this)">
            <span>{{ str_limit(ucfirst($order->merchant), 30) }} ,  {{ ucfirst($order->tracking_id) }}</span>
        </div>
        {{-- <div style="padding-left: 45px !important">
          
        </div> --}}
    </td>
    @endadmin
    <td>
        @if(\Request::route()->getName() != 'admin.reports.order.index'  && !$order->isTrashed())
            {{-- <div class="vs-radio-con" wire:click="$emit('edit-order',{{$order->id}})" title="@lang('Edit Order')">
                <input type="radio" name="edit_order" class="edit-order" value="false">
                <span class="vs-radio vs-radio-sm">
                    <span class="vs-radio--border"></span>
                    <span class="vs-radio--circle"></span>
                </span>
            </div> --}}
            <a href="#" id="openEditModal" class="mb-0 " wire:click="$emit('edit-order',{{$order->id}})">
                {{ optional($order->order_date)->format('m/d/Y') }}
            </a>
        @endif
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
    {{-- <td>
        {{ str_limit(ucfirst($order->merchant), 30) }}
    </td> --}}
    {{-- <td>
        {{ ucfirst($order->tracking_id) }}
    </td> --}}
    <td>
        {{ ucfirst($order->customer_reference) }}
    </td>
    {{-- <td>
        {{ $order->carrierService() }}
    </td>
    @admin
    <td>
        {{ $order->carrierCost() }}
    </td>
    @endadmin --}}
    <td>
        {{ $order->corrios_tracking_code }}
        @if($order->hasSecondLabel())
            <hr>
            {{ $order->us_api_tracking_code }}
        @endif
    </td>
    <td>
        ${{ number_format($order->gross_total,2) }}
    </td>
    <td>
        <select style="" data-toggle="dropdown" class="form-control btn-width-md dropdown-toggle {{ !auth()->user()->isAdmin() ? 'btn disabled' : ''  }} {{ $order->getStatusClass() }}" @if (auth()->user()->isAdmin())  wire:change="$emit('updated-status',{{$order->id}},$event.target.value)" @else disabled="disabled"  @endif>
            <option class="bg-info" value="{{ App\Models\Order::STATUS_ORDER }}" {{ $order->status == App\Models\Order::STATUS_ORDER ? 'selected': '' }}>ORDER</option>
            {{-- <option class="bg-warning" value="{{ App\Models\Order::STATUS_NEEDS_PROCESSING }}" {{ $order->status == App\Models\Order::STATUS_NEEDS_PROCESSING ? 'selected': '' }}>NEEDS PROCESSING</option> --}}
            <option class="btn-cancelled" value="{{ App\Models\Order::STATUS_CANCEL }}" {{ $order->status == App\Models\Order::STATUS_CANCEL ? 'selected': '' }}>CANCELLED</option>
            <option class="btn-cancelled" value="{{ App\Models\Order::STATUS_REJECTED }}" {{ $order->status == App\Models\Order::STATUS_REJECTED ? 'selected': '' }}>REJECTED</option>
            <option class="bg-warning text-dark" value="{{ App\Models\Order::STATUS_RELEASE }}" {{ $order->status == App\Models\Order::STATUS_RELEASE ? 'selected': '' }}>RELEASED</option>
            <option class="bg-danger" value="{{ App\Models\Order::STATUS_PAYMENT_PENDING }}" {{ $order->status == App\Models\Order::STATUS_PAYMENT_PENDING ? 'selected': '' }}>PAYMENT_PENDING</option>
            <option class="bg-success" value="{{ App\Models\Order::STATUS_PAYMENT_DONE }}" {{ $order->status == App\Models\Order::STATUS_PAYMENT_DONE ? 'selected': '' }}>PAYMENT_DONE</option>
            <option class="bg-secondary" value="{{ App\Models\Order::STATUS_SHIPPED }}" {{ $order->status == App\Models\Order::STATUS_SHIPPED ? 'selected': '' }}>SHIPPED</option>
            @if($order->isPaid() || $order->isRefund() && !$order->isShipped())
                <option class="btn-refund" value="{{ App\Models\Order::STATUS_REFUND }}" {{ $order->status == App\Models\Order::STATUS_REFUND ? 'selected': '' }}>REFUND / CANCELLED</option>
            @endif

        </select>
    </td>
    <td style="zoom: 0.87">
        @if ( $order->is_consolidated )
            <span class="btn btn-sm btn-primary">
                Consolidated
            </span>
        @else
            <span class="btn btn-sm btn-primary">
                Non-Consolidated
            </span>
        @endif
    </td>
    <td class="font-large-1">
        @if( $order->isPaid() )
            <i class="fa fa-check-circle text-success"></i>
        @else
            <i class="fa fa-times-circle @if( $order->user->hasRole('retailer') &&  !$order->isPaid()) text-white @else text-danger @endif"></i>
        @endif
    </td>
    
    <td class="no-print" >
        <div class="btn-group">
            <div class="dropdown">
                <button type="button" class="btn btn-success btn-sm dropdown-toggle btn-width-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    @lang('orders.actions.actions')
                </button>
                <div class="dropdown-menu overlap-menu" aria-labelledby="dropdownMenuLink">

                    @user
                        @if( !$order->isPaid() && !$order->isNeedsProcessing() && $order->user->isActive())

                            @if ( optional($order)->getPaymentInvoice() )
                                <a  @if(Auth::user()->isActive()) href="{{ route('admin.payment-invoices.invoice.show',optional($order)->getPaymentInvoice()) }}" @else data-toggle="modal" data-target="#hd-modal" data-url="{{ route('admin.modals.user.suspended') }}" @endif  class="dropdown-item" title="Pay Order">
                                    <i class="feather icon-dollar-sign"></i> @lang('orders.actions.pay-order')
                                </a>
                            @else
                                <a @if(Auth::user()->isActive()) href="{{ route('admin.payment-invoices.orders.index',['order'=>$order]) }}" @else data-toggle="modal" data-target="#hd-modal" data-url="{{ route('admin.modals.user.suspended') }}" @endif  class="dropdown-item" title="Pay Order">
                                    <i class="feather icon-dollar-sign"></i> @lang('orders.actions.pay-order')
                                </a>
                            @endif
                            
                        @endif
                    @enduser
                        <button data-toggle="modal" data-target="#hd-modal" data-url="{{ route('admin.modals.order.invoice',$order) }}" class="dropdown-item" title="Show Order Details">
                            <i class="feather icon-list"></i> @lang('orders.actions.view-order')
                        </button>
                    @if( $order->corrios_tracking_code)
                        <button class="dropdown-item" data-target="#hd-modal" data-toggle="modal"  @if(Auth::user()->isActive()) data-modal-type="html" @else  data-url="{{ route('admin.modals.user.suspended') }}" @endif data-content='<p class="h4">{{ $order->corrios_tracking_code }}</p> <a href="https://www2.correios.com.br/sistemas/rastreamento/default.cfm" target="_blank">https://www2.correios.com.br/sistemas/rastreamento/default.cfm</a>' title="@lang('orders.track-order')">
                            <i class="feather icon-truck"></i>@lang('orders.actions.track-order')
                        </button>
                    @endif
                    @can('update',  $order)
                        <a @if(Auth::user()->isActive()) href="{{ route('admin.parcels.edit',$order) }}" @else data-toggle="modal" data-target="#hd-modal" data-url="{{ route('admin.modals.user.suspended') }}" @endif  class="dropdown-item" title="@lang('parcel.Edit Parcel')">
                            <i class="feather icon-edit"></i> @lang('parcel.Edit Parcel')
                        </a>
                    @endcan

                    @if( $order->isPaid() && auth()->user()->can('canPrintLable',$order) && !$order->isRefund() && $order->is_paid && Auth::user()->isActive() && !$order->isTrashed())
                        <a href="{{ route('admin.orders.label.index',$order) }}" class="dropdown-item" title="@lang('orders.actions.label')">
                            <i class="feather icon-printer"></i>@lang('orders.actions.label')
                        </a>
                        @if( $order->corrios_tracking_code && $order->recipient->country_id != \App\Models\Order::US && !$order->hasSecondLabel())
                            <a href="{{ route('admin.order.us-label.index',$order) }}" class="dropdown-item" title="@lang('orders.actions.label')">
                                <i class="feather icon-printer"></i>@lang('orders.actions.buy-us-label')
                            </a>
                        @endif
                        @if($order->hasSecondLabel() && !$order->isTrashed())
                            <a href="{{ route('admin.order.us-label.index',$order) }}" class="dropdown-item" title="@lang('orders.actions.label')">
                                <i class="feather icon-printer"></i>@if($order->usLabelService() == \App\Models\ShippingService::UPS_GROUND)@lang('orders.actions.print-ups-label') @elseif($order->usLabelService() == \App\Models\ShippingService::FEDEX_GROUND) @lang('orders.actions.print-fedex-label') @else @lang('orders.actions.print-usps-label') @endif
                            </a>
                            @if ($order->apiPickupResponse() != null)
                                <a href="{{ route('admin.order.ups-label.cancel.pickup', $order->id) }}" class="dropdown-item" title="@lang('orders.actions.label')">
                                    <i class="feather icon-trash"></i>@lang('orders.actions.cancel-ups-pickup')
                                </a>
                            @endif
                        @endif        
                    @endif
                   @can('updateOrder', $order)
                        <a  @if(Auth::user()->isActive()) href="{{ route('admin.orders.sender.index',$order) }}" @else data-toggle="modal" data-target="#hd-modal" data-url="{{ route('admin.modals.user.suspended') }}" @endif class="dropdown-item" title="@lang('orders.actions.update')">
                            <i class="feather icon-edit"></i>@lang('orders.actions.update')
                        </a>
                   @endcan
                   @can('copyOrder', $order)
                        <a @if(Auth::user()->isActive())  @else data-toggle="modal" data-target="#hd-modal" data-url="{{ route('admin.modals.user.suspended') }}"  @endif href="{{ route('admin.orders.duplicate',$order) }}" class="dropdown-item" title="@lang('orders.actions.duplicate-order')">
                            <i class="feather icon-copy"></i>@lang('orders.actions.duplicate-order')
                        </a>
                   @endcan
                    @if( Auth::user()->isActive() && !$order->isTrashed())
                    <form action="{{ route('admin.orders.destroy',$order->id) }}" method="post" onsubmit="return confirmDelete()">
                        @csrf
                        @method('DELETE')
                        <button class="dropdown-item text-danger" title="Delete Record">
                            <i class="feather icon-trash"></i>@if( $order->user->hasRole('retailer') &&  !$order->isPaid()) @lang('orders.Remove') @else @lang('orders.Delete') @endif  
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </td>
</tr>
