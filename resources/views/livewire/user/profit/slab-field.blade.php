<div>
        <td>
            {{-- <input type="text" class="form-control" wire:model.debounce.500ms="profit"> --}}
            <input type="text" value="{{ $slab['value'] }}" class="form-control" name="slab[{{$key}}][value]" wire:model.debounce.500ms="profit">
            @error("slab.$key.value")
                <div class="text-danger">
                    {{ $message }}
                </div>
            @enderror
        </td>
        <td>
            <input type="text" class="form-control" readonly >    
        </td>
       
    {{-- @if($slab['max_weight']) {{ $this->getSaleRate($profitPackage, $slab['max_weight']) }} @endif --}}
</div>
