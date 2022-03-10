<div>
    <div class="p-2">
        <div class="row mb-2 no-print">
            <div class="col-1">
                <select class="form-control" wire:model="pageSize">
                    <option value="1">1</option>
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="20">20</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="300">300</option>
                </select>
            </div>
            <div class="col-11 text-right">
                <form action="{{ route('admin.inventory.orders.export') }}" method="GET" target="_blank">
                    @csrf
                    <label>Start Date</label>
                    <input type="date" name="start_date" class="from-control col-2">
    
                    <label>End Date</label>
                    <input type="date" name="end_date" class="from-control col-2">
    
                    <button class="btn btn-success" title="@lang('orders.import-excel.Download')">
                        @lang('orders.Download Orders') <i class="fa fa-arrow-down"></i>
                    </button>
                </form>
            </div>
        </div>
        <div class="table-responsive order-table">
            <table class="table mb-0 table-responsive-md" id="order-table">
                <thead>
                    <tr>
                        <th>
                            <span class="mr-4"></span>
                            <a href="#" wire:click.prevent="sortBy('created_at')">@lang('orders.date')</a>
                        </th>
                        <th>
                            <a href="#" wire:click.prevent="sortBy('id')">@lang('orders.order-id')</a> <i>  </i>
                        </th>
                        @admin
                        <th>User Name</th>
                        @endadmin
                        <th>Loja/Cliente</th>
                        <th>Carrier Tracking</th>
                        <th>Referência do Cliente</th>
                        <th>Tracking Code</th>
                        <th><a href="#" wire:click.prevent="sortBy('gross_total')">@lang('orders.amount')</a></th>
                        <th>@lang('orders.status')</th>
                        <th>@lang('orders.payment-status')</th>
                        <th class="no-print">@lang('orders.actions.actions')</th>
                    </tr>
                    <tr class="no-print">
                        <th>
                            <input type="search" class="form-control col-md-9 ml-5" wire:model.debounce.1000ms="date">
                        </th>
                        <th>
                            <input type="search" class="form-control" wire:model.debounce.1000ms="whr_number">
                        </th>
                        @admin
                        <th>
                            <input type="search" class="form-control" wire:model.debounce.1000ms="name">
                        </th>
                        @endadmin
                        <th>
                            <input type="search" class="form-control" wire:model.debounce.1000ms="merchant">
                        </th>
                        <th>
                            <input type="search" class="form-control" wire:model.debounce.1000ms="tracking_id">
                        </th>
                        <th>
                            <input type="search" class="form-control" wire:model.debounce.1000ms="customer_reference">
                        </th>
                        <th>
                            <input type="search" class="form-control" wire:model.debounce.1000ms="tracking_code">
                        </th>
                        <th>
                            <input type="search" class="form-control" wire:model.debounce.1000ms="amount">
                        </th>
                        <th>
                            <select class="form-control" wire:model="status">
                                <option value="">All</option>
                                <option value="{{ App\Models\Order::STATUS_ORDER }}">ORDER</option>
                                <option value="{{ App\Models\Order::STATUS_CANCEL }}">CANCELLED</option>
                                <option value="{{ App\Models\Order::STATUS_REJECTED }}">REJECTED</option>
                                <option value="{{ App\Models\Order::STATUS_RELEASE }}">RELEASED</option>
                                <option value="{{ App\Models\Order::STATUS_PAYMENT_PENDING }}">PAYMENT_PENDING</option>
                                <option value="{{ App\Models\Order::STATUS_PAYMENT_DONE }}">PAYMENT_DONE</option>
                                <option value="{{ App\Models\Order::STATUS_SHIPPED }}">SHIPPED</option>
                            </select>
                        </th>
                        <th>
                            <select class="form-control" wire:model="paymentStatus">
                                <option value="">All</option>
                                <option value="paid">Paid</option>
                                <option value="unpaid">Unpaid</option>
                            </select>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr>
                        <td>{{ $order->created_at->format('d/m/Y') }}</td>
                        <td>
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
                        </td>
                        @admin
                        <td>{{ $order->user->name }} - {{ $order->user->hasRole('wholesale') ? 'W' : 'R' }}</td>
                        @endadmin
                        <td>{{ ucfirst($order->merchant) }}</td>
                        <td>{{ ucfirst($order->tracking_id) }}</td>
                        <td>{{ ucfirst($order->customer_reference) }}</td>
                        <td>{{ $order->corrios_tracking_code }}</td>
                        <td>${{ number_format($order->gross_total,2) }}</td>
                        <td>
                            <select style="min-width:150px;" class="form-control {{ $order->getStatusClass() }} disabled">
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
                        <td class="font-large-1">
                            @if( $order->isPaid() )
                                <i class="feather icon-check text-success"></i>
                            @else
                                <i class="feather icon-x  @if( $order->user->hasRole('retailer') &&  !$order->isPaid()) text-white @else text-danger @endif"></i>
                            @endif
                        </td>
                        <td>
                            <button data-toggle="modal" data-target="#hd-modal" data-url="{{ route('admin.modals.inventory.order.products',$order) }}" class="btn btn-primary">
                                <i class="feather icon-list"></i> @lang('orders.actions.view-products')
                            </button>
                        </td>
                    </tr>
                    @empty
                        <x-tables.no-record colspan="12"></x-tables.no-record>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="d-flex justify-content-end my-2 pb-4 mx-2">
        {{ $orders->links() }}
    </div>
    @include('layouts.livewire.loading')
</div>
