<div>
    <table class="table table-bordered">
        <tr>
            {{-- <th>@lang('profitpackage.min-weight (grams)')</th> --}}
            <th>@lang('profitpackage.max-weight (grams)')</th>
            <th>Cost</th>
            <th>@lang('profitpackage.profit')</th>
            <th>@lang('profitpackage.selling')</th>
        </tr>
        @foreach ($slabs as $key => $slab)
            <tr>
                {{-- <td> --}}
                    <input type="hidden" class="form-control" name="slab[{{$key}}][min_weight]" value="{{ $slab['min_weight'] }}">
                    {{-- @error("slab.$key.min_weight")
                    <div class="text-danger">
                        {{ $message }}
                    </div>
                    @enderror
                </td> --}}
                <td>
                    <input type="number" class="form-control" name="slab[{{$key}}][max_weight]" value="{{ $slab['max_weight'] }}">
                    @error("slab.$key.max_weight")
                    <div class="text-danger">
                        {{ $message }}
                    </div>
                    @enderror
                </td>
                @php
                $weight = $slab['min_weight'];
                if($weight < 100 ){
                    $weight = 100;
                }
                if ($this->profitPackage) {
                    $cost = $this->getSaleRate($this->profitPackage, $weight, false);
                }else {
                    $cost = 0;
                }
                    
                @endphp
                <td>
                    <input type="text" class="form-control shipping" name="shipping" value="{{$slab['leve']}}" id="shipping_{{$key}}" data-key="{{$key}}">
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
                    <input type="text" class="form-control selling" id="selling_{{$key}}"  value="@if($slab['max_weight']){{ number_format(($cost * ($slab['value'] /100)) + $cost, 2) }}@endif"  data-key="{{$key}}">
                </td>
            
                <td>
                    <button class="btn btn-danger" role="button" tabindex="-1" type="button" wire:click='removeSlab({{$key}})'>
                        @lang('profitpackage.remove-slab')
                    </button>
                </td>
            </tr>
        
            {{-- <livewire:user.profit.slab-field :slab="$slab" :key="$key" :index="$key" :package="$profitPackage"/> --}}

        @endforeach
        <tr>
            <td colspan="2">
                <button class="btn btn-primary" role="button" type="button" wire:click='addSlab'>
                    @lang('profitpackage.add-slab')
                </button>
            </td>
        </tr>
    </table>
@include('layouts.livewire.loading')
</div>
