<div>
    <div class="p-2">
        <div class="row text-left">
            <div class="ml-auto mr-3 mb-2">
                <h1>Total Value: <span class="text-primary">$ {{ $totalValue }}</span></h1>
            </div>
        </div>
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
                    <input type="hidden" name="pick" value="0">
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
                            <a href="#" wire:click.prevent="sortBy('id')">Sale Order Number</a> <i>  </i>
                        </th>
                        @admin
                        <th>User Name</th>
                        @endadmin
                        <th>Weight</th>
                        <th>Unit</th>
                        <th>@lang('orders.status')</th>
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
                        </th>
                        <th>
                        </th>
                        <th>
                            <select class="form-control" wire:model="status">
                                <option value="">All</option>
                                <option value="{{ App\Models\Order::STATUS_INVENTORY_PENDING }}">PENDING</option>
                                <option value="{{ App\Models\Order::STATUS_INVENTORY_IN_PROGRESS }}">IN_PROGRESS</option>
                                <option value="{{ App\Models\Order::STATUS_INVENTORY_CANCELLED }}">CANCELLED</option>
                                {{-- <option value="{{ App\Models\Order::STATUS_INVENTORY_REJECTED }}">REJECTED</option> --}}
                                <option value="{{ App\Models\Order::STATUS_INVENTORY_FULFILLED }}">FULFILLED</option>
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
                        <td>{{ $order->weight }}</td>
                        <td>{{ $order->measurement_unit }}</td>
                        <td>
                            <select style="min-width:150px;" class="form-control {{ !auth()->user()->isAdmin() ? 'btn disabled' : ''  }} {{ $order->getStatusClass() }}" @if (auth()->user()->isAdmin())  wire:change="$emit('updated-status',{{$order->id}},$event.target.value)" @else disabled="disabled"  @endif>
                                <option class="bg-info" value="{{ App\Models\Order::STATUS_INVENTORY_PENDING }}" {{ $order->status == App\Models\Order::STATUS_INVENTORY_PENDING ? 'selected': '' }}>Pending</option>
                                <option class="bg-warning text-dark" value="{{ App\Models\Order::STATUS_INVENTORY_IN_PROGRESS }}" {{ $order->status == App\Models\Order::STATUS_INVENTORY_IN_PROGRESS ? 'selected': '' }}>In Progress</option>
                                <option class="btn-danger" value="{{ App\Models\Order::STATUS_INVENTORY_CANCELLED }}" {{ $order->status == App\Models\Order::STATUS_INVENTORY_CANCELLED ? 'selected': '' }}>CANCELLED</option>
                                {{-- <option class="btn-danger" value="{{ App\Models\Order::STATUS_INVENTORY_REJECTED }}" {{ $order->status == App\Models\Order::STATUS_INVENTORY_REJECTED ? 'selected': '' }}>REJECTED</option> --}}
                                <option class="bg-success" value="{{ App\Models\Order::STATUS_INVENTORY_FULFILLED }}" {{ $order->status == App\Models\Order::STATUS_INVENTORY_FULFILLED ? 'selected': '' }}>Fulfilled</option>
                            </select>
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

@push('lvjs-stack')
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            
            @this.on('updated-status',function(orderId,status){
                $.post('{{route("api.order.status.update")}}',{
                    order_id: orderId,
                    status : status
                })
                .then(function(response){
                    if ( response.success ){
                        @this.call('render')
                    }else{
                        toastr.error(response.message)
                    }
                }).catch(function(data){
                    toastr.error(response.message)
                })
            })

        });
    </script>
@endpush