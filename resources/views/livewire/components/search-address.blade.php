<div class="position-relative">
    <input type="search" autocomplete="off" class="form-control" name="phone" wire:model.debounce.500ms="search">
    
    @error('user_id')
        <div class="help-block text-danger">{{ $message }}</div>
    @enderror
    
    <div wire:loading style="position: absolute;right:5px;top:25%;">
        <i class="fa fa-spinner fa-spin"></i>
    </div>

    <div class="position-absolute bg-white w-100 mt-1" style="z-index: 100">
        @if ($addresses)
            <div class="d-flex w-100 shadow-lg flex-column">
                @foreach ($addresses as $address)
                    <div class="w-100 border-bottom-light p-2 cursor-pointer" wire:click="selectAddress({{$address}})">
                        <strong>Address:</strong> {{ $address['address'] }} <br>
                        <strong>Zipcode:</strong> {{ $address['zipcode'] }} <br>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
