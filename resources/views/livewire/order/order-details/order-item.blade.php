<div class="items shadow p-4 border-top-success border-2 mt-2">
    <div class="row mt-1">
        <div class="form-group col-12 col-sm-6 col-md-6">
            <div class="controls">
                <label>@lang('orders.order-details.order-item.Harmonized Code')<span class="text-danger"></span></label>
                <livewire:components.search-sh-code class="form-control" required name="items[{{$keyId}}][sh_code]" :code="optional($item)['sh_code']" />
                @error("items.{$keyId}.sh_code")
                    <div class="help-block text-danger">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="form-group col-12 col-sm-6 col-md-6">
            <div class="controls">
                <label>@lang('orders.order-details.order-item.Description') <span class="text-danger"></span></label>
                <input type="text" class="form-control" required name="items[{{$keyId}}][description]" value="{{ optional($item)['description'] }}">
                @error("items.{$keyId}.description")
                    <div class="help-block text-danger">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
    <div class="row mt-1">
        <div class="form-group col-12 col-sm-4 col-md-4">
            <div class="controls">
                <label>@lang('orders.order-details.order-item.Quantity') <span class="text-danger"></span></label>
                <input type="number" class="form-control quantity" step="0.01" min="1" required name="items[{{$keyId}}][quantity]" value="{{ optional($item)['quantity'] }}">
                @error("items.{$keyId}.quantity")
                    <div class="help-block text-danger">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="form-group col-12 col-sm-4 col-md-4">
            <div class="controls">
                <label>@lang('orders.order-details.order-item.Unit Value') <span class="text-danger"></span></label>
                <input type="number" class="form-control value" step="0.01" min="0.01" required name="items[{{$keyId}}][value]" value="{{ optional($item)['value'] }}">
                @error("items.{$keyId}.value")
                    <div class="help-block text-danger">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="form-group col-12 col-sm-4 col-md-4">
            <div class="controls">
                <label>@lang('orders.order-details.order-item.Total') <span class="text-danger"></span></label>
                <input type="number" readonly class="form-control total" value="{{ optional($item)['quantity'] * optional($item)['value']  }}">
            </div>
        </div>
    </div>
    <div class="row mt-1">
        <div class="form-group col-12 col-sm-4 col-md-4">
            <div class="controls">
                <label class="d-flex">@lang('orders.order-details.order-item.Is Contains Dangrous Goods')  </label>
                <select name="items[{{$keyId}}][dangrous_item]" required class="form-control" id="">
                    <option value="0">No</option>
                    <option value="contains_battery" {{ optional($item)['contains_battery'] == 1 ? 'selected': '' }}>@lang('orders.order-details.order-item.Contains Battery')</option>
                    <option value="contains_perfume" {{ optional($item)['contains_perfume'] == 1 ? 'selected': '' }}>@lang('orders.order-details.order-item.Contains Perfume')</option>
                    {{-- <option value="contains_flammable_liquid" {{ optional($item)['contains_flammable_liquid'] == 1 ? 'selected': '' }}>@lang('orders.order-details.order-item.Contains Flammable Liquid')</option> --}}
                </select>
                @error("items.{$keyId}.dangrous_item")
                    <div class="help-block text-danger">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="row justify-content-end">
        <div class="col-4 text-right">
            <button class="btn btn-danger" type="button" role="button" wire:click="$emit('removeItem',{{$keyId}})">@lang('orders.order-details.order-item.Remove')</button>
        </div>
    </div>
</div>
