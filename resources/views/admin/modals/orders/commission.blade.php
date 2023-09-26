<div class="modal-header">
    <div class="col-8">
        <h4> @lang('sales-commission.Confirm Pay') </h4>
    </div>
</div>
<div class="modal-body" style="font-size: 15px;">
    @if ($totalOrder)
        <section class="card invoice-page">
        <table class="   ">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Pobox number</th>
                    <th>Referrals</th>
                    <th>Orders</th>
                    <th>Balance</th>
                </tr>
            </thead>
            <tbody> 
                @foreach ($userSales as $sale) 
                <tr> 
                    <td>{{  $sale['name'] }}</td>
                    <td>{{  $sale['pobox_number']  }}</td>
                    <td>
                        @foreach ($sale['referrer'] as $referrer)
                            <tr>
                                <td></td>
                                <td></td>
                                <td>
                                    {{ $referrer->first()->referrer->name }} 
                                </td>
                                <td>
                                    {{  $referrer->count() }} 
                                </td>
                                <td>
                                    {{ number_format($referrer->sum('commission'), 2) }} <br>
                                </td>
                            </tr>
                        @endforeach
                    </td>
                    <tr>
                        <td colspan="2"></td>
                        <td colspan="3">
                            <hr class="m-1">  
                        </td>   
                    </tr>
                    <tr>
                        <td colspan="2"></td>
                        <td><strong>Total</strong></td>
                        <td> <strong>  {{ $sale['orders'] }} </strong></td>
                        <td><strong> {{ $sale['commission'] }} USD </strong></td>
                    </tr>
                
                </tr>
                <tr>
                    <td colspan="5">
                        <hr class="m-1">  
                    </td>  
                </tr>
                @endforeach
                <tr>
                    <td colspan="2"></td>
                    <td><strong>Grand Total</strong></td>
                    <td><strong>{{ $totalOrder }} </strong></td>
                    <td> <strong>{{ $totalCommission}} USD </strong></td>
                </tr>
            </tbody>
        </table>
            @if(request('start') || request('end'))
                <h4 class="mt-2">Period :  <b>{{ request('start') ? request('start').' to':'before'  }}   {{ request('end')??date('Y-m-d') }}</b></h4>
            @endif 
        </section>
        @lang('sales-commission.Confirmation Message')
        <div class="modal-footer">
            <form action="{{ route('admin.affiliate.sales-commission.create') }}" method="GET" id="bulk_sale_form">
                <input name="data" type="hidden" value="{{ json_encode($sales->pluck('id')) }}">
                <button type="submit" class="btn btn-primary"> @lang('sales-commission.Proceed') </button>
            </form>  
            <button type="button" class="btn btn-secondary" data-dismiss="modal"> @lang('sales-commission.Decline') </button>
        </div> 
    @else
        <x-tables.no-record colspan="15"></x-tables.no-record>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal"> @lang('sales-commission.Decline') </button>
        </div> 
    @endif
</div>
