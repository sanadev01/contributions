<div class="p-2" >
    <div class="row mb-2 no-print">
        @admin
            @if (request()->route()->getName() != 'admin.trash-orders.index')
            <div class="col-12">
                <div class="p-1 mb-3">
                    <ul class="nav nav-pills">
                        <li class="nav-item ">
                            <a class="nav-link border @if($userType == 'wholesale') btn btn-primary text-white @endif" href="{{ route('admin.orders.show','wholesale') }}"><span style="font-size: 22px;">Wholesales</span></a>
                        </li>
                        <li class="nav-item ">
                            <a class="nav-link border @if($userType == 'retailer') btn btn-primary text-white @endif" href="{{ route('admin.orders.show','retailer') }}"><span style="font-size: 22px;">Retail</span></a>
                        </li>
                        <li class="nav-item ">
                            <a class="nav-link border @if($userType == 'domestic') btn btn-primary text-white @endif" href="{{ route('admin.orders.show','domestic') }}"><span style="font-size: 22px;">Domestic</span></a>
                        </li>
                        <li class="nav-item ">
                            <a class="nav-link border @if($userType == 'pickups') btn btn-primary text-white @endif" href="{{ route('admin.orders.show','pickups') }}"><span style="font-size: 22px;">Pickups</span></a>
                        </li>
                    </ul>
                </div>        
            </div>
            @endif 
        @endadmin       
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
        <div class="row col-11  d-flex justify-content-end pr-0">
            <form class="row col-8  d-flex justify-content-end " action="{{ route('admin.order.exports') }}" method="GET" target="_blank">
                @csrf
                @if (request()->route()->getName() != 'admin.trash-orders.index')
                    <input type="hidden" name="is_trashed" value="0">
                @else
                    <input type="hidden" name="is_trashed" value="1">
                @endif

                <label>Start Date</label>
                <input type="date" name="start_date" class="form-control col-2">

                <label>End Date</label>
                <input type="date" name="end_date" class="form-control col-2">

                <label>Type</label>
                <select class="form-control col-2 mr-2" name="type">
                    <option value="">All</option>
                    <option value="domestic">Domestic</option>
                    <option value="gss">GSS</option>
                    <option value="{{ App\Models\Order::STATUS_ORDER }}">ORDER</option>
                    <option value="{{ App\Models\Order::STATUS_CANCEL }}">CANCELLED</option>
                    <option value="{{ App\Models\Order::STATUS_REJECTED }}">REJECTED</option>
                    <option value="{{ App\Models\Order::STATUS_RELEASE }}">RELEASED</option>
                    <option value="{{ App\Models\Order::STATUS_PAYMENT_PENDING }}">PAYMENT_PENDING</option>
                    <option value="{{ App\Models\Order::STATUS_PAYMENT_DONE }}">PAYMENT_DONE</option>
                    <option value="{{ App\Models\Order::STATUS_SHIPPED }}">SHIPPED</option>
                    <option value="{{ App\Models\Order::STATUS_REFUND }}">REFUND / CANCELLED</option>
                </select>

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
                    @if (\Request::route()->getName() != 'admin.trash-orders.index' && $isTrashed)
                        <th>
                            @lang('orders.Bulk Print')
                        </th>
                    @endif
                    <th>
                        {{-- @if (\Request::route()->getName() != 'admin.trash-orders.index'  && $isTrashed)
                            <span class="mr-4"> @lang('Edit Order')</span>
                        @endif --}}
                        <a href="#" wire:click.prevent="sortBy('created_at')">@lang('orders.date')</a>
                    </th>
                    <th>
                        <a href="#" wire:click.prevent="sortBy('id')">@lang('orders.order-id')</a> <i>  </i>
                    </th>
                    @admin
                    <th>User Name</th>
                    @endadmin
                    <th>Loja/Cliente</th>
                    <th>Referência do Cliente</th>
                    <th>Carrier</th>
                    @admin
                    <th>Carrier Cost</th>
                    @endadmin
                    <th>Tracking Code</th>
                    <th><a href="#" wire:click.prevent="sortBy('gross_total')">@lang('orders.amount')</a></th>
                    <th>@lang('orders.Estimate tax & duty')</th>
                    <th>@lang('orders.status')</th>
                    <th>@lang('orders.payment-status')</th>
                    <th class="no-print">@lang('orders.actions.actions')</th>
                </tr>
                <tr class="no-print">
                    @if (\Request::route()->getName() != 'admin.trash-orders.index' && $isTrashed)
                        <th style="min-width: 100px;">
                            <select name="" id="bulk-actions" class="form-control">
                                <option value="clear">Clear All</option>
                                <option value="checkAll">Select All</option>
                                <option value="print-label">Print Label</option>
                                <option value="consolidate-domestic-label">Print Domestic Label</option>
                                <option value="pre-alert">Pre Alert</option>
                                <option value="move-order-trash">Move Trash</option>
                            </select>
                        </th>
                    @endif
                    <th>
                        
                        <input type="search" class="form-control col-md-9" wire:model.debounce.1000ms="date">
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
                        <input type="search" class="form-control" wire:model.debounce.1000ms="customer_reference">
                    </th>
                    <th>
                        <select class="form-control" wire:model.debounce.1000ms="carrier">
                            <option value="">All</option>
                            <option value="Brazil">Correios Brazil</option>
                            <option value="Anjun">Correios A</option>
                            <option value="AnjunChina">Correios AJ</option>
                            <option value="BCN">Correios B</option>
                            <option value="USPS">USPS</option>
                            <option value="UPS">UPS</option>
                            <option value="FEDEX">FEDEX</option>
                            <option value="Chile">Correios Chile</option>
                            <option value="Global eParcel">Global eParcel</option>
                            <option value="Prime5">Prime5</option>
                            <option value="Post Plus">Post Plus</option>
                            <option value="Total Express">Total Express</option>
                            <option value="HD Express">HD Express</option>
                            <option value="Hound Express">Hound Express</option>
                        </select>
                    </th>
                    @admin<th></th>@endadmin
                    <th>
                        <input type="search" class="form-control" wire:model.debounce.1000ms="tracking_code">
                    </th>
                    <th>
                        <input type="search" class="form-control" wire:model.debounce.1000ms="amount">
                    </th>
                    <th>
                        <input type="search" class="form-control" wire:model.debounce.1000ms="tax_and_duty">
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
                            <option value="{{ App\Models\Order::STATUS_REFUND }}">REFUND / CANCELLED</option>
                        </select>
                    </th>
                    <th>
                        <select class="form-control" wire:model="paymentStatus">
                            <option value="">All</option>
                            <option value="paid">Paid</option>
                            <option value="unpaid">Unpaid</option>
                        </select>
                    </th>
                    <th >
                        <select class="form-control" wire:model="orderType">
                            <option value="">All</option>
                            <option value="consolidated">Consolidated</option>
                            <option value="non-consolidated">Non-Consolidated</option>
                        </select>
                    </th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($orders as $order)
                    @include('admin.orders.components.order-row',['order'=>$order])    
                @empty
                    <x-tables.no-record colspan="12"></x-tables.no-record>
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