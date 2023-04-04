<div class="p-2">
    @admin
        <div class="row">
            <div class="col-12 text-right mb-3">
                <p class="mr-2 h5">Paid Commission:<span class="text-success h4"> $
                        {{ number_format($balance->where('is_paid', true)->sum('value'), 2) }}</span></p>
                <p class="mr-2 h5">UnPaid Commission:<span class="text-danger h4"> $
                        {{ number_format($balance->where('is_paid', false)->sum('value'), 2) }}</span></p>
            </div>
        </div>
    @endadmin
    <div class="row col-12 mb-2 no-print">
        <div class="col-1 mt-4">
            <select class="form-control mt-1" wire:model="pageSize">
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
            <form action="{{ route('admin.affiliate.sale.exports') }}" id="affiliateSale" method="GET"
                class="row col-12">
                @csrf
                <div class="col-2">
                    <label class="pull-left">@lang('sales-commission.start date')</label>
                    <input type="date" name="start" class="form-control">
                </div>


                <div class="col-2">
                    <label class="pull-left">@lang('sales-commission.end date')</label>
                    <input type="date" name="end" class="form-control">
                </div>
                <div class="col-2">
                    <label class="pull-left">@lang('parcel.User POBOX Number')</label>
                    <livewire:components.search-user />
                </div>
                <input name="status" type="hidden">
                <div class="col-2">
                    <label class="pull-left">@lang('Type')</label>
                    <select type="text" id="choose_status" name="choose_status" class="form-control" required>
                        <option value="">Select option</option>
                        <option value="downlaod">Download Report</option>
                        @if (Auth::user()->isAdmin())
                            <option value="confirmToPay">Pay Commissions</option>
                        @endif
                    </select>
                </div>
                <div class="col-2 mt-4">
                    <button class="btn btn-success mt-1 pull-left" title="@lang('sales-commission.Download Pay')">
                        @lang('sales-commission.Download Pay') <i class="fa fa-arrow-down"></i>
                    </button>
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
                    @admin
                        <th>@lang('sales-commission.User')</th>
                    @endadmin
                    <th>Commission From</th>
                    <th>@lang('sales-commission.Order ID')</th>
                    <th>WHR#</th>
                    <th>Tracking Code</th>
                    <th>Customer Reference</th>
                    <th>Carrier Tracking#</th>
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
                        <input type="search" class="form-control" wire:model.debounce.1000ms="corrios_tracking">
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
                    <th>
                        <select class="form-control" wire:model="saleType">
                            <option value="">All</option>
                            <option value="flat">Flat</option>
                            <option value="percentage">Percentage</option>
                        </select>
                    </th>
                    <th>
                        <input type="search" class="form-control" wire:model.debounce.1000ms="commission">
                    </th>
                    <th>
                        <select class="form-control" wire:model="status">
                            <option value="">All</option>
                            <option value="unpaid">Unpaid</option>
                            <option value="paid">Paid</option>
                        </select>
                    </th>
                    <th></th>
                    @admin
                        <th></th>
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
    <div class="d-flex justify-content-end my-2 pb-4 mx-2">
        {{ $sales->links() }}
    </div>
    @include('layouts.livewire.loading')
</div>


<div  id="toPay"class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
       <div class="modal-content">
          <div class="modal-header"> 
                <div class="col-8">
                    <h4>
                       Confirm to Pay Commission
                    </h4>
                </div> 
             
          </div>
          <div class="modal-body" style="font-size: 15px;">
            <div class="modal-body">

            </div>

        </div>
 
          <div class="modal-footer"> 

            <button type="submit" class="btn btn-primary" id="payConfirmed"> Proceed</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">  Decline </button>
        </div>
       </div>
    </div>
 </div>


 <div class="modal fade" id="confirm" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
            <div class="col-8">
                <h4>
                   Confirm to Pay Commission
                </h4>
            </div>
        </div>
        <form action="{{ route('admin.affiliate.sales-commission.create') }}" method="GET" id="bulk_sale_form">
            <div class="modal-body" style="font-size: 15px;">
                <div class="modal-body-confirm">
 
                </div>  
                <input type="hidden" name="command" id="command" value="">
                <input type="hidden" name="data" id="data" value="">
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary" id="save"> Yes Pay</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal"> @lang('consolidation.Cancel')</button>
            </div>
        </form>
      </div>
    </div>
</div>

{{-- @include('comission-models') --}}

@section('js')

    <script>
        $(document).on("submit", "#affiliateSale", function(e) {
            
                if ($("select[name=choose_status]").val() == "confirmToPay") {
                    e.preventDefault();
                    start = $("input[name=start]").val()
                    end = $("input[name=end]").val()
                    user_id = $("input[name=user_id]").val() 


                    $.ajax({
                    url :"{{ route('admin.modals.order.commissions') }}",
                    type: 'GET',
                    data:{start:start,end:end,user_id:user_id},
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success:function(data){
                        $('.modal-body').html(data)
                        $('#toPay').modal('show'); 
                    },

                });


                }
        });

        $('#payConfirmed').click(function() {
            $("input[name=status]").val('toPay').change();
            $("select[name=choose_status]").val('toPay').change();
            $("#affiliateSale").submit();
        });

        $("#choose_status").on('change', function() {
            selected = $("select[name=choose_status]").val()
            if (selected) {
                let value = selected == "confirmToPay" || selected == "toPay" ? "toPay" : "download"
                $("input[name=status]").val(value).change();
            }
        });

 
        $('body').on('change','#bulk-actions',function(){

            if ( $(this).val() == 'clear' ){
                $('.bulk-sales').prop('checked',false)
            }else if ( $(this).val() == 'checkAll' ){
                $('.bulk-sales').prop('checked',true)
            }else if ( $(this).val() == 'pay-commission' ){
                var orderIds = [];
                $.each($(".bulk-sales:checked"), function(){
                    orderIds.push($(this).val());
                    
                    // $(".result").append('HD-' + this.value + ',');
                });
                
                $('#bulk_sale_form #command').val('pay-commission');
                $('#bulk_sale_form #data').val(JSON.stringify(orderIds));  
                    $.ajax({
                        url :"{{ route('admin.modals.order.commissions') }}",
                        type: 'GET',
                        data:{orderIds:JSON.stringify(orderIds)},
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success:function(data){
                            $('#confirm').modal('show');  
                            $('.modal-body-confirm').html(data)

                        }, 
                   }); 
                } 
            });
    </script>
@endsection