<div>
    <h5 class="ml-5">Auto Charge Settings</h5>
    <div class="pl-3 pr-3 card-content"> 
        <div class="row col-12">
            <div class="col-3">
                <label>Auto charge Amount</label>
                <input type="number" wire:model.defer="charge_amount" min="0" class="form-control">
                @error('charge_amount')
                    <div class="text-danger">
                        {{ $message }}
                    </div>
                @enderror
            </div>
            <div class="col-3">
                <label>When Balance less than</label>
                <input type="number" wire:model.defer="charge_limit" min="0" class="form-control">
                @error('charge_limit')
                    <div class="text-danger">
                        {{ $message }}
                    </div>
                @enderror
            </div>
            <div class="col-3">
                <label> Billing information</label>
                <select class="form-control" wire:model.defer="charge_biling_information">
                    <option value="">Please Select</option>
                    @forelse (auth()->user()->billingInformations as $billingInfo)
                        <option value="{{ $billingInfo->id }}"
<<<<<<< HEAD
                            {{ setting('charge_biling_information', null, auth()->id()) ? 'selected' : '' }}>
=======
                            {{ (setting('charge_biling_information', null, auth()->id())) ? 'selected' : '' }}>
>>>>>>> b490d8d3d85c519c81011defbe5c4fc08027f29d
                            **** **** **** {{ substr($billingInfo->card_no, -4) }}</option>
                    @empty
                        <option value="">No Record Found / Nenhum Registro Encontrado</option>
                    @endforelse
                </select>
                @error('charge_biling_information')
                    <div class="text-danger">
                        {{ $message }}
                    </div>
                @enderror
            </div>
            <div class="col-3">
                <label>Auto debit authorization apply towards account balance</label><br>
                <input type="hidden" wire:model.defer="charge">
                <div class="btn-group btn-toggle" id="btn-toggle">
                    <label class="AutoChargeSwitch" wire:click.prevent="save">
                        <input type="checkbox" @if($charge) checked @endif >
                        <span class="AutoChargeSlider AutoChargeRound"></span>
                    </label>
                </div>
            </div>
        </div>
    </div>
    <hr>
    @include('layouts.livewire.loading')
</div>
<<<<<<< HEAD

<script>
    window.addEventListener('alert', event => { 
                 toastr[event.detail.type](event.detail.message, 
                 event.detail.title ?? ''), toastr.options = {
                        "closeButton": true,
                        "progressBar": true,
                    }
                });
    </script>
=======
>>>>>>> b490d8d3d85c519c81011defbe5c4fc08027f29d
