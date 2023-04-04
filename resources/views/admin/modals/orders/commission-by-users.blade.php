<div class="modal-header">
    <div class="col-8">
        <h4>
            @lang('commission.Confirm Pay')
        </h4>
    </div>

</div>
<div class="modal-body" style="font-size: 15px;">
    <div class="modal-body"> 
        <section class="card invoice-page"> 
            @if ($totalOrder) 
                @if ($start)
                    <h4> <strong> @lang('commission.End Date') :</strong> {{ $start }}</h1>
                @endif
                @if ($end)
                    <h4> <strong>@lang('commission.Start Date') :</strong> {{ $end }}</h1>
                @endif
                <h4> <strong> @lang('commission.Total Order') : </strong> {{ $totalOrder }}</h4>
                <h4> <strong> @lang('commission.Total Commission') : </strong>{{ $totalCommission }}</h4>
                <h4> <strong> @lang('commission.Users') : </strong></h4>
                <ul>
                    @foreach ($groupByUser as $userSales)
                        <li>
                            {{ $userSales[0]->user->name }}
                        </li>
                    @endforeach
                </ul>

                <p>

                    @lang('commission.Confirmation Message')
                </p>
            @else
                <x-tables.no-record colspan="15"></x-tables.no-record>
            @endif



        </section>

    </div>

</div>

<div class="modal-footer">

    <button type="submit" class="btn btn-primary" onclick="payCommission()"> @lang('commission.Proceed') </button>
    <button type="button" class="btn btn-secondary" data-dismiss="modal"> @lang('commission.Decline') </button>
</div>
</div>
