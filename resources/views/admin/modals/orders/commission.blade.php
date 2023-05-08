<div class="modal-header">
    <div class="col-8">
        <h4> @lang('sales-commission.Confirm Pay') </h4>
    </div>
</div>
<div class="modal-body" style="font-size: 15px;">
    @if ($totalOrder)
        <section class="card invoice-page">
        <table class="table  ">
            <thead>
                <tr>
                    <th>Name </th>
                    <th>Pobox number</th>
                    <th>Orders</th>
                    <th>Balance</th>
                </tr>
            </thead>
            <tbody> 
                @foreach ($userSales as $sale) 
                    <tr>
                        <td>{{  $sale->first()->user->name }}</td>
                        <td>{{  $sale->first()->user->pobox_number  }}</td>
                        <td>{{  number_format($sale->count() ) }}</td>
                        <td>{{  number_format($sale->sum('commission'),2)  }} USD</td>
                    </tr>
                @endforeach  
                <tr>
                    <td colspan="2"></td>
                    <td>{{ $totalOrder }}</td>
                    <td>{{ $totalCommission}} USD</td>
                </tr>
            </tbody>
        </table>
            @if(request('start') || request('end'))
                Period : <h4> {{ request('start') ? request('start').' to':'before'  }}   {{ request('end')??date('Y-m-d') }}</h4>
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
