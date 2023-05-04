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
                    {{ $order->corrios_tracking_code }}
                </td>
                <td>
                    {{ $order->warehouse_number }}
                </td>
                <td>
                    {{ $order->getOriginalWeight('kg') }} Kg
                </td>
                <td>
                    {{ $order->getWeight('lbs') }} Lbs 
                        <hr>
                    {{ $order->getWeight('kg') }} Kg
                </td>
                <td>
                    {{ optional($order->user)->pobox_number.' / '. optional($order->user)->getFullName() }}
                </td>
                <td>
                    {{ $order->getSenderFullName() }}
                </td>
                <td>
                    {{ $order->customer_reference }}
                </td>
                <td>
                    @if ($editMode == true && $order->containers[0]->sequence == 1)
                        <button wire:click="removeOrder({{ $order->id }})" class="btn btn-danger">
                            Remove
                        </button>
                    @endif
                </td>
            </tr>
            @endforeach
            <tr>
                <td colspan="8">
                    @if($error)
                    <div class="alert {{ Session::get('alert-class', 'alert-danger') }}" role="alert">
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
                    <form wire:submit.prevent="submit">
                        <input type="text" wire:model.defer="tracking" class="w-100 text-center" style="height:50px;font-size:30px;" id="scan">
                        @error('tracking') <span class="error offset-5 h4 text-danger">{{ $message }}</span> @enderror
                    </form>
                </td>
            </tr>
            @endif
        </tbody>
    </table>
    @include('layouts.livewire.loading')
</div>
