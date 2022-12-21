<div>
    <table class="table mb-0">
        <thead>
            <tr>
                <th>Tracking Code</th>
                <th>WHR#</th>
                <th>Weight</th>
                <th>Volume Weight</th>
                <th>POBOX#</th>
                <th>Sender</th>
                <th>Customer Reference</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($orders as $key => $order)
            <tr id="{{ $key }}">
                <td>
                    {{ $order['corrios_tracking_code'] }}
                </td>
                <td>
                    {{ $order['warehouse_number'] }}
                </td>
                <td>
                    {{ $order['weight'] }} Kg
                </td>
                <td>
                    {{ $order['weight_lbs'] }} Lbs 
                        <hr>
                    {{ $order['weight_kg'] }} Kg
                </td>
                <td>
                    {{ $order['pobox'] }}
                </td>
                <td>
                    {{ $order['sender_name'] }}
                </td>
                <td>
                    {{ $order['customer_reference'] }}
                </td>
                <td>
                    @if ($editMode == true)
                        <button wire:click="removeOrder({{ $order['id'] }}, '{{$key}}')" class="btn btn-danger">
                            Remove
                        </button>
                    @endif
                </td>
            </tr>
            @endforeach
            <tr>
                <td colspan="8">
                    @if($error)
                    <div class="alert alert-danger" role="alert">
                        {{ $error }}
                    </div>
                    @endif
                </td>
            </tr>
            <tr>
                <td colspan="8" class="h2 text-right px-5">
                    <span class="text-danger font-weight-bold">Weight :</span> {{$totalweight}}
                    <span class="mx-3 text-danger font-weight-bold">Packages:</span> {{$num_of_Packages}}
                </td>
            </tr>
            @if($editMode == true)
            <tr>
                <td colspan="8">
                    <input type="text" wire:model.debounce.500ms="barcode" class="w-100 text-center" style="height:50px;font-size:30px;" id="scan">
                </td>
            </tr>
            @endif
        </tbody>
    </table>
</div>
