<div class="p-2" >
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
        
    </div>
    <div class="table-responsive order-table">
        <table class="table mb-0 table-responsive-md" id="order-table">
            <thead>
                <tr>
                    <th>
                        <a href="#" wire:click.prevent="sortBy('created_at')">@lang('orders.date')</a>
                    </th>
                    @admin
                    <th>User Name</th>
                    @endadmin
                    <th>Amazon Order Id</th>
                    <th>Ship Service Level</th>
                    <th><a href="#" wire:click.prevent="sortBy('order_total')">@lang('orders.amount') USD</a></th>
                    <th>Order Items</th>
                    <th>@lang('orders.status')</th>
                    <th>HD Order Id</th>
                    <th class="no-print">@lang('orders.actions.actions')</th>
                </tr>
                <tr class="no-print">
                    <th>
                        
                        <input type="search" class="form-control col-md-9" wire:model.debounce.1000ms="date">
                    </th>
                    @admin
                    <th>
                        <input type="search" class="form-control" wire:model.debounce.1000ms="name">
                    </th>
                    @endadmin
                    <th>
                        <input type="search" class="form-control" wire:model.debounce.1000ms="order_id">
                    </th>
                    <th>
                        <input type="search" class="form-control" wire:model.debounce.1000ms="carrier">
                    </th>
                    <th>
                        <input type="search" class="form-control" wire:model.debounce.1000ms="amount">
                    </th>
                    <th>
                        <input type="search" class="form-control" wire:model.debounce.1000ms="items">
                    </th>
                    <th>
                        <select class="form-control" wire:model="status">
                            <option value="">All</option>
                            <option value="Shipped">Shipped</option>
                            <option value="Canceled">Canceled</option>
                        </select>
                    </th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                    <tr>
                        <td>{{ optional(\Carbon\Carbon::parse($order->purchase_date))->format('Y-m-d') }}</td>
                        @admin
                            <td>{{ $order->user_name }} - {{ $order->role_name == ('wholesale') ? 'W' : 'R' }}</td>
                        @endadmin
                        <td>{{ $order->amazon_order_id }}</td>
                        <td>{{ $order->shipment_service_level_category }}</td>
                        <td>{{ number_format($order->order_total, 2) }}</td>
                        <td>{{ $order->number_of_items_shipped }}</td>
                        <td>{{ $order->order_status }}</td>
                        <td>{{ $order->seller_order_id }}</td>
                    
                        <td class="d-flex no-print">
                            <div class="btn-group">
                                <div class="dropdown">
                                    <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        @lang('orders.actions.actions')
                                    </button>
                                    <div class="dropdown-menu overlap-menu" aria-labelledby="dropdownMenuLink">
                                        @user
                                            @if(!$order->seller_order_id)
                                                <a href="#" title="Creat HD Order"  class="btn dropdown-item w-100 edit-order" data-toggle="modal" data-target="#hd-modal" data-url="{{ route('amazon.orders.create', [$order->sale_order_id]) }}">
                                                    <i class="feather icon-copy"></i> Create HD Order
                                                </a>
                                            @endif
                                        @enduser
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <livewire:order.bulk-edit.modal/>
    </div>
    <div class="d-flex justify-content-end my-2 pb-4 mx-2">
        {{ $orders->links() }}
    </div>
    @include('layouts.livewire.loading')
</div>
@section('modal')
    <x-modal/>
@endsection

@push('lvjs-stack')
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            
            @this.on('updated-status',function(orderId,status){
                @this.call('render')
                $.post('{{route("admin.order.update.status")}}',{
                    _token: "{{ csrf_token() }}",
                    order_id: orderId,
                    status : status,
                    user: '{{auth()->user()->name}}'
                })
                .then(function(response){
                    if ( response.success ){
                        toastr.success(response.message)
                        @this.call('render')
                    }else{
                        toastr.error(response.message)
                        @this.call('render')
                    }
                }).catch(function(data){
                    toastr.error(response.message)
                })
            })

            // @this.on('edit-order',function(){
            //     $('#order-table').addClass('w-25');
            //     $('#order-table').removeClass('w-100');
            // })
        });
    </script>
@endpush