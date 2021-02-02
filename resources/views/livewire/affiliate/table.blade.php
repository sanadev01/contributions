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
                    <th>@lang('sales-commission.Date')</th>
                    @admin
                        <th>@lang('sales-commission.User')</th>
                    @endadmin
                    <th>@lang('sales-commission.Order ID')</th>
                    <th>@lang('sales-commission.Value')</th>
                    <th>@lang('sales-commission.Type')</th>
                    <th>@lang('sales-commission.Commission')</th>
                    <th>@lang('status')</th>
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
                        <input type="search" class="form-control" wire:model.debounce.1000ms="order">
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
                   
                </tr>
            </thead>
            <tbody>
                @forelse ($sales as $sale)
                    @include('admin.affiliate.components.sale-row',['sale'=>$sale])    
                @empty
                    <x-tables.no-record colspan="6"></x-tables.no-record>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-end my-2 pb-4 mx-2">
        {{ $sales->links() }}
    </div>
    @include('layouts.livewire.loading')
</div>
