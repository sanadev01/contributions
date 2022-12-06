<div>
    <div class="p-2">
        @admin
            <div class="row">
                <div class="col-1">
                    <div id="printBtnDiv">
                        <button title="Print Labels" id="print" type="btn" onclick="allCheckedOrders()"
                            class="btn btn-primary mr-1 mb-1 waves-effect waves-light"><i class="fa fa-dollar"></i></button>
                    </div>
                </div>
                <div class="col-11 text-right mb-3">
                    <p class="mr-2 h5">UserName:<span class="text-success h4"> {{ $user->name }}</span></p>
                    <p class="mr-2 h5">POBOX Number:<span class="text-success h4"> {{ $user->pobox_number }}</span></p>
                    <p class="mr-2 h5">Paid Commission:<span class="text-success h4"> $
                            {{ number_format($user->affiliateSales()->where('is_paid', true)->sum('commission'),2) }}</span>
                    </p>
                    <p class="mr-2 h5">UnPaid Commission:<span class="text-danger h4"> $
                            {{ number_format($user->affiliateSales()->where('is_paid', false)->sum('commission'),2) }}</span>
                    </p>
                </div>
            </div>
        @endadmin
        <div class="col-12 text-right pr-2">
            <a href="{{ route('admin.reports.commission.index') }}" class="btn btn-primary">
                Back to list
            </a>
            <button type="btn" onclick="toggleOrderPageSearch()" id="orderSearch"
                class="btn btn-primary waves-effect waves-light"><i class="feather icon-search"></i></button>
            <button type="btn" onclick="toggleUserSearch()" id="customSwitch8"
                class="btn btn-primary  waves-effect waves-light"><i class="feather icon-filter"></i></button>
        </div>
        <div class="row mb-2 no-print">

            <div class="col-11">
                <form action="{{ route('admin.affiliate.sale.exports') }}" method="GET" target="_blank">
                    @csrf
                    <input type="hidden" name="user_id" value="{{ $user->id }}">

                    <div class="row mt-1" id="userSearch">
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
                        <button class="btn btn-success mb-4" title="@lang('sales-commission.Download Sales')">
                            @lang('sales-commission.Download Sales') <i class="fa fa-arrow-down"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <div class="mb-2 row col-md-12" @if ($this->search || $this->saleType || $this->start || $this->end) style="display: block !important" @endif
            id="singleSearch">
            <form class="col-12 d-flex pl-0" wire:submit.prevent="render">
                <div class="col-4 pl-0">
                    <label>Search</label>
                    <input type="search" class="form-control" wire:model.defer="search">
                </div>
                <div class="col-3 d-flex hd-mt-20 pl-1">
                    <input type="date" class="form-control col-md-6" wire:model.debounce.1000ms="start">
                    <input type="date" class="form-control col-md-6" wire:model.debounce.1000ms="end">
                </div>
                <div class="col-1 d-flex hd-mt-20 pl-1">
                    <select class="form-control" wire:model="saleType">
                        <option value="">All</option>
                        <option value="flat">Flat</option>
                        <option value="percentage">Percentage</option>
                    </select>
                </div>
                <div class="mt-1">
                    <button type="submit" class="btn btn-primary ml-1 mt-4 waves-effect waves-light">
                        <i class="fa fa-search" aria-hidden="true"></i>
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
                            <th>
                                <div class="vs-checkbox-con vs-checkbox-primary" title="Select All"
                                    style="width: 15px !important">
                                    <input type="checkbox" id="checkAll" name="orders[]" class="check-all"
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
                </thead>
                <tbody>
                    @forelse ($sales as $sale)
                        <tr>
                            @admin
                                <td class="optionChkbx">
                                    <div class="vs-checkbox-con vs-checkbox-primary" title="@lang('orders.Bulk Print')">
                                        <input type="checkbox" id="bulksales" name="sales[]" class="bulk-sales"
                                            value="{{ $sale->id }}">
                                        <span class="vs-checkbox vs-checkbox-sm">
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
                                <a href="#" data-toggle="modal" data-target="#hd-modal"
                                    data-url="{{ route('admin.modals.order.invoice', $sale->order) }}"
                                    title="@lang('sales-commission.Show Order Details')">
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
                                {{ $sale->commission ? number_format($sale->commission, 2) : 0 }}
                            </td>
                            <td>
                                @if ($sale->is_paid)
                                    <i class="feather icon-check text-success"></i>
                                @else
                                    <i class="feather icon-x text-danger"></i>
                                @endif
                            </td>

                            @admin
                                <td class="d-flex">
                                    <div class="btn-group">
                                        <div class="dropdown">
                                            <button type="button" class="btn btn-sm btn-success width-100 dropdown-toggle"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                @lang('parcel.Action')
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right dropright">
                                                @can('delete', $sale)
                                                    <form method="post"
                                                        action="{{ route('admin.affiliate.sales-commission.destroy', $sale) }}"
                                                        class="d-inline-block w-100" onsubmit="return confirmDelete()">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="dropdown-item w-100 text-danger"
                                                            title="@lang('parcel.Delete Parcel')">
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
        <div class="col-12 d-flex pl-0">
            <div class="col-1 pl-0">
                <select class="form-control mt-4" wire:model="pageSize">
                    <option value="1">1</option>
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="20">20</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="300">300</option>
                </select>
            </div>
            <div class="col-11 d-flex justify-content-end my-2 pb-4 mx-2 mt-4 pr-0">
                {{ $sales->links() }}
            </div>
        </div>
        @include('layouts.livewire.loading')
    </div>

</div>
@section('js')
    <script>
        function allCheckedOrders() {
            var orderIds = [];
            $.each($(".bulk-sales:checked"), function() {
                orderIds.push($(this).val());

            });
            $('#bulk_sale_form #command').val('pay-commission');
            $('#bulk_sale_form #data').val(JSON.stringify(orderIds));
            $('#confirm').modal('show');
        }
        $('body').on('change', '#checkAll', function() {

            if ($('#checkAll').is(':checked')) {
                $('.bulk-sales').prop('checked', true)
                document.getElementById("printBtnDiv").style.display = 'block';
            } else {
                $('.bulk-sales').prop('checked', false)
                document.getElementById("printBtnDiv").style.display = 'none';
            }

        })
        $('body').on('click', '#pay-commission', function() {
            var orderIds = [];
            $.each($(".bulk-sales:checked"), function() {
                orderIds.push($(this).val());

            });
            $('#bulk_sale_form #command').val('pay-commission');
            $('#bulk_sale_form #data').val(JSON.stringify(orderIds));
            $('#confirm').modal('show');
        })
        $('body').on('change', '#bulksales', function() {
            if ($('.bulk-sales').is(':checked')) {
                document.getElementById("printBtnDiv").style.display = 'block';
            } else {
                document.getElementById("printBtnDiv").style.display = 'none';
            }

            if ($(this).val() == 'clear') {
                $('.bulk-sales').prop('checked', false)
            } else if ($(this).val() == 'checkAll') {
                $('.bulk-sales').prop('checked', true)
            } else if ($(this).val() == 'pay-commission') {
                var orderIds = [];
                $.each($(".bulk-sales:checked"), function() {
                    orderIds.push($(this).val());
                    // $(".result").append('HD-' + this.value + ',');
                });

                $('#bulk_sale_form #command').val('pay-commission');
                $('#bulk_sale_form #data').val(JSON.stringify(orderIds));
                $('#confirm').modal('show');
                // $('#bulk_sale_form').submit();
            }
        })
    </script>
@endsection
