<div class="row mt-2 mb-3" col-12>
    <div class="card-header">
        <h4 class="mb-0">Units Available for Return</h4>
    </div>
</div>
<div class="row col-12">
    <table class="table mb-0 table-bordered table-responsive-md">
        <thead>
            <tr>
                <th>S#</th>
                <th>Unit Code</th>
                <th>Weight</th>
                <th>Length</th>
                <th>Height</th>
                <th>Tracking Codes</th>
                <th>Create Date</th>
            </tr>
        </thead>
        <tbody>
           @forelse($unitInfo->returningUnits as $unit)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $unit->unitCode }}</td>
                <td>{{ $unit->weight }}</td>
                <td>{{ $unit->length }}</td>
                <td>{{ $unit->width }}</td>
                <td>{{ $unit->trackingNumbers }}</td>
                <td>{{ date('d-m-Y', strtotime($unit->creationDate)) }}</td>
            </tr>
            @empty
                <x-tables.no-record colspan="7"></x-tables.no-record>
            @endforelse
        </tbody>
    </table>
    <div class="d-flex justify-content-end my-2 pb-4 mx-2">
    </div>
    @include('layouts.livewire.loading')
</div>