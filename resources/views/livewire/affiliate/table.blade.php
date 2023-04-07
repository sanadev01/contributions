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
                    <p class="mr-0 h5">Paid Commission:<span class="text-success h4"> ${{ number_format($balance->where('is_paid', true)->sum('commission'), 2) }}</span></p>
                    <p class="mr-0 h5">UnPaid Commission:<span class="text-danger h4"> ${{ number_format($balance->where('is_paid', false)->sum('commission'), 2) }}</span></p>
                </div>
            </div>
        @endadmin

        <div class="mb-2 row col-md-12 " id="datefilters">
            <form action="{{ route('admin.affiliate.sale.exports') }}" method="GET" class="row col-12" id="dateSearch" style="display: none;">
                @csrf
                <div class="col-2 ml-0 pl-0">
                    <label class="pull-left">@lang('sales-commission.start date')</label>
                    <input type="date" name="start" class="form-control">
                </div>
                <div class="col-2">
                    <label class="pull-left">@lang('sales-commission.end date')</label>
                    <input type="date" name="end" class="form-control">
                </div>
                    @admin
                        <div class="col-2">
                            <label class="pull-left">@lang('parcel.User POBOX Number')</label>
                            <livewire:components.search-user />
                        </div>
                    @endadmin
                <input name="status" type="hidden" value="download">

                <div class="col-2 mt-4">
                    <button class="btn btn-success mt-1 pull-left" title="@lang('sales-commission.Download')">
                        @lang('sales-commission.Download') <i class="fa fa-arrow-down"></i>
                    </button>
                        @admin
                            <button class="btn btn-info mt-1 ml-2 pull-left d-none" title="@lang('sales-commission.Pay Commission')"
                                id="toPayCommission">
                                @lang('sales-commission.Pay Commission')
                            </button>
                        @endadmin
                </div>
                
            </form>
        </div>


        {{-- <div class="" >
            <div class=" col-12 text-left mb-2 pl-0">
                <div class="row" >
                    <form class="row" action="{{ route('admin.affiliate.sale.exports') }}" method="GET"  >
                        @csrf
                        <div class="col-lg-3"  >
                            <label>Start Date</label>
                            <input type="date" name="start" class="form-control">
                        </div>
                        <div class="col-lg-3"  ">
                            <label>End Date</label>
                            <input type="date" name="end" class="form-control">
                        </div>
                        <div class="col-lg-3"  >
                            <label  >@lang('parcel.User POBOX Number')</label>
                            <livewire:components.search-user />
                        </div>
                        <div class="col-lg-2">
                            <input name="status" type="hidden" value="download">                  
                            <button class="btn btn-success   waves-effect waves-light pull-left" title="@lang('sales-commission.Download')">
                                @lang('sales-commission.Download') <i class="fa fa-arrow-down" aria-hidden="true"></i>
                            </button> 
                        </div>
                        <div class="col-lg-2">  
                            <button class="btn btn-info   waves-effect waves-light d-none"  title="@lang('sales-commission.Pay Commission')" id="toPayCommission">   
                                @lang('sales-commission.Pay Commission')
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div> --}}
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
                    <tr >
                        @admin
                        <th style="max-width: 30px;"   >
                            <select name="" id="bulk-actions" class="form-control">
                                <option value="clear">Clear All</option>
                                <option value="checkAll">Select All</option>
                                <option value="pay-commission"> @lang('sales-commission.Pay Commission')</option>
                            </select>
                        </th>
                        @endadmin
                        <th>@lang('sales-commission.Date')</th>
                        @admin
                            <th>@lang('sales-commission.User')</th>
                        @endadmin
                        <th>Commission From</th>
                        <th>WHR#</th>
                        <th>@lang('sales-commission.Value')</th>
                        <th>@lang('sales-commission.Type')</th>
                        <th>@lang('sales-commission.Commission')</th>
                        <th>@lang('Is Paid')</th>
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
<div id="toPay" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        </div>
    </div>
</div>
@section('js')
    <script> 
        $('#toPayCommission').click(function(e) {
            e.preventDefault();
            start = $("input[name=start]").val()
            end = $("input[name=end]").val()
            user_id = $("input[name=user_id]").val()
            loadModal(null,start,end,user_id)
        }); 

        $('body').on('change', '#bulk-actions', function() {

            if ($(this).val() == 'clear') {
                $('.bulk-sales').prop('checked', false)
            } else if ($(this).val() == 'checkAll') {
                $('.bulk-sales').prop('checked', true)
            } else if ($(this).val() == 'pay-commission') {
                var orderIds = [];
                $.each($(".bulk-sales:checked"), function() {
                    orderIds.push($(this).val()); 
                });
                loadModal(JSON.stringify(orderIds)); 
            }
        });

        function loadModal(ids,start=null,end=null,user_id=null) {
            $.ajax({
                url: "{{ route('admin.modals.order.commissions') }}",
                type: 'GET',
                data: {
                    orderIds:ids,
                    start: start,
                    end: end,
                    user_id: user_id
                },
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(data) {
                    $('.modal-content').html(data);
                    $('#toPay').modal('show');

                },
            });
        }

        $("input").change(function() {
            togglePay()
            setTimeout(togglePay, 1000);
        }); 
        function togglePay() {
            if ($("input[name=start]").val() || $("input[name=end]").val() || $("input[name=user_id]").val()) {
                $("#toPayCommission").removeClass("d-none");
            } else {
                $("#toPayCommission").addClass("d-none");
            }
        }
        togglePay()
    </script>
@endsection
