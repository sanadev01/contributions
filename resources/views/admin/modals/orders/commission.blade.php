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
                        <td>{{    $sale->first()->user->name }}</td>
                        <td>{{    $sale->first()->user->pobox_number  }}</td>
                        <td>{{   number_format($sale->count() ) }}</td>
                        <td>{{   number_format($sale->sum('commission'),2)  }}</td>
                    </tr>
                @endforeach  
                <tr>
                    <td colspan="2"></td>
                    <td>{{ $totalOrder }}</td>
                    <td>{{ $totalCommission}}</td>
                </tr>
            </tbody>
        </table>
            @if(request('start') || request('end'))
                <h4> <strong> @lang('sales-commission.start date') :</strong> {{ request('start') }}</h1>
                <h4> <strong>@lang('sales-commission.end date') :</strong> {{ request('end') }}</h1>
            @endif
            {{-- <h4> <strong> @lang('sales-commission.Total Order') : </strong> </h4> --}}
            <h4> <strong> @lang('sales-commission.Total Commission') : </strong>{{ $totalCommission }}</h4>
            <h4> <strong> Period : </strong></h4>
            <ul> 
                
                     
                    <li>  {{ $sales[0]->created_at->format('Y-m-d') }} - {{ $sales[$sales->count()-1]->created_at->format('Y-m-d')  }}     </li>
         
                    
            </ul>
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
