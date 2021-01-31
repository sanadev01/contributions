 <tr>
    <td>
        <input type="number" class="form-control" name="slab[{{$key}}][min_weight]" value="{{ $slab['min_weight'] }}">
        @error("slab.$key.min_weight")
        <div class="text-danger">
            {{ $message }}
        </div>
        @enderror
    </td>
    <td>
        <input type="number" class="form-control" name="slab[{{$key}}][max_weight]" value="{{ $slab['max_weight'] }}">
        @error("slab.$key.max_weight")
        <div class="text-danger">
            {{ $message }}
        </div>
        @enderror
    </td>

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

    <td>
        <button class="btn btn-danger" role="button" tabindex="-1" type="button" wire:click='removeSlab({{$key}})'>
            @lang('profitpackage.remove-slab')
        </button>
    </td>
</tr>
