<section class="card invoice-page">

    @if ($totalOrder)

        @if ($start)
            <h4> <strong> Start date :</strong> {{ $start }}</h1>
        @endif
        @if ($end)
            <h4> <strong> End date :</strong> {{ $end }}</h1>
        @endif
        <h4> <strong> Total Order : </strong> {{ $totalOrder }}</h4>
        <h4> <strong> Total Commission : </strong>{{ $totalCommission }}</h4>
        <h4> <strong> Users </strong></h4>
        <ul>
            @foreach ($groupByUser as $userSales)
                <li>
                    {{ $userSales[0]->user->name }}
                </li>
            @endforeach
        </ul>
        
        <p>
            Are you Sure want to Pay the Commissions against these orders ?
        </p> 
    @else
        <x-tables.no-record colspan="15"></x-tables.no-record>
    @endif



</section>
