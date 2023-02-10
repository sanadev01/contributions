<div>
    <div class="p-0 pr-2 d-flex justify-content-between">
        <div>
            <div class="" id="printBtnDiv">
                <button type="btn" id="pay-commission" class="btn btn-primary ml-2 waves-effect waves-light">
                    <i class="fa fa-dollar-sign"></i>
                </button>
            </div>
        </div>
        <div>
            <button type="btn" onclick="toggleDateSearch()" id="customSwitch8"
                class="btn btn-primary mr-1 waves-effect waves-light"><i class="feather icon-filter"></i>
            </button>
            <button onclick="toggleLogsSearch()" class="btn btn-primary waves-effect waves-light">
                <i class="feather icon-search"></i>
            </button>
        </div>
    </div>
    <div class="p-2">
        @admin
            <div class="row">
                <div class="col-12 text-right">
                    <p class="mr-0 h5">Paid Commission:<span class="text-success h4"> $
                            {{ number_format($balance->where('is_paid', true)->sum('value'), 2) }}</span></p>
                    <p class="mr-0 h5">UnPaid Commission:<span class="text-danger h4"> $
                            {{ number_format($balance->where('is_paid', false)->sum('value'), 2) }}</span></p>
                </div>
            </div>
        @endadmin
        <div class="row col-12 pr-0 m-0 pl-0" id="datefilters">
            <div class=" col-6 text-left mb-2 pl-0">
                <div class="row" id="dateSearch" style="display: none;">
                    <form class="col-12 pl-0" action="{{ route('admin.affiliate.sale.exports') }}" method="GET"
                        target="_blank">
                        @csrf
                        <div class="form-group mb-2 col-4" style="float:left;margin-right:20px;">
                            <label>Start Date</label>
                            <input type="date" name="start_date" class="form-control">
                        </div>
                        <div class="form-group mx-sm-3 mb-2 col-4" style="float:left;margin-right:20px;">
                            <label>End Date</label>
                            <input type="date" name="end_date" class="form-control">
                        </div>
                        <button class="btn btn-success searchDateBtn waves-effect waves-light"
                            title="@lang('sales-commission.Download Sales')">
                            <i class="fa fa-arrow-down" aria-hidden="true"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="mb-2 row col-md-12 hide "
            @if ($this->search) style="display: block !important;" @endif id="logSearch">
            <form class="col-12 d-flex pl-0" wire:submit.prevent="render">
                <div class="col-6 pl-0">
                    <label>Search</label>
                    <input type="search" class="form-control" wire:model.defer="search">
                </div>
                <div class="mt-1">
                    <button type="submit" class="btn btn-primary mt-4">
                        <i class="fa fa-search"></i>
                    </button>
                    <button class="btn btn-primary ml-1 mt-4 waves-effect waves-light"
                        onclick="window.location.reload();">
                        <i class="fa fa-undo" data-bs-toggle="tooltip" title=""
                            data-bs-original-title="fa fa-undo" aria-label="fa fa-undo" aria-hidden="true"></i></button>
                </div>
            </form>
        </div>

        <div class="table-wrapper position-relative">
            <table class="table mb-0 table-bordered table-responsive-md" id="">
                <thead>
                    <tr>
                        @admin
                            <th id="optionChkbx">
                                <div class="vs-checkbox-con vs-checkbox-primary" title="Select All">
                                    <input type="checkbox" id="checkAll" name="bulk-sales[]" class="check-all"
                                        value="">
                                    <span class="vs-checkbox vs-checkbox-sm">
                                        <span class="vs-checkbox--check">
                                            <i class="vs-icon feather icon-check"></i>
                                        </span>
                                    </span>
                                </div>
                            </th>
                        @endadmin
                        <th>@lang('sales-commission.Date')</th>
                        @admin
                            <th>@lang('sales-commission.User')</th>
                        @endadmin
                        <th>Commission From</th>
                        {{-- <th>@lang('sales-commission.Order ID')</th> --}}
                        <th>WHR#</th>
                        {{-- <th>Tracking Code</th> --}}
                        {{-- <th>Customer Reference</th>
                        <th>Carrier Tracking#</th>
                        <th>Weight</th> --}}
                        <th>@lang('sales-commission.Value')</th>
                        <th>@lang('sales-commission.Type')</th>
                        <th>@lang('sales-commission.Commission')</th>
                        <th>@lang('Is Paid')</th>
                        {{-- <th>@lang('status')</th> --}}
                        @admin
                            <th>@lang('Action')</th>
                        @endadmin
                    </tr>
                </thead>
                <tbody>
                    @forelse ($sales as $sale)
                        @include('admin.affiliate.components.sale-row', ['sale' => $sale])
                    @empty
                        <x-tables.no-record colspan="15"></x-tables.no-record>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="row pt-4">
            <div class="col-1 pt-5">
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

            <div class="col-11 d-flex justify-content-end pt-5">
                {{ $sales->links() }}
            </div>
        </div>
        @include('layouts.livewire.loading')
    </div>
</div>
