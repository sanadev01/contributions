<div>
    <table class="table table-bordered">
        <tr>
            <th>@lang('profitpackage.min-weight (grams)')</th>
            <th>@lang('profitpackage.max-weight (grams)')</th>
            <th>@lang('profitpackage.profit')</th>
            <th>@lang('profitpackage.selling')</th>
            <th></th>
        </tr>
        @foreach ($slabs as $key => $slab)
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
                    <input type="text" value="{{ $slab['value'] }}" class="form-control" name="slab[{{$key}}][value]">
                    @error("slab.$key.value")
                        <div class="text-danger">
                            {{ $message }}
                        </div>
                    @enderror
                </td>
                <td>
                    <input type="text" class="form-control" value="@if($slab['max_weight']) {{ $this->getSaleRate($profitPackage, $slab['max_weight']) }} @endif">
                    {{-- @error("slab.$key.rate")
                        <div class="text-danger">
                            {{ $message }}
                        </div>
                    @enderror --}}
                </td>
                <td>
                    <button class="btn btn-danger" role="button" tabindex="-1" type="button" wire:click='removeSlab({{$key}})'>
                        @lang('profitpackage.remove-slab')
                    </button>
                </td>
            </tr>
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