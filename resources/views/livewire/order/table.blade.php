<div class="row">
    <div class="col-md-12">
        <div class="hd-card mt-1 mb-3">
            <div class="row col-12 p-0 m-0" id="togglers" style="justify-content: space-between;">
            </div>
            <div class="row col-8 pr-0 pl-4 " id="singleSearch"
                @if ($this->search) style="display: block !important;" @endif>
                <div class="form-group singleSearchStyle col-12">

                    <form wire:submit.prevent="render">
                        <div class="form-group mb-2 col-12 row">
                            <label class="col-12 text-left pl-1"> Search</label>
                            <input type="text" class="form-control col-8 hd-search" wire:model.defer="search">
                            <button type="submit" class="btn btn-primary ml-2">
                                <i class="fa fa-search"></i>
                            </button>
                            <button class="btn btn-primary ml-1 waves-effect waves-light"
                                onclick="window.location.reload();">
                                <i class="fa fa-undo" data-bs-toggle="tooltip" title=""
                                    data-bs-original-title="fa fa-undo" aria-label="fa fa-undo"
                                    aria-hidden="true">
                                </i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="row col-12 pr-0 m-0 pl-0" id="datefilters">
                <div class=" col-6 text-left">
                    <div class="row" id="dateSearch">
                        <form action="{{ route('admin.order.exports') }}" method="GET" target="_blank" class="row col-12">
                            @csrf
                            <div class="col-4">
                                <label>Start Date</label>
                                <input type="date" name="start_date" class="form-control">
                            </div>
                            <div class="col-4">
                                <label>End Date</label>
                                <input type="date" name="end_date" class="form-control">
                            </div>
                            <div class="col-2">
                                <label>Type</label>
                                <select class="form-control" name="type">
                                    <option value="">All</option>
                                    <option value="domestic">Domestic</option>
                                </select>
                            </div>
                            <button class="btn btn-success searchDateBtn" title="@lang('orders.import-excel.Download')">
                                <i class="fa fa-arrow-down"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive order-table">
            <table class="table mb-0  table-bordered" id="tblOrders">
                <thead>
                    <tr>
                        @if (\Request::route()->getName() != 'admin.trash-orders.index')
                            <th id="optionChkbx">
                                <div class="vs-checkbox-con vs-checkbox-primary" title="Select All">
                                    <input type="checkbox" id="checkAll" name="orders[]" class="check-all"
                                        value="">
                                    <span class="vs-checkbox vs-checkbox-sm">
                                        <span class="vs-checkbox--check">
                                            <i class="vs-icon feather icon-check"></i>
                                        </span>
                                    </span>
                                </div>
                            </th>
                        @endif
                        @admin
                            <th id="userNameCol">USER NAME</th>
                        @endadmin
                        <th>
                            <a title="Click to Sort" wire:click.prevent="sortBy('created_at')">
                                @lang('orders.date')</a><a wire:click.prevent="sortBy('created_at')"
                                class="fas fa-sort text-right custom-sort-arrow">
                            </a>
                        </th>
                        <th>
                            <a title="Click to Sort" wire:click.prevent="sortBy('id')">
                                @lang('orders.order-id')</a><a wire:click.prevent="sortBy('id')"
                                class="fas fa-sort text-right custom-sort-arrow">
                            </a>
                        </th>
                        <th>Carrier Tracking</th>
                        <th>Referência do Cliente</th>
                        <th>TRACKING CODE</th>
                        <th>
                            <a title="Click to Sort" wire:click.prevent="sortBy('gross_total')">
                                @lang('orders.amount')</a><a wire:click.prevent="sortBy('gross_total')"
                                class="fas fa-sort text-right custom-sort-arrow">
                            </a>
                        </th>
                        <th>@lang('orders.status')</th>
                        <th>@lang('orders.payment-status')</th>
                        <th class="no-print">@lang('orders.actions.actions')</th>
                    </tr>
                    <tr>
                        <th></th>
                        @admin
                            <th>
                                <input type="search" class="form-control" wire:model.debounce.1000ms="name">
                            </th>
                        @endadmin
                        <th>
                            <input type="search" class="form-control" wire:model.debounce.1000ms="date">
                        </th>
                        <th>
                            <input type="search" class="form-control" wire:model.debounce.1000ms="whr_number">
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
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $order)
                        @include('admin.orders.components.order-row', ['order' => $order])
                    @empty
                        <x-tables.no-record colspan="15"></x-tables.no-record>
                    @endforelse
                </tbody>
            </table>
            <livewire:order.bulk-edit.modal />
        </div>
        <div class="row">
            <div class="col-1 pt-2">
                <select class="form-control hd-search" wire:model="pageSize">
                    <option value="1">1</option>
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="20">20</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="300">300</option>
                </select>
            </div>
            <div class="col-11 pr-0 d-flex justify-content-end pr-3 pt-2">
                {{ $orders->links() }}
            </div>
        </div>
        @include('layouts.livewire.loading')
    </div>
</div>

@push('lvjs-stack')
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            
            @this.on('updated-status',function(orderId,status){
                $.post('{{route("api.order.status.update")}}',{
                    order_id: orderId,
                    status : status,
                    user: '{{auth()->user()->name}}'
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
        function toggleDateSearch() {
            const div = document.getElementById('dateSearch');
            if (div.style.display != 'block') {
                div.style.display = 'block';
            } else {
                div.style.display = 'none';
            }

        }

        function toggleOrderPageSearch() {
            const div = document.getElementById('singleSearch');
            if (div.style.display != 'block') {
                div.style.display = 'block';
            } else {
                div.style.display = 'none';
            }
        }
    </script>
@endpush
