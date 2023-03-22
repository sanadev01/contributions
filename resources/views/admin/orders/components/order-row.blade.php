<tr @if ($order->user->hasRole('retailer') && !$order->isPaid()) class="bg-danger-custom" @endif>
    @if (\Request::route()->getName() != 'admin.reports.order.index' &&
    !$order->isTrashed() &&
    \Request::route()->getName() != 'admin.reports.order.index' &&
    Request::path() != 'livewire/message/reports.order-report-table')
        <td>
            <div class="vs-checkbox-con vs-checkbox-primary" title="Select">
                <input type="checkbox" onchange='handleChange(this);' name="orders[]" class="bulk-orders"
                    value="{{ $order->id }}">
                <span class="vs-checkbox vs-checkbox-sm">
                    <span class="vs-checkbox--check">
                        <i class="vs-icon feather icon-check"></i>
                    </span>
                </span>
                
            </div>
        </td>
    @endif
    @admin
        <td id="userNameCol">
            <div class="media media-xs overflow-visible">
                <img class="corrioes-lable" src="{{ asset('images/tracking/' . $order->carrierService() . '.png') }}"
                    title="{{ $order->carrierService() }}"style="height: 30px; width: 30px; vertical-align:middle;"
                    alt="">
            </div>
            <div class="media-body valign-middle" id="imageDecrptionTop" style="width:100%; font-size:15px !important">
                <a href="#" title="Click to see Shipment" data-toggle="modal" data-target="#hd-modal"
                    data-url="{{ route('admin.modals.parcel.shipment-info', $order) }}">
                    {{ $order->user->name }} - {{ $order->user->hasRole('wholesale') ? 'W' : 'R' }}
                </a>
            </div>

            <div id="imageDecrption" style="font-size: 14px !important;">
                
                @if ($order->is_consolidated)
                    <span>
                        Consolidated
                    </span>
                @else
                    <span>
                        Non-Consolidated
                    </span>
                @endif
                
            </div>
           
        </td>
    @endadmin
    <td>
       
        <a href="#" id="openEditModal" class="mb-0 " wire:click="$emit('edit-order',{{ $order->id }})"
            title="Click to edit">
            {{ optional($order->order_date)->format('m/d/Y') }}
        </a>
    </td>
    <td class="order-id">
        @if ($order->isArrivedAtWarehouse())
            <i class="fa fa-star text-success p-1"></i>
        @endif
        <span>
            <a href="#" title="Click to see Shipment" data-toggle="modal" data-target="#hd-modal"
                data-url="{{ route('admin.modals.parcel.shipment-info', $order) }}">
                {{ $order->warehouse_number }}
            </a>
        </span>
        <td>
            {{ ucfirst($order->tracking_id) }}
        </td>
        <td>
            {{ ucfirst($order->customer_reference) }}
        </td>
        @if ($order->isConsolidated())
            <hr>
        @endif
        <span title="Consolidation Requested For Following Shipments">
            @foreach ($order->subOrders as $subOrder)
                <a href="#" class="mb-1 d-block" data-toggle="modal" data-target="#hd-modal"
                    data-url="{{ route('admin.modals.parcel.shipment-info', $subOrder) }}">
                    {{ $subOrder->warehouse_number }}
                </a>
            @endforeach
        </span>
    </td>
    
    <td>
        {{ $order->corrios_tracking_code }}
        @if ($order->hasSecondLabel())
            <hr>
            {{ $order->us_api_tracking_code }}
        @endif
    </td>
    <td>
        <span class="col-1">${{ number_format($order->gross_total, 2) }}</span>
    </td>

    <td width="100px">
        
        <div class="dropdown">
            <button id="status-btn" title="status" type="button"
                @if (\Request::route()->getName() == 'admin.trash-orders.index' ||
                    \Request::route()->getName() == 'admin.reports.order.index') disabled @endif
                class="btn d-flex justify-content-center{{ !auth()->user()->isAdmin()? 'btn disabled': '' }} {{ $order->getStatusClass() }}"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                {{ $order->getStatus() }} <i class="fa fa-caret-down ml-1 mr-0"></i>
            </button>
            <div class="dropdown-menu overlap-menu overlap-menu-order" aria-labelledby="dropdownMenuLink">
                <button id="status-btn" wire:click="$emit('updated-status',{{ $order->id }},$event.target.value)"
                    value="{{ App\Models\Order::STATUS_ORDER }}" class="dropdown-item" title="Show Order Details">
                    <i class="feather icon-circle"></i> ORDER
                </button>
                <button class="dropdown-item"
                    wire:click="$emit('updated-status',{{ $order->id }},$event.target.value)"
                    value="{{ App\Models\Order::STATUS_CANCEL }}">
                    <i class="feather icon-circle"></i>CANCELLED
                </button>
                <button class="dropdown-item" value="{{ App\Models\Order::STATUS_REJECTED }}"
                    wire:click="$emit('updated-status',{{ $order->id }},$event.target.value)">
                    <i class="feather icon-circle"></i>REJECTED
                </button>
                <button class="dropdown-item" value="{{ App\Models\Order::STATUS_RELEASE }}"
                    wire:click="$emit('updated-status',{{ $order->id }},$event.target.value)">
                    <i class="feather icon-circle"></i>RELEASED
                </button>
                <button class="dropdown-item" value="{{ App\Models\Order::STATUS_PAYMENT_PENDING }}"
                    wire:click="$emit('updated-status',{{ $order->id }},$event.target.value)">
                    <i class="feather icon-circle"></i>PAYMENT_PENDING
                </button>
                <button class="dropdown-item" value="{{ App\Models\Order::STATUS_PAYMENT_DONE }}"
                    wire:click="$emit('updated-status',{{ $order->id }},$event.target.value)">
                    <i class="feather icon-circle"></i>PAYMENT_DONE
                </button>
                <button class="dropdown-item" value="{{ App\Models\Order::STATUS_SHIPPED }}"
                    wire:click="$emit('updated-status',{{ $order->id }},$event.target.value)">
                    <i class="feather icon-circle"></i>SHIPPED
                </button>
                <button class="dropdown-item" value="{{ App\Models\Order::STATUS_REFUND }}"
                    wire:click="$emit('updated-status',{{ $order->id }},$event.target.value)">
                    <i class="feather icon-circle"></i>REFUND / CANCELLED
                </button>
            </div>
        </div>
    

    </td>

    <td class="font-large-1">
        @if ($order->isPaid())
            <i class="fa fa-check-circle text-success" title="Payment Done"></i>
        @else
            <i class="fa fa-times-circle @if ($order->user->hasRole('retailer') && !$order->isPaid()) text-white @else text-danger @endif"
                title="Payment Pending"></i>
        @endif
    </td>

    @if (\Request::route()->getName() != 'admin.reports.order.index')
        <td class="no-print">
            <div class="btn-group d-flex justify-content-center">
                <div class="dropdown">
                    <button title="Action" type="button" class="btn btn-primary btn-sm" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        <i class="feather icon-edit-1 mr-0"></i>
                    </button>
                    <div class="dropdown-menu overlap-menu overlap-menu-order" aria-labelledby="dropdownMenuLink">

                        @user
                            @if (!$order->isPaid() && !$order->isNeedsProcessing() && $order->user->isActive())
                                @if (optional($order)->getPaymentInvoice())
                                    <a @if (Auth::user()->isActive()) href="{{ route('admin.payment-invoices.invoice.show', optional($order)->getPaymentInvoice()) }}" @else data-toggle="modal" data-target="#hd-modal" data-url="{{ route('admin.modals.user.suspended') }}" @endif
                                        class="dropdown-item" title="Pay Order">
                                        <i class="feather icon-dollar-sign"></i> @lang('orders.actions.pay-order')
                                    </a>
                                @else
                                    <a @if (Auth::user()->isActive()) href="{{ route('admin.payment-invoices.orders.index', ['order' => $order]) }}" @else data-toggle="modal" data-target="#hd-modal" data-url="{{ route('admin.modals.user.suspended') }}" @endif
                                        class="dropdown-item" title="Pay Order">
                                        <i class="feather icon-dollar-sign"></i> @lang('orders.actions.pay-order')
                                    </a>
                                @endif
                            @endif
                        @enduser
                        <button data-toggle="modal" data-target="#hd-modal"
                            data-url="{{ route('admin.modals.order.invoice', $order) }}" class="dropdown-item"
                            title="Show Order Details">
                            <i class="feather icon-list"></i> @lang('orders.actions.view-order')
                        </button>
                        @if ($order->corrios_tracking_code)
                            <button class="dropdown-item" data-target="#hd-modal" data-toggle="modal"
                                @if (Auth::user()->isActive()) data-modal-type="html" @else  data-url="{{ route('admin.modals.user.suspended') }}" @endif
                                data-content='<p class="h4">{{ $order->corrios_tracking_code }}</p> <a href="https://www2.correios.com.br/sistemas/rastreamento/default.cfm" target="_blank">https://www2.correios.com.br/sistemas/rastreamento/default.cfm</a>'
                                title="@lang('orders.track-order')">
                                <i class="feather icon-truck"></i>@lang('orders.actions.track-order')
                            </button>
                        @endif
                        @can('update', $order)
                            <a @if (Auth::user()->isActive()) href="{{ route('admin.parcels.edit', $order) }}" @else data-toggle="modal" data-target="#hd-modal" data-url="{{ route('admin.modals.user.suspended') }}" @endif
                                class="dropdown-item" title="@lang('parcel.Edit Parcel')">
                                <i class="feather icon-edit"></i> @lang('parcel.Edit Parcel')
                            </a>
                        @endcan

                        @if ($order->isPaid() &&
                            auth()->user()->can('canPrintLable', $order) &&
                            !$order->isRefund() &&
                            $order->is_paid &&
                            Auth::user()->isActive() &&
                            !$order->isTrashed())
                            <a href="{{ route('admin.orders.label.index', $order) }}" class="dropdown-item"
                                title="@lang('orders.actions.label')">
                                <i class="feather icon-printer"></i>@lang('orders.actions.label')
                            </a>
                            @if($order->carrierService() == "Global eParcel" && !$order->isRefund() && !$order->isShipped())
                                <a href="{{ route('admin.order.label.cancel',$order) }}" class="dropdown-item" title="@lang('orders.actions.cancel')">
                                    <i class="feather icon-x-square"></i>@lang('orders.actions.cancel')
                                </a>
                            @endif
                            @if ($order->corrios_tracking_code &&
                                 $order->recipient &&
                                $order->recipient->country_id != \App\Models\Order::US && (
                                !$order->hasSecondLabel() || $order->shippingService->isGDEService()) && !$order->isRefund())
                                <a href="{{ route('admin.order.us-label.index', $order) }}" class="dropdown-item"
                                    title="@lang('orders.actions.label')">
                                    <i class="feather icon-printer"></i>@lang('orders.actions.buy-us-label')
                                </a>
                            @endif
                            @if ($order->hasSecondLabel() && !$order->isTrashed())
                                <a href="{{ route('admin.order.us-label.index', $order) }}" class="dropdown-item"
                                    title="@lang('orders.actions.label')">
                                    <i class="feather icon-printer"></i>
                                    @if ($order->usLabelService() == \App\Models\ShippingService::UPS_GROUND)
                                        @lang('orders.actions.print-ups-label')
                                    @elseif($order->usLabelService() == \App\Models\ShippingService::FEDEX_GROUND)
                                        @lang('orders.actions.print-fedex-label')
                                    @else
                                        @lang('orders.actions.print-usps-label')
                                    @endif
                                </a>
                                @if ($order->apiPickupResponse() != null)
                                    <a href="{{ route('admin.order.ups-label.cancel.pickup', $order->id) }}"
                                        class="dropdown-item" title="@lang('orders.actions.label')">
                                        <i class="feather icon-trash"></i>@lang('orders.actions.cancel-ups-pickup')
                                    </a>
                                @endif
                            @endif
                        @endif
                        @can('updateOrder', $order)
                            <a @if (Auth::user()->isActive()) href="{{ route('admin.orders.sender.index', $order) }}" @else data-toggle="modal" data-target="#hd-modal" data-url="{{ route('admin.modals.user.suspended') }}" @endif
                                class="dropdown-item" title="@lang('orders.actions.update')">
                                <i class="feather icon-edit"></i>@lang('orders.actions.update')
                            </a>
                        @endcan
                        @can('copyOrder', $order)
                            <a @if (Auth::user()->isActive()) @else data-toggle="modal" data-target="#hd-modal" data-url="{{ route('admin.modals.user.suspended') }}" @endif
                                href="{{ route('admin.orders.duplicate', $order) }}" class="dropdown-item"
                                title="@lang('orders.actions.duplicate-order')">
                                <i class="feather icon-copy"></i>@lang('orders.actions.duplicate-order')
                            </a>
                        @endcan
                        @if (Auth::user()->isActive() && !$order->isTrashed())
                            <form action="{{ route('admin.orders.destroy', $order->id) }}" method="post"
                                onsubmit="return confirmDelete()">
                                @csrf
                                @method('DELETE')
                                <button class="dropdown-item text-danger" title="Delete Record">
                                    <i class="feather icon-trash"></i>
                                    @if ($order->user->hasRole('retailer') && !$order->isPaid())
                                        @lang('orders.Remove')
                                    @else
                                        @lang('orders.Delete')
                                    @endif
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </td>
    @endif
</tr>
