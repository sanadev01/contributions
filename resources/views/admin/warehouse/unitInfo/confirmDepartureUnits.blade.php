<div class="row mt-2 mb-3" col-12>
    <div class="card-header">
        <h4 class="mb-0">Units with Confirmed Departure</h4>
    </div>
</div>
<div class="row col-12">
    <table class="table mb-0 table-bordered table-responsive-md">
        <thead>
            <tr>
                <th>S#</th>
                <th>Delivey Bill Code</th>
                <th>Flight No.</th>
                <th>Airline Code</th>
                <th>Departure Date</th>
                <th>Departure Aiport</th>
                <th>Arrival Date</th>
                <th>Arrival Aiport</th>
                <th>Destination Country</th>
                <th>Unit Code</th>
            </tr>
        </thead>
        <tbody>
           @forelse($unitInfo->dispatches as $unit)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $unit->deliveryBillCode }}</td>
                <td>{{ $unit->flightNumber }}</td>
                <td>{{ $unit->airlineCode }}</td>
                <td>{{ $unit->departureDate }}</td>
                <td>{{ $unit->departureAirportCode }}</td>
                <td>{{ $unit->arrivalDate }}</td>
                <td>{{ $unit->arrivalAirportCode }}</td>
                <td>{{ $unit->destinationCountryCode }}</td>
                <td>
                    @foreach($unit->unitList as $list)
                        {{ $list->unitCode }}, TrackingNos: {{ $list->trackingNumbers }}
                    @endforeach
                </td>
            </tr>
            @empty
                <tr colspan="10">No Record Found</tr>
            @endforelse
        </tbody>
    </table>
    <div class="d-flex justify-content-end my-2 pb-4 mx-2">
    </div>
    @include('layouts.livewire.loading')
</div>