<div class="p-2">
    @admin
        <div class="row">
            <div class="col-12 text-right mb-3">
                <p class="mr-2 h5">Paid Commission:<span class="text-success h4"> $ {{ number_format($balance->where('is_paid', true)->sum('value'), 2) }}</span></p>
                <p class="mr-2 h5">UnPaid Commission:<span class="text-danger h4"> $ {{ number_format($balance->where('is_paid', false)->sum('value'), 2) }}</span></p>
            </div>
        </div>
    @endadmin
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
            <form action="{{ route('admin.affiliate.sale.exports') }}" method="GET" target="_blank">
                @csrf
                <label>@lang('sales-commission.start date')</label>
                <input type="date" name="start_date" class="from-control col-2">

                <label>@lang('sales-commission.end date')</label>
                <input type="date" name="end_date" class="from-control col-2">

                <button class="btn btn-success" title="@lang('sales-commission.Download Sales')">
                    @lang('sales-commission.Download Sales') <i class="fa fa-arrow-down"></i>
                </button>
            </form>
        </div>
    </div>
    <div class="table-wrapper position-relative">
        <table class="table mb-0 table-responsive-md" id="">
            <thead>
                <tr>
                    @admin
                        <th style="min-width: 100px;">
                            <select name="" id="bulk-actions" class="form-control">
                                <option value="clear">Clear All</option>
                                <option value="checkAll">Select All</option>
                                <option value="pay-commission">Pay Commission</option>
                            </select>
                        </th>
                    @endadmin
                    <th>@lang('sales-commission.Date')</th>
                    @admin
                        <th>@lang('sales-commission.User')</th>
                    @endadmin
                    <th>Commission From</th>
                    <th>@lang('sales-commission.Order ID')</th>
                    <th>Whr#</th>
                    <th>Customer Reference</th>
                    <th>Carrier Tracking#</th>
                    <th>Weight</th>
                    <th>@lang('sales-commission.Value')</th>
                    <th>@lang('sales-commission.Type')</th>
                    <th>@lang('sales-commission.Commission')</th>
                    <th>@lang('Is Paid')</th>
                    <th>@lang('status')</th>
                </tr>
                <tr class="no-print">
                    @admin
                        <th></th>
                    @endadmin
                    <th>
                        <div class="row">
                            <input type="date" class="form-control col-md-6" wire:model.debounce.1000ms="start">
                            <input type="date" class="form-control col-md-6" wire:model.debounce.1000ms="end">
                        </div>
                        
                    </th>
                    @admin
                    <th>
                        <input type="search" class="form-control" wire:model.debounce.1000ms="name">
                    </th>
                    @endadmin
                    <th>
                        <input type="search" class="form-control" wire:model.debounce.1000ms="user">
                    </th>

                    <th>
                        <input type="search" class="form-control" wire:model.debounce.1000ms="order">
                    </th>
                    <th>
                        <input type="search" class="form-control" wire:model.debounce.1000ms="whr">
                    </th>
                    <th>
                        <input type="search" class="form-control" wire:model.debounce.1000ms="reference">
                    </th>
                    <th>
                        <input type="search" class="form-control" wire:model.debounce.1000ms="tracking">
                    </th>
                    <th>
                        <input type="search" class="form-control" wire:model.debounce.1000ms="weight">
                    </th>
                    
                    <th>
                        <input type="search" class="form-control" wire:model.debounce.1000ms="value">
                    </th>
                    <th >
                        <select class="form-control" wire:model="saleType">
                            <option value="">All</option>
                            <option value="flat">Flat</option>
                            <option value="percentage">Percentage</option>
                        </select>
                    </th>
                    <th>
                        <input type="search" class="form-control" wire:model.debounce.1000ms="commission">
                    </th>
                    <th></th>
                    <th></th>
                   
                </tr>
            </thead>
            <tbody>
                @forelse ($sales as $sale)
                    @include('admin.affiliate.components.sale-row',['sale'=>$sale])    
                @empty
                    <x-tables.no-record colspan="12"></x-tables.no-record>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-end my-2 pb-4 mx-2">
        {{ $sales->links() }}
    </div>
    @include('layouts.livewire.loading')
</div>
