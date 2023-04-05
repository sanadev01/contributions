<div class="modal-header">
    <div class="col-8">
        <h4>
            @lang('sales-commission.Confirm Pay')
        </h4>
    </div>
</div>
@if (request('orderIds'))
    <form action="{{ route('admin.affiliate.sales-commission.create') }}" method="GET" id="bulk_sale_form">
        <div class="modal-body" style="font-size: 15px;">
            <div class="modal-body">
                <section class="card invoice-page">
                    @if ($totalOrder)
                        <h4> <strong> @lang('sales-commission.Total Order') : </strong> {{ $totalOrder }}</h4>
                        <h4> <strong> @lang('sales-commission.Total Commission') : </strong>{{ $totalCommission }}</h4>
                        <h4> <strong> @lang('sales-commission.Users') : </strong></h4>
                        <ul>
                            @foreach ($groupByUser as $userSales)
                                <li>
                                    {{ $userSales[0]->user->name }}
                                </li>
                            @endforeach
                        </ul>
                        <p>
                            @lang('sales-commission.Confirmation Message')
                        </p>
                    @else
                        <x-tables.no-record colspan="15"></x-tables.no-record>
                    @endif
                </section>
            </div>
            <input type="hidden" name="command" id="command" value="">
            <input type="hidden" name="data" id="data" value="{{ request('orderIds') }}">
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary" id="save"> @lang('sales-commission.Proceed') </button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal"> @lang('sales-commission.Decline')
            </button>
        </div>
    </form>
@else
    <div class="modal-body" style="font-size: 15px;">
        <div class="modal-body">
            <section class="card invoice-page">
                @if ($totalOrder)
                    <h4> <strong> @lang('sales-commission.start date') :</strong> {{ request('start') }}</h1>
                        <h4> <strong>@lang('sales-commission.end date') :</strong> {{ request('end') }}</h1>
                            <h4> <strong> @lang('sales-commission.Total Order') : </strong> {{ $totalOrder }}</h4>
                            <h4> <strong> @lang('sales-commission.Total Commission') : </strong>{{ $totalCommission }}</h4>
                            <h4> <strong> @lang('sales-commission.Users') : </strong></h4>
                            <ul>
                                @foreach ($groupByUser as $userSales)
                                    <li>
                                        {{ $userSales[0]->user->name }}
                                    </li>
                                @endforeach
                            </ul>
                            <p>
                                @lang('sales-commission.Confirmation Message')
                            </p>
                        @else
                            <x-tables.no-record colspan="15"></x-tables.no-record>
                @endif
            </section>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary" onclick="payCommission()"> @lang('sales-commission.Proceed') </button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal"> @lang('sales-commission.Decline') </button>
    </div>
@endif
