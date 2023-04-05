<div class="modal-header">
    <div class="col-8">
        <h4> @lang('sales-commission.Confirm Pay') </h4>
    </div>
</div>
<div class="modal-body" style="font-size: 15px;">
    @if ($totalOrder)
        <section class="card invoice-page">
            @if(request('start') || request('end'))
                <h4> <strong> @lang('sales-commission.start date') :</strong> {{ request('start') }}</h1>
                <h4> <strong>@lang('sales-commission.end date') :</strong> {{ request('end') }}</h1>
            @endif
            <h4> <strong> @lang('sales-commission.Total Order') : </strong> {{ $totalOrder }}</h4>
            <h4> <strong> @lang('sales-commission.Total Commission') : </strong>{{ $totalCommission }}</h4>
            <h4> <strong> @lang('sales-commission.Users') : </strong></h4>
            <ul>
                @foreach ($userNames as $name)
                    <li> {{ $name }} </li>
                @endforeach
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
