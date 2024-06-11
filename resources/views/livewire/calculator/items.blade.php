<div class="ml-4">
    <h3 class="my-3 border-bottom-light py-2">@lang('orders.order-details.Order Items')</h3>
    <div class="row my-3">
        <div class="col-12">
            <button class="btn btn-success" type="button" role="button" wire:click="addItem">@lang('orders.order-details.Add Item')</button>
        </div>
    </div>
    @foreach ($items as $key => $item)
    <div class="items shadow p-4 border-top-success border-2 mt-2">
        <div class="row mt-1">
            <div class="form-group col-12 col-sm-4 col-md-4">
                <div class="controls">
                    <label>@lang('orders.order-details.order-item.Description') <span class="text-danger"></span></label>
                    <input type="text" class="form-control" required name="items[{{$key}}][description]" value="{{ optional($item)['description'] }}">
                    @error("items.{$key}.description")
                    <div class="help-block text-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="form-group col-12 col-sm-4 col-md-4">
                <div class="controls">
                    <label>@lang('orders.order-details.order-item.Quantity') <span class="text-danger"></span></label>
                    <input type="number" class="form-control quantity" step="0.01" onkeydown="if(event.key==='.'){event.preventDefault();}"  oninput="event.target.value = event.target.value.replace(/[^0-9]*/g,'');"  min="1" required name="items[{{$key}}][quantity]" value="{{ optional($item)['quantity'] }}">
                    @error("items.{$key}.quantity")
                        <div class="help-block text-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="form-group col-12 col-sm-4 col-md-4">
                <div class="controls">
                    <label>@lang('orders.order-details.order-item.Unit Value') <span class="text-danger"></span></label>
                    <input type="number" class="form-control value" step="0.01" min="0.01" required name="items[{{$key}}][value]" value="{{ optional($item)['value'] }}">
                    @error("items.{$key}.value")
                        <div class="help-block text-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
        <div class="row justify-content-end">
            <div class="col-4 text-right">
                <button class="btn btn-danger" type="button" wire:click="removeItem({{$key}})">@lang('orders.order-details.order-item.Remove')</button>
            </div>
        </div>
    </div>
    @endforeach
</div>
