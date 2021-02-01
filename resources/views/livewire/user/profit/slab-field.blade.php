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
        
         <input type="text" value="{{ $slab['value'] }}" class="form-control rate" data-key="{{$key}}" id="profit_{{$key}}" name="slab[{{$key}}][value]">
         @error("slab.$key.value")
         <div class="text-danger">
             {{ $message }}
         </div>
         @enderror
     </td>
     <td>
         <input type="text" class="form-control shipping" name="shipping" value="{{$this->getSaleRate($package, $slab['max_weight'], false) }}" id="shipping_{{$key}}" data-key="{{$key}}">
     </td>
     <td>
         <input type="text" class="form-control selling" id="selling_{{$key}}"  value="{{ $sale }}"  data-key="{{$key}}">
     </td>

    <td>
        <button class="btn btn-danger" role="button" tabindex="-1" type="button" wire:click='removeSlab({{$key}})'>
            @lang('profitpackage.remove-slab')
        </button>
    </td>
</tr>
