<div>
    <div class="p-2">
        @admin
            <div class="row">
                <div class="col-12 text-right mb-3">
                    <p class="mr-2 h5">UserName:<span class="text-success h4"> {{ $user->name }}</span></p>
                    <p class="mr-2 h5">POBOX Number:<span class="text-success h4"> {{ $user->pobox_number }}</span></p>
                    <p class="mr-2 h5">Paid Commission:<span class="text-success h4"> $ {{ number_format($user->affiliateSales()->where('is_paid', true)->sum('commission'), 2) }}</span></p>
                    <p class="mr-2 h5">UnPaid Commission:<span class="text-danger h4"> $ {{ number_format($user->affiliateSales()->where('is_paid', false)->sum('commission'), 2) }}</span></p>
                </div>
            </div>
        @endadmin
        <div class="col-12 text-right">
            <a href="{{ route('admin.reports.commission.index') }}" class="btn btn-primary">
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
                <form action="{{ route('admin.affiliate.sale.exports') }}" method="GET" target="_blank">
                    @csrf
                    <input type="hidden" name="user_id" value="{{ $user->id }}">

                    <div class="row mt-1">
                        <div class="form-group col-10 col-sm-6 col-md-3">
                            <div class="row">
                                <label class="col-md-3 control-label">@lang('sales-commission.start date')</label>
                                <input type="date" name="start_date" class="form-control col-md-8">
                            </div>
                        </div>
                        <div class="form-group col-12 col-sm-6 col-md-3">
                            <div class="row">
                                <label class="col-md-3 control-label">@lang('sales-commission.end date')</label>
                                <input type="date" name="end_date" class="form-control col-md-8">
                            </div>
                        </div>
                        <div class="form-group col-12 col-sm-6 col-md-3">
                            <div class="row">
                                <label class="col-md-3 control-label">Status</label>
                                <select class="form-control col-md-8" name="status">
                                    <option value="">All</option>
                                    <option value="paid">Paid</option>
                                    <option value="unpaid">Unpaid</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-12 col-sm-6 col-md-3">
                            <button class="btn btn-success col-4" title="@lang('sales-commission.Download Sales')">
                                @lang('sales-commission.Download Sales') <i class="fa fa-arrow-down"></i>
                            </button>
                        </div>
                    </div>
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
                       
                        <th>Commission From</th>
                        <th>WHR#</th>
                        <th>Tracking Code</th>
                        <th>Customer Reference</th>
                        <th>Weight</th>
                        <th>@lang('sales-commission.Value')</th>
                        <th>@lang('sales-commission.Type')</th>
                        <th>@lang('sales-commission.Commission')</th>
                        <th>@lang('Is Paid')</th>
                        {{-- <th>@lang('status')</th> --}}
                        @admin
                            <th>@lang('Action')</th>
                        @endadmin
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
                        
                        <th>
                            <input type="search" class="form-control" wire:model.debounce.1000ms="user_commission">
                        </th>
    
                        <th>
                            <input type="search" class="form-control" wire:model.debounce.1000ms="whr">
                        </th>
                        <th>
                            <input type="search" class="form-control" wire:model.debounce.1000ms="corrios_tracking">
                        </th>
                        <th>
                            <input type="search" class="form-control" wire:model.debounce.1000ms="reference">
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
                        @admin
                            <th></th>
                        @endadmin
                    </tr>
                </thead>
                <tbody>
                    @forelse ($sales as $sale)
                    <tr>
                        @admin
                            <td>
                                <div class="vs-checkbox-con vs-checkbox-primary" title="@lang('orders.Bulk Print')">
                                    <input type="checkbox" name="sales[]" class="bulk-sales" value="{{$sale->id}}">
                                    <span class="vs-checkbox vs-checkbox-lg">
                                        <span class="vs-checkbox--check">
                                            <i class="vs-icon feather icon-check"></i>
                                        </span>
                                    </span>
                                    <span class="h3 mx-2 text-primary my-0 py-0"></span>
                                </div>
                            </td>
                        @endadmin
                        <td>
                            {{ optional($sale->created_at)->format('m/d/Y') }}
                        </td>
                        
                        <td>
                            {{ optional($sale->order->user)->name }}
                        </td>
                        <td>
                            <a href="#" data-toggle="modal" data-target="#hd-modal" data-url="{{ route('admin.modals.order.invoice',$sale->order) }}" title="@lang('sales-commission.Show Order Details')">
                                {{ $sale->order->warehouse_number }}
                            </a>
                            
                        </td>
                        
                        <td>
                            {{ $sale->order->corrios_tracking_code }}
                        </td>
                        <td>
                            {{ $sale->order->customer_reference }}
                        </td>
                        
                        <td>
                            {{ $sale->order->weight . $sale->order->measurement_unit }}
                        </td>
                        
                        <td>
                            {{ $sale->value }}
                            
                        </td>
                        <td>
                            {{ $sale->type }}
                            
                        </td>
                        <td>
                            {{ $sale->commission? number_format($sale->commission, 2): 0 }}
                        </td>
                        <td>
                            @if( $sale->is_paid )
                                <i class="feather icon-check text-success"></i>
                            @else
                                <i class="feather icon-x text-danger"></i>
                            @endif
                        </td>
                        
                        @admin
                            <td class="d-flex">
                                <div class="btn-group">
                                    <div class="dropdown">
                                        <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            @lang('parcel.Action')
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right dropright">
                                            @can('delete', $sale)
                                                <form method="post" action="{{ route('admin.affiliate.sales-commission.destroy',$sale) }}" class="d-inline-block w-100" onsubmit="return confirmDelete()">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="dropdown-item w-100 text-danger" title="@lang('parcel.Delete Parcel')">
                                                        <i class="feather icon-trash-2"></i> @lang('parcel.Delete')
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </div>
                                </div>
                            </td>
                        @endadmin 
                    </tr>    
                    @empty
                        <x-tables.no-record colspan="15"></x-tables.no-record>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-end my-2 pb-4 mx-2">
            {{ $sales->links() }}
        </div>
        @include('layouts.livewire.loading')
    </div>
    
</div>
