<div>
    <div class="p-2">
        <div class="col-12 text-right">
            <a href="{{ route('admin.reports.audit-report.index') }}" class="btn btn-primary">
                Back to list
            </a>
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
            <div class="col-11">
                <form action="{{ route('admin.reports.audit-report.create') }}" method="GET" target="_blank">
                    @csrf

                    <div class="row mt-1">
                        <div class="form-group col-10 col-sm-6 col-md-3">
                            <div class="col-12">
                                <label class="col-md-6 control-label">@lang('sales-commission.start date')</label>
                                <input type="date" name="start_date" class="form-control ">
                            </div>
                        </div>
                        <div class="form-group col-12 col-sm-6 col-md-3">
                            <div class="col-12">
                                <label class="col-md-6 control-label">@lang('sales-commission.end date')</label>
                                <input type="date" name="end_date" class="form-control ">
                            </div>
                        </div>
                        <div class="form-group col-12 col-sm-6 col-md-3">
                            <div class="col-12">
                                <label>@lang('parcel.User POBOX Number') <span class="text-danger">*</span></label>
                                <livewire:components.search-user />
                                @error('pobox_number')
                                <div class="help-block text-danger"> {{ $message }} </div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group col-12 col-sm-6 col-md-3">
                            <div class="col-12 mt-4">
                                <button class="btn btn-success" title="@lang('sales-commission.Download Sales')">
                                    @lang('sales-commission.Download Sales') <i class="fa fa-arrow-down"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="table-wrapper position-relative">
            <table class="table mb-0 table-responsive-md" id="">
                <thead>
                    <tr>
                        <th>Order Date</th>
                        <th>User</th>
                        <th>WHR#</th>
                        <th>Tracking Code</th>
                        <th>Weight (kg) | (lbs)</th>
                        <th>Perfume/Battery</th>
                        <th>Additional Charges</th>
                        <th>Shipping Value</th>
                        <th>Total Charges</th>
                        {{-- <th>Profit</th> --}}
                        <th>Corrieos Charges</th>
                    </tr>
                    <tr class="no-print">
                        <th>
                            <div class="row">
                                <input type="date" class="form-control col-md-6" wire:model.debounce.1000ms="startDate">
                                <input type="date" class="form-control col-md-6" wire:model.debounce.1000ms="endDate">
                            </div>
                        </th>
                        
                        <th>
                            <input type="search" class="form-control" wire:model.debounce.1000ms="user">
                        </th>
    
                        <th>
                            <input type="search" class="form-control" wire:model.debounce.1000ms="whr">
                        </th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($auditRecords as $order)
                    <tr>
                        <td>
                            {{ optional($order->order_date)->format('m/d/Y') }}
                        </td>
                        
                        <td>
                            {{ optional($order->user)->name }}
                        </td>
                        <td>
                            <a href="#" data-toggle="modal" data-target="#hd-modal" data-url="{{ route('admin.modals.order.invoice',$order) }}" title="@lang('orders-commission.Show Order Details')">
                                {{ $order->warehouse_number }}
                            </a>
                        </td>
                        <td>
                            {{ $order->corrios_tracking_code }}
                        </td>
                        <td>
                            {{ $order->getWeight('kg') }} kg  ( {{ $order->getWeight('lbs') }} lbs )
                        </td>
                        <td>
                            {{ number_format($order->dangrous_goods,2) }}
                        </td>
                        <td>
                            {{ number_format($order->services->sum('price'),2) }}
                        </td>
                        <td>
                            {{ number_format($order->shipping_value,2) }}
                        </td>
                        <td>
                            {{ number_format($order->gross_total,2) }}
                        </td>
                        {{-- <td>
                            {{ number_format(optional($this->getRates($order))['profitPackageRate'],2) }}
                        </td> --}}
                        <td>
                            {{ number_format(optional($this->getRates($order))['accrualRate'],2) }}
                        </td>
                    </tr>    
                    @empty
                        <x-tables.no-record colspan="15"></x-tables.no-record>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-end my-2 pb-4 mx-2">
            {{ $auditRecords->links() }}
        </div>
        @include('layouts.livewire.loading')
    </div>
    
</div>

@section('modal')
    <x-modal/>
@endsection
