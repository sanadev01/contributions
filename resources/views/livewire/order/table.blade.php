<div class="row">
    <div class="col-md-12" >
        <div class="hd-card mt-1 mb-3">
            <div class="row col-12 p-0 m-0 pb-2 pt-2" id="togglers" style="justify-content: space-between;">
                    <div id="printBtnDiv">
                        <button type="btn" onclick="toggleDateSearch()" id="customSwitch1" class="btn btn-primary mr-1 mb-1 waves-effect waves-light"><i class="feather icon-check-square"></i></button>
                        <button type="btn" onclick="toggleDateSearch()" id="customSwitch2" class="btn btn-primary mr-1 mb-1 waves-effect waves-light"><i class="feather icon-printer"></i></button>
                        <button type="btn" onclick="toggleDateSearch()" id="customSwitch3" class="btn btn-primary mr-1 mb-1 waves-effect waves-light"><i class="feather icon-printer"></i></button>
                        <button type="btn" onclick="toggleDateSearch()" id="customSwitch4" class="btn btn-primary mr-1 mb-1 waves-effect waves-light"><i class="feather icon-trash"></i></button>
                    </div>
                {{-- <div class="col-11 text-right p-0">
                    <form action="{{ route('admin.order.exports') }}" method="GET" target="_blank">
                        @csrf
                        <label>Start Date</label>
                        <input type="date" name="start_date" class="from-control col-2 hd-search">

                        <label>End Date</label>
                        <input type="date" name="end_date" class="from-control col-2 hd-search">

                        <button class="btn btn-success" title="@lang('orders.import-excel.Download')">
                            @lang('orders.Download Orders') <i class="fa fa-arrow-down"></i>
                        </button>
                    </form>
                </div> --}}
            </div>
            <div class="row col-10" id="datefilters">
                <div class=" col-10 text-left mb-2 pl-0" id="dateSearch" style="margin: 22px;">
                    <div class="row col-12 my-3">
                        {{-- <form action="{{ route('admin.order.exports') }}" method="GET" target="_blank">
                            @csrf
                            <div style="float: left">
                            <label>Start Date</label>
                            <input type="date" name="start_date" class="form-control">
                            </div>
                            <div style="float: right">
                            <label>End Date</label>
                            <input type="date" name="end_date" class="form-control">
                            <button class="btn btn-primary btn-sm" style="margin-bottom: 4px;" title="@lang('orders.import-excel.Download')">
                                <i class="fa fa-arrow-down"></i>
                                </button>
                            </div>
                        </form> --}}
                        <form class="form-inline" action="{{ route('admin.order.exports') }}" method="GET" target="_blank">
                            @csrf
                            <div class="form-group mb-2">
                                <label>Start Date</label>
                                <input type="date" name="start_date" class="form-control">
                            </div>
                            <div class="form-group mx-sm-3 mb-2">
                                <label>End Date</label>
                                <input type="date" name="end_date" class="form-control">
                            </div>
                            <button class="btn btn-primary btn-sm" style="margin-bottom: 4px;" title="@lang('orders.import-excel.Download')">
                                <i class="fa fa-arrow-down"></i>
                                </button>                          
                            </form>
                    </div>
                </div>
            </div>
        </div>

        @admin
            {{-- <div class="row col-md-12 mb-2" >
                @if (request()->route()->getName() != 'admin.trash-orders.index')
                    <div class="col-12 p-0">
                        <ul class="nav nav-pills m-0">
                            <li class="nav-item ">
                                <a class="nav-link border @if(!$userType) btn btn-primary text-white @endif" href="{{ route('admin.orders.index') }}"><span style="font-size: 22px;">All Orders</span></a>
                            </li>
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
                @endif
            </div> --}}
        @endadmin

        <div class="table-responsive order-table">
            <table class="table mb-0  table-bordered" id="">
                <thead>
                    <tr>
                        @if (\Request::route()->getName() != 'admin.trash-orders.index')
                            <th id="optionChkbx">
                                {{-- @lang('orders.Bulk Print') --}}
                            </th>
                        @endif
                        @admin
                            <th id="userNameCol">User Name</th>
                        @endadmin
                        <th>
                            @if (\Request::route()->getName() != 'admin.trash-orders.index')
                                {{-- <span class="mr-4"> @lang('Edit Order')</span> --}}
                            @endif
                            <a href="#" class="" wire:click.prevent="sortBy('created_at')">@lang('orders.date')</a>
                        </th>
                        <th>
                            <a href="#" class="" wire:click.prevent="sortBy('id')">@lang('orders.order-id')</a> <i>  </i>
                        </th>
                        
                        {{-- <th>Loja/Cliente</th>
                        <th>Carrier Tracking</th> --}}
                        <th>Reference</th>
                        {{-- <th>Carrier</th>
                        @admin
                            <th>Carrier Cost</th>
                        @endadmin --}}
                        <th>Tracking Code</th>
                        <th><a href="#" wire:click.prevent="sortBy('gross_total')">@lang('orders.amount')</a></th>
                        <th>@lang('orders.status')</th>
                        <th>@lang('orders.type')</th>
                        <th>@lang('orders.payment-status')</th>
                        <th class="no-print">@lang('orders.actions.actions')</th>
                    </tr>
                    {{-- <tr class="no-print">
                        @if (\Request::route()->getName() != 'admin.trash-orders.index')
                            <th style="min-width: 100px;">
                                <select name="" id="bulk-actions" class="form-control">
                                    <option value="clear">Clear All</option>
                                    <option value="checkAll">Select All</option>
                                    <option value="print-label">Print Label</option>
                                    <option value="consolidate-domestic-label">Print Domestic Label</option>
                                    <option value="move-order-trash">Move Trash</option>
                                </select>
                            </th>
                        @endif
                        <th>

                            <input type="search" class="form-control col-md-9 offset-3" wire:model.debounce.1000ms="date">
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
                            <select class="form-control" wire:model.debounce.1000ms="carrier">
                                <option value="">All</option>
                                <option value="Brazil">Correios Brazil</option>
                                <option value="USPS">USPS</option>
                                <option value="UPS">UPS</option>
                                <option value="FEDEX">FEDEX</option>
                                <option value="Chile">Correios Chile</option>
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
                    </tr> --}}
                </thead>
                <tbody>
                    @forelse ($orders as $order)
                        @include('admin.orders.components.order-row',['order'=>$order])
                    @empty
                        <x-tables.no-record colspan="15"></x-tables.no-record>
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
</div>

@push('lvjs-stack')
    <script>

        function toggleDateSearch()
    {
        var checkBox = document.getElementById("customSwitch8");
        const div = document.getElementById('dateSearch');
        if (div.style.display != 'block'){
            div.style.display = 'block';
            console.log('asdasd');
        } else {
            div.style.display = 'none';
            console.log('aa');

        }

    }
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