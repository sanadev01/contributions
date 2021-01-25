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
        <div class="col-11 text-right">
            <form action="{{ route('admin.order.exports') }}" method="GET" target="_blank">
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
    <div class="table-wrapper position-relative">
        <table class="table mb-0 table-responsive-md" id="order-table">
            <thead>
                <tr>
                    <th>
                        @lang('orders.Bulk Print')
                    </th>
                    <th>
                        <span class="mr-4"> @lang('Edit Order')</span>
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
                    <th>@lang('orders.type')</th>
                    <th>@lang('orders.payment-status')</th>
                    <th class="no-print">@lang('orders.actions.actions')</th>
                </tr>
                <tr class="no-print">
                    <th style="min-width: 100px;">
                        <select name="" id="bulk-actions" class="form-control">
                            <option value="clear">Clear All</option>
                            <option value="checkAll">Select All</option>
                            <option value="print-label">Print Label</option>
                        </select>
                    </th>
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
                            <option value="{{ App\Models\Order::STATUS_PAYMENT_PENDING }}">PAYMENT_PENDING</option>
                            <option value="{{ App\Models\Order::STATUS_PAYMENT_DONE }}">PAYMENT_DONE</option>
                            <option value="{{ App\Models\Order::STATUS_SHIPPED }}">SHIPPED</option>
                        </select>
                    </th>
                    <th >
                        <select class="form-control" wire:model="orderType">
                            <option value="">All</option>
                            <option value="consolidated">Consolidated</option>
                            <option value="non-consolidated">Non-Consolidated</option>
                        </select>
                    </th>
                    <th>
                        <select class="form-control" wire:model="paymentStatus">
                            <option value="">All</option>
                            <option value="paid">Paid</option>
                            <option value="unpaid">Unpaid</option>
                        </select>
                    </th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($orders as $order)
                    @include('admin.orders.components.order-row',['order'=>$order])    
                @empty
                    <x-tables.no-record colspan="9"></x-tables.no-record>
                @endforelse
            </tbody>
        </table>
        <livewire:order.bulk-edit.modal/>
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

            // @this.on('edit-order',function(){
            //     $('#order-table').addClass('w-25');
            //     $('#order-table').removeClass('w-100');
            // })
        });
    </script>
@endpush